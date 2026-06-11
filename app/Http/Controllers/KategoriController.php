<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('barang')->get();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|unique:kategoris,nama_kategori']);
        Kategori::create($request->only('nama_kategori','deskripsi'));
        if ($request->ajax()) return response()->json(['success'=>true]);
        return redirect()->route('kategori.index')->with('success','Kategori ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate(['nama_kategori' => 'required|unique:kategoris,nama_kategori,'.$kategori->id]);
        $kategori->update($request->only('nama_kategori','deskripsi'));
        if ($request->ajax()) return response()->json(['success'=>true]);
        return redirect()->route('kategori.index')->with('success','Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success','Kategori dihapus.');
    }
}
