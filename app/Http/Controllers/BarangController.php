<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Services\GoogleSheetsService;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'barangLokasi.lokasi']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($sq) use ($q) {
                $sq->where('nama_barang','like',"%$q%")
                   ->orWhere('kode_barang','like',"%$q%");
            });
        }
        if ($request->filled('kondisi') && $request->kondisi !== 'semua') {
            $query->where('kondisi', $request->kondisi);
        }

        $barang   = $query->latest()->paginate(15)->withQueryString();
        $kategori = Kategori::all();
        $lokasi   = Lokasi::all();

        return view('barang.index', compact('barang','kategori','lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'  => 'required|unique:barangs,kode_barang',
            'nama_barang'  => 'required',
            'kategori_id'  => 'required',
            'lokasi_awal_id' => 'required|exists:lokasis,id',
            'jumlah_awal'  => 'required|integer|min:1',
        ]);

        $data = $request->except('_token','foto','lokasi_awal_id','jumlah_awal');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang','public');
        }
        $data['jumlah'] = $request->jumlah_awal;

        $barang = Barang::create($data);

        BarangLokasi::create([
            'barang_id' => $barang->id,
            'lokasi_id' => $request->lokasi_awal_id,
            'jumlah'    => $request->jumlah_awal,
        ]);

        $barang->load(['kategori','barangLokasi.lokasi']);

        try {
            app(GoogleSheetsService::class)->appendBarang($barang);
        } catch (\Exception $e) {
            \Log::error('Sheets: '.$e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success'=>true,'message'=>'Barang berhasil ditambahkan.']);
        }
        return redirect()->route('barang.index')->with('success','Barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,'.$id,
            'nama_barang' => 'required',
        ]);

        $data = $request->except('_token','_method','foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang','public');
        }
        $barang->update($data);

        if ($request->ajax()) {
            return response()->json(['success'=>true,'message'=>'Barang berhasil diperbarui.']);
        }
        return redirect()->route('barang.index')->with('success','Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')->with('success','Barang berhasil dihapus.');
    }

    public function show($kode)
    {
        $barang = Barang::where('kode_barang',$kode)
            ->with(['kategori','barangLokasi.lokasi','transaksi.user','scanLogs.user'])
            ->firstOrFail();
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        return response()->json(Barang::with('barangLokasi')->findOrFail($id));
    }

    public function create()
    {
        return redirect()->route('barang.index');
    }

    public function downloadQR($kode)
    {
        $barang = Barang::where('kode_barang', $kode)->firstOrFail();
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg    = $writer->writeString(url('/scan/' . $barang->kode_barang));
        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="QR-' . $barang->kode_barang . '.svg"');
    }

    public function qrSvg($kode)
    {
        $barang = Barang::where('kode_barang', $kode)->firstOrFail();
        $renderer = new ImageRenderer(
            new RendererStyle(80),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg    = $writer->writeString(url('/scan/' . $barang->kode_barang));
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
