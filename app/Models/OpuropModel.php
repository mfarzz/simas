<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpuropModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rektorat_op";
    protected $primaryKey = 'id_opurop';
}
