<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikRumahSakitModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rumah_sakit";
    protected $primaryKey = 'id_opurs';
}
