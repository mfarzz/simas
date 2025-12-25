<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpursopModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rumah_sakit_op";
    protected $primaryKey = 'id_opursop';
}
