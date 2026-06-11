<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiBarang;
use App\Models\ScanLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang      = Barang::count();
        $stokRendah       = Barang::where('jumlah', '<', 5)->where('jumlah', '>', 0)->count();
        $barangRusak      = Barang::whereIn('kondisi', ['rusak', 'hilang'])->count();
        $transaksiHariIni = TransaksiBarang::whereDate('tanggal', today())->count();
        $masukHariIni     = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'masuk')->count();
        $keluarHariIni    = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'keluar')->count();

        $recentBarang    = Barang::with(['kategori', 'barangLokasi.lokasi'])->latest()->take(5)->get();
        $recentTransaksi = TransaksiBarang::with(['barang', 'user'])->latest('tanggal')->take(6)->get();

        // Chart 7 hari terakhir
        $chartData   = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = Carbon::today()->subDays($i);
            $chartLabels[] = $date->isoFormat('ddd');
            $chartData[]   = TransaksiBarang::whereDate('tanggal', $date)->count();
        }

        return view('dashboard', compact(
            'totalBarang', 'stokRendah', 'barangRusak',
            'transaksiHariIni', 'masukHariIni', 'keluarHariIni',
            'recentBarang', 'recentTransaksi', 'chartData', 'chartLabels'
        ));
    }
}
