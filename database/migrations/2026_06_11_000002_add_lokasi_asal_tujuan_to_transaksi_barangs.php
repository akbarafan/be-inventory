<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_barangs', function (Blueprint $table) {
            $table->foreignId('lokasi_asal_id')->nullable()->constrained('lokasis')->nullOnDelete()->after('jumlah');
            $table->foreignId('lokasi_tujuan_id')->nullable()->constrained('lokasis')->nullOnDelete()->after('lokasi_asal_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_barangs', function (Blueprint $table) {
            $table->dropForeign(['lokasi_asal_id']);
            $table->dropForeign(['lokasi_tujuan_id']);
            $table->dropColumn(['lokasi_asal_id', 'lokasi_tujuan_id']);
        });
    }
};
