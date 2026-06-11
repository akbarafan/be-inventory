<?php
namespace App\Exports;

use App\Models\Barang;
use App\Models\TransaksiBarang;
use App\Models\ScanLog;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class LaporanExport implements WithMultipleSheets
{
    public function __construct(
        private string $dari,
        private string $sampai
    ) {}

    public function sheets(): array
    {
        return [
            'Ringkasan'       => new LaporanRingkasanSheet($this->dari, $this->sampai),
            'Data Barang'     => new BarangExport(),
            'Transaksi'       => new TransaksiExport($this->dari, $this->sampai),
        ];
    }
}

// ── Sheet 1: Ringkasan ──
class LaporanRingkasanSheet implements
    \Maatwebsite\Excel\Concerns\FromArray,
    \Maatwebsite\Excel\Concerns\WithTitle,
    \Maatwebsite\Excel\Concerns\WithStyles,
    \Maatwebsite\Excel\Concerns\ShouldAutoSize
{
    public function __construct(
        private string $dari,
        private string $sampai
    ) {}

    public function array(): array
    {
        $dari_dt   = $this->dari . ' 00:00:00';
        $sampai_dt = $this->sampai . ' 23:59:59';

        $rows = [];

        // Title
        $rows[] = ['LAPORAN INVENTARIS SMK LABSCHOOL UNESA 1 SURABAYA'];
        $rows[] = ['Periode: ' . Carbon::parse($this->dari)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($this->sampai)->isoFormat('D MMMM Y')];
        $rows[] = ['Dicetak: ' . now()->isoFormat('D MMMM Y, HH:mm')];
        $rows[] = [];

        // Summary stats
        $rows[] = ['RINGKASAN DATA'];
        $rows[] = ['Keterangan', 'Jumlah'];
        $rows[] = ['Total Barang Terdaftar', Barang::count()];
        $rows[] = ['Barang Kondisi Baik', Barang::where('kondisi', 'baik')->count()];
        $rows[] = ['Barang Kondisi Rusak', Barang::where('kondisi', 'rusak')->count()];
        $rows[] = ['Barang Hilang', Barang::where('kondisi', 'hilang')->count()];
        $rows[] = ['Stok Rendah (< 5)', Barang::where('jumlah', '<', 5)->where('jumlah', '>', 0)->count()];
        $rows[] = [];

        // Transaksi summary
        $rows[] = ['RINGKASAN TRANSAKSI PERIODE INI'];
        $rows[] = ['Keterangan', 'Jumlah'];
        $rows[] = ['Total Transaksi', TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->count()];
        $rows[] = ['Barang Masuk', TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->where('jenis', 'masuk')->count()];
        $rows[] = ['Barang Keluar', TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->where('jenis', 'keluar')->count()];
        $rows[] = ['Perpindahan', TransaksiBarang::whereBetween('tanggal', [$dari_dt, $sampai_dt])->where('jenis', 'pindah')->count()];
        $rows[] = ['Total Scan QR', ScanLog::whereBetween('scanned_at', [$dari_dt, $sampai_dt])->count()];
        $rows[] = [];

        // Per kategori
        $rows[] = ['DATA PER KATEGORI'];
        $rows[] = ['Kategori', 'Jumlah Barang', 'Stok Total'];
        foreach (Barang::with('kategori')->get()->groupBy('kategori_id') as $katId => $items) {
            $rows[] = [
                $items->first()->kategori?->nama_kategori ?? 'Tanpa Kategori',
                $items->count(),
                $items->sum('jumlah'),
            ];
        }

        return $rows;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Title style
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'name' => 'Calibri', 'color' => ['rgb' => '0F2A6E']],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '6B7280']],
        ]);

        // Section headers
        foreach ([5, 13, 21] as $row) {
            if ($sheet->getCell('A' . $row)->getValue()) {
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri', 'color' => ['rgb' => '0F2A6E']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF1FC']],
                ]);
            }
        }

        return [];
    }

    public function title(): string { return 'Ringkasan'; }
}
