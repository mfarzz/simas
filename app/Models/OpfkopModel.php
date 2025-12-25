<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpfkopModel extends Model
{
    use HasFactory;
    protected $table = "opsik_fakultas_op";
    protected $primaryKey = 'id_opfkop';
}
