<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetLokasiModel extends Model
{
    use HasFactory;
    protected $table = "aset_lokasi";
    protected $primaryKey = 'a_id_al';
}
