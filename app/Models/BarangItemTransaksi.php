<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BarangItemTransaksi extends Pivot
{
    protected $table = 'barang_item_transaksi';

    protected $fillable = [
        'barang_item_id',
        'transaksi_id',
        'jenis',
    ];
}
