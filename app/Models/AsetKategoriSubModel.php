<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetKategoriSubModel extends Model
{
    use HasFactory;
    protected $table = "aset_kategori_sub";
    protected $primaryKey = 'a_id_kt_sub';
}
