<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE barangs b JOIN (SELECT barang_id, SUM(jumlah) as total FROM barang_lokasi GROUP BY barang_id) bl ON b.id = bl.barang_id SET b.jumlah = bl.total');

        Schema::dropIfExists('barang_lokasi');
    }

    public function down(): void
    {
        Schema::create('barang_lokasi', function ($table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasis')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
            $table->unique(['barang_id', 'lokasi_id']);
        });
    }
};
