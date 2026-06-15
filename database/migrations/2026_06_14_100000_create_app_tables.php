<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. KATEGORIS ───
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        // ─── 2. LOKASIS ───
        Schema::create('lokasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->timestamps();
        });

        // ─── 3. BARANGS ───
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->enum('sumber', ['pembelian', 'donasi', 'hibah', 'bantuan', 'inventaris_lama'])->default('pembelian');
            $table->string('foto')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->integer('jumlah')->default(0);
            $table->date('tanggal_masuk')->nullable();
            $table->string('deskripsi')->nullable();
            $table->integer('sheet_row')->nullable();
            $table->timestamps();
        });

        // ─── 4. BARANG_LOKASI ───
        Schema::create('barang_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasis')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
            $table->unique(['barang_id', 'lokasi_id']);
        });

        // ─── 5. TRANSAKSI_BARANGS ───
        Schema::create('transaksi_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar', 'pindah', 'scan', 'update_kondisi']);
            $table->integer('jumlah')->default(1);
            $table->foreignId('lokasi_asal_id')->nullable()->constrained('lokasis')->nullOnDelete();
            $table->foreignId('lokasi_tujuan_id')->nullable()->constrained('lokasis')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });

        // ─── 6. BARANG_ITEMS ───
        Schema::create('barang_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lokasi_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kondisi')->default('baik');
            $table->string('sumber')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        // ─── 7. BARANG_ITEM_TRANSAKSI (pivot) ───
        Schema::create('barang_item_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_item_id')->constrained('barang_items')->cascadeOnDelete();
            $table->foreignId('transaksi_id')->constrained('transaksi_barangs')->cascadeOnDelete();
            $table->string('jenis');
            $table->timestamps();
        });

        // ─── 8. SCAN_LOGS ───
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
        Schema::dropIfExists('barang_item_transaksi');
        Schema::dropIfExists('barang_items');
        Schema::dropIfExists('transaksi_barangs');
        Schema::dropIfExists('barang_lokasi');
        Schema::dropIfExists('barangs');
        Schema::dropIfExists('lokasis');
        Schema::dropIfExists('kategoris');
    }
};
