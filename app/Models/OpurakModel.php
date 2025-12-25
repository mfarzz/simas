<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpurakModel extends Model
{
    use HasFactory;
    protected $table = "opsik_rektorat_akhir";
    protected $primaryKey = 'id_opurak';
}
