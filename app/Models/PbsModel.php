<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbsModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_status";
    protected $primaryKey = 'id_pbs';
}
