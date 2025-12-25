<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkprModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_penerima_rektorat";
    protected $primaryKey = 'id_bkpr';
}
