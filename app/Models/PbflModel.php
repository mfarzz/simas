<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbflModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_fakultas_log";
    protected $primaryKey = 'id_pbfl';
}
