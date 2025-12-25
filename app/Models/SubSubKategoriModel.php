<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubKategoriModel extends Model
{
    use HasFactory;
    protected $table = "subsubkategori";
    protected $primaryKey = 'id_sskt';
}
