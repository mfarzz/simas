<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitRumahSakitModel extends Model
{
    use HasFactory;
    protected $table = "unit_rumah_sakit";
    protected $primaryKey = 'id_urs';
}
