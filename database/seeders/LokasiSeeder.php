<?php
namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Gudang Utama',
            'Ruang IT',
            'Aula Utama',
            'Tata Usaha',
            'Ruang Kelas A',
            'Ruang Kelas B',
            'Ruang Kelas C',
            'Ruang Rapat',
            'Laboratorium',
            'Perpustakaan',
        ];

        foreach ($items as $item) {
            Lokasi::updateOrCreate(['nama_lokasi' => $item]);
        }
    }
}
