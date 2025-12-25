<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefProvinsiModel extends Model
{
    use HasFactory;
    protected $table = "ref_provinsi";    
    protected $primaryKey = 'id_rprov';
}
