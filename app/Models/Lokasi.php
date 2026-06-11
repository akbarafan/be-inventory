<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasis';
    protected $fillable = ['nama_lokasi','deskripsi'];

    public function barangLokasi() { return $this->hasMany(BarangLokasi::class); }
}
