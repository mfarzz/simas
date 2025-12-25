<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabfkModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_fakultas";
    protected $primaryKey = 'id_jabfk';
}
