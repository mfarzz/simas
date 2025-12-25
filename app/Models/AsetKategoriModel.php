<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetKategoriModel extends Model
{
    use HasFactory;
    protected $table = "aset_kategori";
    protected $primaryKey = 'a_id_kt';
}
