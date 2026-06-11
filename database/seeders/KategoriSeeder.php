<?php
namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Elektronik',
            'Furnitur',
            'ATK',
            'Olahraga',
            'Kebersihan',
            'Laboratorium',
            'Perpustakaan',
            'Lainnya',
        ];

        foreach ($items as $item) {
            Kategori::updateOrCreate(['nama_kategori' => $item]);
        }
    }
}
