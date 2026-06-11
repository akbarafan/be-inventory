<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $table = 'scan_logs';
    protected $fillable = ['barang_id','user_id','device','ip_address','scanned_at'];
    protected $casts = ['scanned_at' => 'datetime'];

    public function barang() { return $this->belongsTo(Barang::class); }
    public function user()   { return $this->belongsTo(User::class); }
}
