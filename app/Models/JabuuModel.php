<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabuuModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_universitas";
    protected $primaryKey = 'id_jabuni';
}
