<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangItem;
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

        $transaksi = $query->latest('tanggal')->paginate(15)->withQueryString();
        $barang    = Barang::with('barangLokasi.lokasi')->orderBy('nama_barang')->get();
        $lokasi    = Lokasi::all();

        $masuk  = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'masuk')->count();
        $keluar = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'keluar')->count();
        $pindah = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'pindah')->count();

        $barangStok = $barang->mapWithKeys(function ($b) {
            return [$b->id => $b->barangLokasi->mapWithKeys(function ($bl) {
                return [$bl->lokasi_id => $bl->jumlah];
            })];
        });

        $barangKondisi = $barang->mapWithKeys(function ($b) {
            return [$b->id => BarangItem::where('barang_id', $b->id)
                ->where('status', 'aktif')
                ->get()
                ->groupBy('lokasi_id')
                ->map(function ($items, $lokasiId) {
                    return $items->groupBy('kondisi')->map->count();
                })];
        });

        $barangItems = BarangItem::where('status', 'aktif')
            ->with('lokasi')
            ->get()
            ->groupBy('barang_id')
            ->map(function ($items) {
                return $items->groupBy('lokasi_id')->map(function ($lokasiItems) {
                    return $lokasiItems->map(function ($item) {
                        return ['id' => $item->id, 'kondisi' => $item->kondisi, 'lokasi' => $item->lokasi?->nama_lokasi ?? '-'];
                    })->values();
                });
            });

        $lokasiList = $lokasi->map(fn($l) => ['id' => $l->id, 'nama' => $l->nama_lokasi]);

        return view('transaksi.index', compact('transaksi', 'barang', 'lokasi', 'masuk', 'keluar', 'pindah', 'barangStok', 'lokasiList', 'barangKondisi', 'barangItems'));
    }

    public function store(Request $request)
    {
        $rules = [
            'barang_id'     => 'required|exists:barangs,id',
            'jenis'         => 'required|in:masuk,keluar,pindah,update_kondisi',
            'keterangan'    => 'nullable|string',
            'lokasi_asal_id'   => 'nullable|required_if:jenis,keluar,pindah,update_kondisi|exists:lokasis,id',
            'lokasi_tujuan_id' => 'nullable|required_if:jenis,masuk,pindah|exists:lokasis,id',
        ];

        if ($request->jenis === 'masuk') {
            $rules['sumber']     = 'required|in:pembelian,donasi,hibah,bantuan,inventaris_lama';
            $rules['kondisi']    = 'required|array|min:1';
            $rules['kondisi.*']  = 'required|in:baik,rusak,hilang';
            $rules['jumlah']     = 'required|array|min:1';
            $rules['jumlah.*']   = 'required|integer|min:1';
        } else {
            $rules['item_ids']    = 'required|array|min:1';
            $rules['item_ids.*']  = 'required|exists:barang_items,id';
            $rules['item_kondisi'] = 'nullable|array';
        }

        $request->validate($rules);

        $barang = Barang::findOrFail($request->barang_id);

        // For non-masuk: verify items belong to barang + location + are active
        if ($request->jenis !== 'masuk') {
            $validItemIds = BarangItem::whereIn('id', $request->item_ids)
                ->where('barang_id', $barang->id)
                ->where('lokasi_id', $request->lokasi_asal_id)
                ->where('status', 'aktif')
                ->pluck('id')
                ->toArray();

            $invalidIds = array_diff($request->item_ids, $validItemIds);
            if (!empty($invalidIds)) {
                return back()->withErrors([
                    'item_ids' => 'Beberapa item tidak valid atau tidak tersedia di lokasi ini.'
                ])->withInput();
            }
        }

        $totalJumlah = $request->jenis === 'masuk' ? array_sum($request->jumlah) : count($request->item_ids);

        DB::transaction(function () use ($request, $barang, $totalJumlah) {
            $trx = TransaksiBarang::create([
                'barang_id'       => $request->barang_id,
                'user_id'         => Auth::id(),
                'jenis'           => $request->jenis,
                'jumlah'          => $totalJumlah,
                'lokasi_asal_id'  => $request->jenis !== 'masuk' ? $request->lokasi_asal_id : null,
                'lokasi_tujuan_id'=> in_array($request->jenis, ['masuk', 'pindah']) ? $request->lokasi_tujuan_id : null,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);

            if ($request->jenis === 'masuk') {
                foreach ($request->kondisi as $i => $kondisi) {
                    $jml = $request->jumlah[$i];
                    for ($u = 0; $u < $jml; $u++) {
                        $item = BarangItem::create([
                            'barang_id'    => $barang->id,
                            'lokasi_id'    => $request->lokasi_tujuan_id,
                            'kondisi'      => $kondisi,
                            'sumber'       => $request->sumber,
                            'tanggal_masuk'=> now(),
                            'status'       => 'aktif',
                        ]);
                        $trx->barangItems()->attach($item->id, ['jenis' => 'masuk']);
                    }
                }

                $barang->increment('jumlah', $totalJumlah);
                $bl = BarangLokasi::firstOrNew([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $request->lokasi_tujuan_id,
                ]);
                $bl->jumlah += $totalJumlah;
                $bl->save();

            } else {
                $itemKondisi = $request->item_kondisi ?? [];

                foreach ($request->item_ids as $itemId) {
                    // Update kondisi if provided
                    $kondisiBaru = $itemKondisi[$itemId] ?? null;

                    if ($request->jenis === 'keluar') {
                        $updateData = ['status' => 'keluar'];
                        if ($kondisiBaru) $updateData['kondisi'] = $kondisiBaru;
                        BarangItem::where('id', $itemId)->update($updateData);
                        $trx->barangItems()->attach($itemId, ['jenis' => 'keluar']);

                    } elseif ($request->jenis === 'pindah') {
                        $updateData = ['lokasi_id' => $request->lokasi_tujuan_id];
                        if ($kondisiBaru) $updateData['kondisi'] = $kondisiBaru;
                        BarangItem::where('id', $itemId)->update($updateData);
                        $trx->barangItems()->attach($itemId, ['jenis' => 'pindah']);

                    } elseif ($request->jenis === 'update_kondisi') {
                        if ($kondisiBaru) {
                            BarangItem::where('id', $itemId)->update(['kondisi' => $kondisiBaru]);
                            $trx->barangItems()->attach($itemId, ['jenis' => 'update_kondisi']);
                        }
                    }
                }

                // Update cached counts
                if (in_array($request->jenis, ['keluar', 'pindah'])) {
                    $barang->decrement('jumlah', $totalJumlah);
                    BarangLokasi::where('barang_id', $barang->id)
                        ->where('lokasi_id', $request->lokasi_asal_id)
                        ->decrement('jumlah', $totalJumlah);
                }

                if ($request->jenis === 'pindah') {
                    $bl = BarangLokasi::firstOrNew([
                        'barang_id' => $barang->id,
                        'lokasi_id' => $request->lokasi_tujuan_id,
                    ]);
                    $bl->jumlah += $totalJumlah;
                    $bl->save();
                }
            }
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
