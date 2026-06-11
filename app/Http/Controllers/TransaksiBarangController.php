<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Lokasi;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiBarang::with(['barang', 'user', 'lokasiAsal', 'lokasiTujuan']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('barang', fn($sq) =>
                $sq->where('nama_barang', 'like', "%$q%")
                   ->orWhere('kode_barang', 'like', "%$q%")
            );
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->latest('tanggal')->paginate(15)->withQueryString();
        $barang    = Barang::with('barangLokasi.lokasi')->orderBy('nama_barang')->get();
        $lokasi    = Lokasi::all();

        $masuk  = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'masuk')->count();
        $keluar = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'keluar')->count();
        $pindah = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'pindah')->count();

        return view('transaksi.index', compact('transaksi', 'barang', 'lokasi', 'masuk', 'keluar', 'pindah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'jenis'            => 'required|in:masuk,keluar,pindah',
            'jumlah'           => 'required|integer|min:1',
            'lokasi_asal_id'   => 'required_if:jenis,keluar,pindah|exists:lokasis,id',
            'lokasi_tujuan_id' => 'required_if:jenis,masuk,pindah|exists:lokasis,id',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($request->jenis === 'keluar' || $request->jenis === 'pindah') {
            $stok = BarangLokasi::where('barang_id', $barang->id)
                ->where('lokasi_id', $request->lokasi_asal_id)
                ->value('jumlah') ?? 0;

            if ($stok < $request->jumlah) {
                return back()->withErrors(['jumlah' => "Stok di lokasi asal tidak mencukupi. Tersedia: {$stok}"])->withInput();
            }
        }

        DB::transaction(function () use ($request, $barang) {
            if ($request->jenis === 'masuk') {
                $barang->increment('jumlah', $request->jumlah);
                $bl = BarangLokasi::firstOrNew([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $request->lokasi_tujuan_id,
                ]);
                $bl->jumlah += $request->jumlah;
                $bl->save();

            } elseif ($request->jenis === 'keluar') {
                $barang->decrement('jumlah', $request->jumlah);
                BarangLokasi::where('barang_id', $barang->id)
                    ->where('lokasi_id', $request->lokasi_asal_id)
                    ->decrement('jumlah', $request->jumlah);

            } elseif ($request->jenis === 'pindah') {
                BarangLokasi::where('barang_id', $barang->id)
                    ->where('lokasi_id', $request->lokasi_asal_id)
                    ->decrement('jumlah', $request->jumlah);

                $bl = BarangLokasi::firstOrNew([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $request->lokasi_tujuan_id,
                ]);
                $bl->jumlah += $request->jumlah;
                $bl->save();
            }

            TransaksiBarang::create([
                'barang_id'       => $request->barang_id,
                'user_id'         => Auth::id(),
                'jenis'           => $request->jenis,
                'jumlah'          => $request->jumlah,
                'lokasi_asal_id'  => $request->jenis !== 'masuk' ? $request->lokasi_asal_id : null,
                'lokasi_tujuan_id'=> $request->jenis !== 'keluar' ? $request->lokasi_tujuan_id : null,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat.']);
        }
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dicatat.');
    }

    public function destroy($id)
    {
        TransaksiBarang::findOrFail($id)->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
