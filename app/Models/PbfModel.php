<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbfModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_fakultas";
    protected $primaryKey = 'id_pbf';
}
