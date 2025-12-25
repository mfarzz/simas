<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetUbahModel extends Model
{
    use HasFactory;
    protected $table = "aset_ubah";
    protected $primaryKey = 'a_id_au';
}
