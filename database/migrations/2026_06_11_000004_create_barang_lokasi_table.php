<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasis')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
            $table->unique(['barang_id', 'lokasi_id']);
        });

        DB::statement('INSERT INTO barang_lokasi (barang_id, lokasi_id, jumlah, created_at, updated_at) SELECT id, lokasi_id, jumlah, NOW(), NOW() FROM barangs WHERE jumlah > 0');
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_lokasi');
    }
};
