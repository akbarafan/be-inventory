<?php
namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Exports\TransaksiExport;
use App\Exports\LaporanExport;
use App\Models\Barang;
use App\Models\TransaksiBarang;
use App\Models\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? today()->format('Y-m-d');

        $dari_dt   = $dari . ' 00:00:00';
        $sampai_dt = $sampai . ' 23:59:59';

        $totalTransaksi = TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->count();
        $totalMasuk     = TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->where('jenis', 'masuk')->count();
        $totalKeluar    = TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->where('jenis', 'keluar')->count();
        $totalScan      = ScanLog::whereBetween('scanned_at', [$dari_dt, $sampai_dt])->count();

        $kondisiData = [
            'baik'   => Barang::where('kondisi', 'baik')->count(),
            'rusak'  => Barang::where('kondisi', 'rusak')->count(),
            'hilang' => Barang::where('kondisi', 'hilang')->count(),
        ];

        $chartMonths = [];
        $chartData   = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonths[] = Carbon::create(null, $m)->isoFormat('MMM');
            $chartData[]   = TransaksiBarang::whereYear('tanggal', now()->year)
                                ->whereMonth('tanggal', $m)->count();
        }

        $recentTransaksi = TransaksiBarang::with(['barang', 'user'])
            ->whereBetween('tanggal', [$dari_dt, $sampai_dt])
            ->latest('tanggal')->take(20)->get();

        $syncStatus = [
            ['name' => 'Data Barang',  'rows' => Barang::count(),          'synced' => true,  'time' => 'Aktif'],
            ['name' => 'Transaksi',    'rows' => TransaksiBarang::count(), 'synced' => true,  'time' => 'Aktif'],
            ['name' => 'Scan Log',     'rows' => ScanLog::count(),         'synced' => true,  'time' => 'Aktif'],
            ['name' => 'Lap. Bulanan', 'rows' => 0,                        'synced' => false, 'time' => 'Belum sync'],
        ];

        return view('laporan.index', compact(
            'dari', 'sampai',
            'totalTransaksi', 'totalMasuk', 'totalKeluar', 'totalScan',
            'kondisiData', 'chartMonths', 'chartData',
            'recentTransaksi', 'syncStatus'
        ));
    }

    // Export Laporan Lengkap (multi-sheet)
    public function export(Request $request)
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? today()->format('Y-m-d');

        $filename = 'Laporan-Inventaris-SMK-' . $dari . '-sd-' . $sampai . '.xlsx';
        return Excel::download(new LaporanExport($dari, $sampai), $filename);
    }

    // Export Data Barang saja
    public function exportBarang(Request $request)
    {
        $kondisi    = $request->kondisi;
        $kategori   = $request->kategori_id;
        $search     = $request->search;
        $filename   = 'Data-Barang-SMK-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new BarangExport($kondisi, $kategori, $search), $filename);
    }

    // Export Transaksi saja
    public function exportTransaksi(Request $request)
    {
        $dari     = $request->dari;
        $sampai   = $request->sampai;
        $jenis    = $request->jenis;
        $filename = 'Transaksi-SMK-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new TransaksiExport($dari, $sampai, $jenis), $filename);
    }
}
