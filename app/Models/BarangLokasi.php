<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangLokasi extends Model
{
    protected $table = 'barang_lokasi';

    protected $fillable = [
        'barang_id',
        'lokasi_id',
        'jumlah',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
