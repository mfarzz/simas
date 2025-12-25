<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfModel extends Model
{
    use HasFactory;
    protected $table = "reklasifikasi_fakultas";
    protected $primaryKey = 'id_rf';
}
