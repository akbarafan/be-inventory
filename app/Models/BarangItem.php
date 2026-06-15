<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangItem extends Model
{
    protected $table = 'barang_items';

    protected $fillable = [
        'barang_id',
        'lokasi_id',
        'kondisi',
        'sumber',
        'tanggal_masuk',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function transaksis()
    {
        return $this->belongsToMany(TransaksiBarang::class, 'barang_item_transaksi', 'barang_item_id', 'transaksi_id')
            ->withPivot('jenis');
    }
}
