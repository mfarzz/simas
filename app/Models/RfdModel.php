<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfdModel extends Model
{
    use HasFactory;
    protected $table = "reklasifikasi_fakultas_detail";
    protected $primaryKey = 'id_rfd';
}
