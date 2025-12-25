<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabpenurModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_pengesahan_rektorat";
    protected $primaryKey = 'id_jabpenur';
}
