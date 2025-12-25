<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefKegiatanMOdel extends Model
{
    use HasFactory;
    protected $table = "ref_kegiatan";    
    protected $primaryKey = 'id';
}
