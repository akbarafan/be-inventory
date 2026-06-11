<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'sumber',
        'foto',
        'kondisi',
        'jumlah',
        'tanggal_masuk',
        'deskripsi'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function barangLokasi()
    {
        return $this->hasMany(BarangLokasi::class);
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiBarang::class);
    }

    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getFotoUrlAttribute()
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/no-image.png');
    }

    public function getStatusAttribute()
    {
        return $this->barangLokasi->sum('jumlah') > 0 ? 'Tersedia' : 'Habis';
    }

    public function getRouteKeyName()
    {
        return 'kode_barang';
    }
}
