<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkpfModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_penerima_fakultas";
    protected $primaryKey = 'id_bkpf';
}
