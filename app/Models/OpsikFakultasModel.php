<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikFakultasModel extends Model
{
    use HasFactory;
    protected $table = "opsik_fakultas";
    protected $primaryKey = 'id_opfk';
}
