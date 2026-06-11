<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ScanLog;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanLogsController extends Controller
{
    public function camera()
    {
        return view('scan.camera');
    }

    public function scan($kode)
    {
        $barang = Barang::where('kode_barang', $kode)
            ->with(['kategori', 'barangLokasi.lokasi',
                'scanLogs' => fn($q) => $q->with('user')->latest('scanned_at')->take(5)
            ])
            ->firstOrFail();

        ScanLog::create([
            'barang_id'  => $barang->id,
            'user_id'    => Auth::id(),
            'device'     => request()->userAgent(),
            'ip_address' => request()->ip(),
            'scanned_at' => now(),
        ]);

        return view('scan.result', compact('barang'));
    }

    public function index(Request $request)
    {
        $logs = ScanLog::with(['barang', 'user'])
            ->latest('scanned_at')
            ->paginate(20);

        return view('scan.logs', compact('logs'));
    }
}
