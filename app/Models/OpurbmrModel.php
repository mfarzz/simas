<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpurbmrModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rektorat_bmr";
    protected $primaryKey = 'id_opurbmr';
}
