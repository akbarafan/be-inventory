<?php
namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::withCount('barangLokasi')->with('barangLokasi.barang')->get();

        $lokasiData = $lokasi->mapWithKeys(function ($l) {
            return [$l->id => [
                'nama' => $l->nama_lokasi,
                'barang' => $l->barangLokasi->map(function ($bl) {
                    return [
                        'kode'   => $bl->barang?->kode_barang,
                        'nama'   => $bl->barang?->nama_barang,
                        'jumlah' => $bl->jumlah,
                    ];
                })->sortByDesc('jumlah')->values(),
            ]];
        });

        return view('lokasi.index', compact('lokasi', 'lokasiData'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_lokasi' => 'required|unique:lokasis,nama_lokasi']);
        Lokasi::create($request->only('nama_lokasi','deskripsi'));
        if ($request->ajax()) return response()->json(['success'=>true]);
        return redirect()->route('lokasi.index')->with('success','Lokasi ditambahkan.');
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $request->validate(['nama_lokasi' => 'required|unique:lokasis,nama_lokasi,'.$lokasi->id]);
        $lokasi->update($request->only('nama_lokasi','deskripsi'));
        if ($request->ajax()) return response()->json(['success'=>true]);
        return redirect()->route('lokasi.index')->with('success','Lokasi diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        $lokasi->delete();
        return redirect()->route('lokasi.index')->with('success','Lokasi dihapus.');
    }
}
