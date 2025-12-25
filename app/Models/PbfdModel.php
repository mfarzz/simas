<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbfdModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_fakultas_detail";
    protected $primaryKey = 'id_pbfd';
}
