<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetTempLapModel extends Model
{
    use HasFactory;
    protected $table = "aset_temp_lap";
    protected $primaryKey = 'id_atl';
}
