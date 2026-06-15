<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiBarang extends Model
{
    protected $table = 'transaksi_barangs';

    protected $fillable = [
        'barang_id',
        'user_id',
        'jenis',
        'jumlah',
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'keterangan',
        'tanggal'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasiAsal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_tujuan_id');
    }

    public function barangItems()
    {
        return $this->belongsToMany(BarangItem::class, 'barang_item_transaksi', 'transaksi_id', 'barang_item_id')
            ->withPivot('jenis');
    }
}
