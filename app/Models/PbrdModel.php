<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbrdModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat_detail";
    protected $primaryKey = 'id_pbrd';
}
