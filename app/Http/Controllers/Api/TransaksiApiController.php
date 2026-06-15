<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangItem;
use App\Models\BarangLokasi;
use App\Models\TransaksiBarang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiApiController extends Controller
{
    private function validateAndGetBarang(Request $request): Barang
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
        ]);
        return Barang::findOrFail($request->barang_id);
    }

    private function validateItemsAtLocation(array $itemIds, int $barangId, int $lokasiId): array
    {
        $validIds = BarangItem::whereIn('id', $itemIds)
            ->where('barang_id', $barangId)
            ->where('lokasi_id', $lokasiId)
            ->where('status', 'aktif')
            ->pluck('id')
            ->toArray();

        $invalid = array_diff($itemIds, $validIds);
        if (!empty($invalid)) {
            throw ValidationException::withMessages([
                'item_ids' => ['Beberapa item tidak valid atau tidak tersedia di lokasi ini.'],
            ]);
        }

        return $validIds;
    }

    // ─── POST /api/transaksi/masuk ───
    public function masuk(Request $request): JsonResponse
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'lokasi_tujuan_id' => 'required|exists:lokasis,id',
            'sumber'           => 'required|in:pembelian,donasi,hibah,bantuan,inventaris_lama',
            'kondisi'          => 'required|array|min:1',
            'kondisi.*'        => 'required|in:baik,rusak,hilang',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'required|integer|min:1',
            'keterangan'       => 'nullable|string',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $totalJumlah = array_sum($request->jumlah);

        DB::transaction(function () use ($request, $barang, $totalJumlah) {
            $trx = TransaksiBarang::create([
                'barang_id'       => $barang->id,
                'user_id'         => Auth::id(),
                'jenis'           => 'masuk',
                'jumlah'          => $totalJumlah,
                'lokasi_asal_id'  => null,
                'lokasi_tujuan_id'=> $request->lokasi_tujuan_id,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);

            foreach ($request->kondisi as $i => $kondisi) {
                for ($u = 0; $u < $request->jumlah[$i]; $u++) {
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
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi masuk berhasil dicatat.',
        ]);
    }

    // ─── POST /api/transaksi/keluar ───
    public function keluar(Request $request): JsonResponse
    {
        $request->validate([
            'barang_id'      => 'required|exists:barangs,id',
            'lokasi_asal_id' => 'required|exists:lokasis,id',
            'item_ids'       => 'required|array|min:1',
            'item_ids.*'     => 'required|exists:barang_items,id',
            'item_kondisi'   => 'nullable|array',
            'keterangan'     => 'nullable|string',
        ]);

        $barang = $this->validateAndGetBarang($request);
        $validIds = $this->validateItemsAtLocation($request->item_ids, $barang->id, $request->lokasi_asal_id);
        $totalJumlah = count($validIds);

        DB::transaction(function () use ($request, $barang, $totalJumlah, $validIds) {
            $trx = TransaksiBarang::create([
                'barang_id'       => $barang->id,
                'user_id'         => Auth::id(),
                'jenis'           => 'keluar',
                'jumlah'          => $totalJumlah,
                'lokasi_asal_id'  => $request->lokasi_asal_id,
                'lokasi_tujuan_id'=> null,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);

            $itemKondisi = $request->item_kondisi ?? [];
            foreach ($validIds as $itemId) {
                $updateData = ['status' => 'keluar'];
                if (!empty($itemKondisi[$itemId])) {
                    $updateData['kondisi'] = $itemKondisi[$itemId];
                }
                BarangItem::where('id', $itemId)->update($updateData);
                $trx->barangItems()->attach($itemId, ['jenis' => 'keluar']);
            }

            $barang->decrement('jumlah', $totalJumlah);
            BarangLokasi::where('barang_id', $barang->id)
                ->where('lokasi_id', $request->lokasi_asal_id)
                ->decrement('jumlah', $totalJumlah);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi keluar berhasil dicatat.',
        ]);
    }

    // ─── POST /api/transaksi/pindah ───
    public function pindah(Request $request): JsonResponse
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'lokasi_asal_id'   => 'required|exists:lokasis,id',
            'lokasi_tujuan_id' => 'required|exists:lokasis,id',
            'item_ids'         => 'required|array|min:1',
            'item_ids.*'       => 'required|exists:barang_items,id',
            'item_kondisi'     => 'nullable|array',
            'keterangan'       => 'nullable|string',
        ]);

        $barang = $this->validateAndGetBarang($request);
        $validIds = $this->validateItemsAtLocation($request->item_ids, $barang->id, $request->lokasi_asal_id);
        $totalJumlah = count($validIds);

        DB::transaction(function () use ($request, $barang, $totalJumlah, $validIds) {
            $trx = TransaksiBarang::create([
                'barang_id'       => $barang->id,
                'user_id'         => Auth::id(),
                'jenis'           => 'pindah',
                'jumlah'          => $totalJumlah,
                'lokasi_asal_id'  => $request->lokasi_asal_id,
                'lokasi_tujuan_id'=> $request->lokasi_tujuan_id,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);

            $itemKondisi = $request->item_kondisi ?? [];
            foreach ($validIds as $itemId) {
                $updateData = ['lokasi_id' => $request->lokasi_tujuan_id];
                if (!empty($itemKondisi[$itemId])) {
                    $updateData['kondisi'] = $itemKondisi[$itemId];
                }
                BarangItem::where('id', $itemId)->update($updateData);
                $trx->barangItems()->attach($itemId, ['jenis' => 'pindah']);
            }

            BarangLokasi::where('barang_id', $barang->id)
                ->where('lokasi_id', $request->lokasi_asal_id)
                ->decrement('jumlah', $totalJumlah);
            $bl = BarangLokasi::firstOrNew([
                'barang_id' => $barang->id,
                'lokasi_id' => $request->lokasi_tujuan_id,
            ]);
            $bl->jumlah += $totalJumlah;
            $bl->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi pindah berhasil dicatat.',
        ]);
    }

    // ─── POST /api/transaksi/update-kondisi ───
    public function updateKondisi(Request $request): JsonResponse
    {
        $request->validate([
            'barang_id'      => 'required|exists:barangs,id',
            'lokasi_asal_id' => 'required|exists:lokasis,id',
            'item_ids'       => 'required|array|min:1',
            'item_ids.*'     => 'required|exists:barang_items,id',
            'item_kondisi'   => 'required|array',
            'keterangan'     => 'nullable|string',
        ]);

        $barang = $this->validateAndGetBarang($request);
        $validIds = $this->validateItemsAtLocation($request->item_ids, $barang->id, $request->lokasi_asal_id);

        DB::transaction(function () use ($request, $barang, $validIds) {
            $trx = TransaksiBarang::create([
                'barang_id'       => $barang->id,
                'user_id'         => Auth::id(),
                'jenis'           => 'update_kondisi',
                'jumlah'          => count($validIds),
                'lokasi_asal_id'  => $request->lokasi_asal_id,
                'lokasi_tujuan_id'=> null,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);

            $itemKondisi = $request->item_kondisi;
            foreach ($validIds as $itemId) {
                if (!empty($itemKondisi[$itemId])) {
                    BarangItem::where('id', $itemId)->update(['kondisi' => $itemKondisi[$itemId]]);
                    $trx->barangItems()->attach($itemId, ['jenis' => 'update_kondisi']);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Update kondisi berhasil dicatat.',
        ]);
    }

    // ─── GET /api/transaksi ───
    public function index(Request $request): JsonResponse
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

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
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

    // ─── GET /api/transaksi/{id} ───
    public function show($id): JsonResponse
    {
        $transaksi = TransaksiBarang::with([
            'barang', 'user', 'lokasiAsal', 'lokasiTujuan',
            'barangItems' => fn($q) => $q->with('lokasi'),
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $transaksi,
        ]);
    }

    // ─── DELETE /api/transaksi/{id} ───
    public function destroy($id): JsonResponse
    {
        $transaksi = TransaksiBarang::findOrFail($id);
        $transaksi->barangItems()->detach();
        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus.',
        ]);
    }

    // ─── GET /api/transaksi/stats/hari-ini ───
    public function statsHariIni(): JsonResponse
    {
        $today = now()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'data'    => [
                'masuk'  => TransaksiBarang::whereDate('tanggal', $today)->where('jenis', 'masuk')->count(),
                'keluar' => TransaksiBarang::whereDate('tanggal', $today)->where('jenis', 'keluar')->count(),
                'pindah' => TransaksiBarang::whereDate('tanggal', $today)->where('jenis', 'pindah')->count(),
                'update_kondisi' => TransaksiBarang::whereDate('tanggal', $today)->where('jenis', 'update_kondisi')->count(),
            ],
        ]);
    }

    // ─── GET /api/transaksi/stats/bulan-ini ───
    public function statsBulanIni(): JsonResponse
    {
        $now = now();
        $startOfMonth = $now->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->endOfMonth()->format('Y-m-d');

        $transaksi = TransaksiBarang::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw("jenis, COUNT(*) as total, SUM(jumlah) as total_item")
            ->groupBy('jenis')
            ->get()
            ->keyBy('jenis');

        return response()->json([
            'success' => true,
            'data'    => [
                'bulan' => $now->format('F Y'),
                'masuk'  => [
                    'transaksi' => (int) ($transaksi['masuk']->total ?? 0),
                    'item'      => (int) ($transaksi['masuk']->total_item ?? 0),
                ],
                'keluar' => [
                    'transaksi' => (int) ($transaksi['keluar']->total ?? 0),
                    'item'      => (int) ($transaksi['keluar']->total_item ?? 0),
                ],
                'pindah' => [
                    'transaksi' => (int) ($transaksi['pindah']->total ?? 0),
                    'item'      => (int) ($transaksi['pindah']->total_item ?? 0),
                ],
                'update_kondisi' => [
                    'transaksi' => (int) ($transaksi['update_kondisi']->total ?? 0),
                    'item'      => (int) ($transaksi['update_kondisi']->total_item ?? 0),
                ],
            ],
        ]);
    }
}
