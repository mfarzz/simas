<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabpenursModel extends Model
{
    use HasFactory;
    protected $table = "jabatan_pengesahan_rumah_sakit";
    protected $primaryKey = 'id_jabpenurs';
}
