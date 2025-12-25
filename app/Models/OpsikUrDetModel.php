<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsikUrDetModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rektorat_detail";
    protected $primaryKey = 'id_opurdet';
}
