<?php
namespace App\Exports;

use App\Models\TransaksiBarang;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private ?string $dari   = null,
        private ?string $sampai = null,
        private ?string $jenis  = null
    ) {}

    public function query()
    {
        $query = TransaksiBarang::with(['barang', 'user'])->latest('tanggal');

        if ($this->jenis) {
            $query->where('jenis', $this->jenis);
        }
        if ($this->dari) {
            $query->whereDate('tanggal', '>=', $this->dari);
        }
        if ($this->sampai) {
            $query->whereDate('tanggal', '<=', $this->sampai);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Kode Barang', 'Nama Barang', 'Jenis', 'Jumlah', 'Petugas', 'Keterangan', 'Tanggal & Waktu'];
    }

    public function map($t): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $t->barang?->kode_barang ?? '-',
            $t->barang?->nama_barang ?? '-',
            ucfirst($t->jenis),
            $t->jumlah,
            $t->user?->name ?? 'System',
            $t->keterangan ?? '-',
            \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2A6E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        for ($row = 2; $row <= $lastRow; $row++) {
            $color = ($row % 2 === 0) ? 'F0F4FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['name' => 'Calibri', 'size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        // Color jenis column D
        $jenisColors = [
            'masuk'  => ['bg' => 'DBEAFE', 'fg' => '1E40AF'],
            'keluar' => ['bg' => 'FEE2E2', 'fg' => '991B1B'],
            'pindah' => ['bg' => 'FEF9C3', 'fg' => '854D0E'],
            'scan'   => ['bg' => 'DCFCE7', 'fg' => '166534'],
        ];
        for ($row = 2; $row <= $lastRow; $row++) {
            $jenis = strtolower($sheet->getCell('D' . $row)->getValue());
            if (isset($jenisColors[$jenis])) {
                $sheet->getStyle('D' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $jenisColors[$jenis]['bg']]],
                    'font' => ['color' => ['rgb' => $jenisColors[$jenis]['fg']], 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        return [];
    }

    public function title(): string { return 'Transaksi Barang'; }
}
