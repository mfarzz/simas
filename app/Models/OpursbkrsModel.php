<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpursbkrsModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rumah_sakit_bkrs";
    protected $primaryKey = 'id_opursbkrs';
}
