<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbrlModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat_log";
    protected $primaryKey = 'id_pbrl';
}
