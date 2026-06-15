<?php
namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Contracts\Queue\ShouldQueue;

class BarangExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private ?string $kondisi = null,
        private ?string $kategori_id = null,
        private ?string $search = null
    ) {}

    public function query()
    {
        $query = Barang::with(['kategori', 'barangLokasi.lokasi'])->orderBy('kode_barang');

        if ($this->kondisi && $this->kondisi !== 'semua') {
            $query->whereHas('barangItems', function ($q) {
                $q->where('kondisi', $this->kondisi)->where('status', 'aktif');
            });
        }
        if ($this->kategori_id) {
            $query->where('kategori_id', $this->kategori_id);
        }
        if ($this->search) {
            $q = $this->search;
            $query->where(fn($sq) =>
                $sq->where('nama_barang', 'like', "%$q%")
                   ->orWhere('kode_barang', 'like', "%$q%")
            );
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Kondisi',
            'Jumlah',
            'Sumber',
            'Tanggal Masuk',
            'Deskripsi',
        ];
    }

    public function map($barang): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->kategori?->nama_kategori ?? '-',
            $barang->barangLokasi->map(fn($bl) => $bl->lokasi?->nama_lokasi . ': ' . $bl->jumlah)->implode(', ') ?: '-',
            $barang->barangItems->where('status', 'aktif')
                ->groupBy('kondisi')
                ->map(fn($items, $k) => ucfirst($k) . ': ' . $items->count())
                ->implode(', ') ?: '-',
            $barang->jumlah,
            ucfirst(str_replace('_', ' ', $barang->sumber ?? '-')),
            $barang->tanggal_masuk?->format('d/m/Y') ?? '-',
            $barang->deskripsi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Header row style
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
                'name'  => 'Calibri',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2A6E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row height header
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Alternate row colors
        for ($row = 2; $row <= $lastRow; $row++) {
            $color = ($row % 2 === 0) ? 'F0F4FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'font' => ['name' => 'Calibri', 'size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Border all cells
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Center align No column
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Center align Jumlah column
        $sheet->getStyle('G2:G' . $lastRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Color code kondisi column F
        for ($row = 2; $row <= $lastRow; $row++) {
            $kondisi = strtolower($sheet->getCell('F' . $row)->getValue());
            $colors  = ['baik' => ['bg' => 'DCFCE7', 'fg' => '166534'], 'rusak' => ['bg' => 'FEF9C3', 'fg' => '854D0E'], 'hilang' => ['bg' => 'FEE2E2', 'fg' => '991B1B']];
            if (isset($colors[$kondisi])) {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $colors[$kondisi]['bg']]],
                    'font' => ['color' => ['rgb' => $colors[$kondisi]['fg']], 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        return [];
    }

    public function title(): string
    {
        return 'Data Barang';
    }
}
