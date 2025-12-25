<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpfkakModel extends Model
{
    use HasFactory;
    protected $table = "opsik_fakultas_akhir";
    protected $primaryKey = 'id_opfkak';
}
