<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetBarangModel extends Model
{
    use HasFactory;
    protected $table = "aset_barang";
    protected $primaryKey = 'a_id_brg';
}
