<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabpenfkModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_pengesahan_fakultas";
    protected $primaryKey = 'id_jabpenfk';
}
