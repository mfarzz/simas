<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JaburModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_rektorat";
    protected $primaryKey = 'id_jabur';
}
