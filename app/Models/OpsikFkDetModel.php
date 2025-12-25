<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikFkDetModel extends Model
{
    use HasFactory;
    protected $table = "opsik_fakultas_detail";
    protected $primaryKey = 'id_opfkdet';
}
