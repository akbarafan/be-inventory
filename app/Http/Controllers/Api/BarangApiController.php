<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarangApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Barang::with(['kategori', 'barangLokasi.lokasi'])
            ->withCount([
                'barangItems as baik_count'  => fn($q) => $q->where('kondisi', 'baik')->where('status', 'aktif'),
                'barangItems as rusak_count' => fn($q) => $q->where('kondisi', 'rusak')->where('status', 'aktif'),
                'barangItems as hilang_count'=> fn($q) => $q->where('kondisi', 'hilang')->where('status', 'aktif'),
            ]);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_barang', 'like', "%$q%")
                   ->orWhere('kode_barang', 'like', "%$q%");
            });
        }

        if ($request->filled('kondisi')) {
            $query->whereHas('barangItems', fn($q) =>
                $q->where('kondisi', $request->kondisi)->where('status', 'aktif')
            );
        }

        $barang = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $barang->items(),
            'meta'    => [
                'current_page' => $barang->currentPage(),
                'last_page'    => $barang->lastPage(),
                'per_page'     => $barang->perPage(),
                'total'        => $barang->total(),
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $barang = Barang::with(['kategori', 'barangLokasi.lokasi'])
            ->withCount([
                'barangItems as baik_count'  => fn($q) => $q->where('kondisi', 'baik')->where('status', 'aktif'),
                'barangItems as rusak_count' => fn($q) => $q->where('kondisi', 'rusak')->where('status', 'aktif'),
                'barangItems as hilang_count'=> fn($q) => $q->where('kondisi', 'hilang')->where('status', 'aktif'),
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $barang,
        ]);
    }

    public function items($id): JsonResponse
    {
        $barang = Barang::findOrFail($id);

        $items = BarangItem::where('barang_id', $barang->id)
            ->with('lokasi')
            ->orderBy('kondisi')
            ->get()
            ->map(fn($item) => [
                'id'            => $item->id,
                'kondisi'       => $item->kondisi,
                'lokasi'        => $item->lokasi?->nama_lokasi ?? '-',
                'sumber'        => ucfirst(str_replace('_', ' ', $item->sumber ?? '')),
                'tanggal_masuk' => $item->tanggal_masuk?->format('d M Y'),
                'status'        => $item->status,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    public function stock($id): JsonResponse
    {
        $barang = Barang::findOrFail($id);

        $perLokasi = $barang->barangLokasi()
            ->with('lokasi')
            ->get()
            ->map(fn($bl) => [
                'lokasi_id'   => $bl->lokasi_id,
                'lokasi'      => $bl->lokasi?->nama_lokasi ?? '-',
                'total'       => (int) $bl->jumlah,
                'per_kondisi' => [
                    'baik'  => (int) BarangItem::where('barang_id', $barang->id)
                        ->where('lokasi_id', $bl->lokasi_id)
                        ->where('kondisi', 'baik')
                        ->where('status', 'aktif')->count(),
                    'rusak' => (int) BarangItem::where('barang_id', $barang->id)
                        ->where('lokasi_id', $bl->lokasi_id)
                        ->where('kondisi', 'rusak')
                        ->where('status', 'aktif')->count(),
                    'hilang'=> (int) BarangItem::where('barang_id', $barang->id)
                        ->where('lokasi_id', $bl->lokasi_id)
                        ->where('kondisi', 'hilang')
                        ->where('status', 'aktif')->count(),
                ],
            ]);

        return response()->json([
            'success' => true,
            'data'    => $perLokasi,
        ]);
    }

    public function transaksi($id, Request $request): JsonResponse
    {
        $barang = Barang::findOrFail($id);

        $query = $barang->transaksi()->with(['user', 'lokasiAsal', 'lokasiTujuan', 'barangItems']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $transaksi = $query->latest('tanggal')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $transaksi->items(),
            'meta'    => [
                'current_page' => $transaksi->currentPage(),
                'last_page'    => $transaksi->lastPage(),
                'per_page'     => $transaksi->perPage(),
                'total'        => $transaksi->total(),
            ],
        ]);
    }
}
