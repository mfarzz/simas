<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitRektoratModel extends Model
{
    use HasFactory;
    protected $table = "unit_rektorat";
    protected $primaryKey = 'id_ur';
}
