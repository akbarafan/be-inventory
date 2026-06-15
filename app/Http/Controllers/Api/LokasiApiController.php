<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangItem;
use App\Models\Lokasi;
use Illuminate\Http\JsonResponse;

class LokasiApiController extends Controller
{
    public function index(): JsonResponse
    {
        $lokasi = Lokasi::withCount(['barangLokasi as total_stok' => fn($q) =>
            $q->selectRaw('COALESCE(SUM(jumlah), 0)')
        ])->get();

        return response()->json([
            'success' => true,
            'data'    => $lokasi,
        ]);
    }

    public function stock($id): JsonResponse
    {
        $lokasi = Lokasi::findOrFail($id);

        $barangDiLokasi = $lokasi->barangLokasi()
            ->with('barang.kategori')
            ->get()
            ->map(fn($bl) => [
                'barang_id'   => $bl->barang_id,
                'kode_barang' => $bl->barang?->kode_barang ?? '-',
                'nama_barang' => $bl->barang?->nama_barang ?? '-',
                'total'       => (int) $bl->jumlah,
                'per_kondisi' => [
                    'baik'  => (int) BarangItem::where('barang_id', $bl->barang_id)
                        ->where('lokasi_id', $id)->where('kondisi', 'baik')->where('status', 'aktif')->count(),
                    'rusak' => (int) BarangItem::where('barang_id', $bl->barang_id)
                        ->where('lokasi_id', $id)->where('kondisi', 'rusak')->where('status', 'aktif')->count(),
                    'hilang'=> (int) BarangItem::where('barang_id', $bl->barang_id)
                        ->where('lokasi_id', $id)->where('kondisi', 'hilang')->where('status', 'aktif')->count(),
                ],
            ]);

        return response()->json([
            'success' => true,
            'data'    => $barangDiLokasi,
        ]);
    }
}
