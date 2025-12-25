<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikRektoratModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rektorat";
    protected $primaryKey = 'id_opur';
}
