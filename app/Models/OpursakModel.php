<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpursakModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rumah_sakit_akhir";
    protected $primaryKey = 'id_opursak';
}
