<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');

            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasis')->cascadeOnDelete();

            $table->enum('sumber', [
                'pembelian',
                'donasi',
                'hibah',
                'bantuan',
                'inventaris_lama'
            ])->default('pembelian');

            $table->string('foto')->nullable(); // path gambar

            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->integer('jumlah')->default(0);
            $table->date('tanggal_masuk')->nullable();

            // 🔥 Tambahan baru
            $table->string('deskripsi')->nullable();
            $table->integer('sheet_row')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
