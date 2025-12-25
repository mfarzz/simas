<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikUrsDetModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rumah_sakit_detail";
    protected $primaryKey = 'id_opursdet';
}
