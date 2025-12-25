<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabpenuuModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_pengesahan_universitas";
    protected $primaryKey = 'id_jabpenuni';
}
