<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefStatusProsesModel extends Model
{
    use HasFactory;
    protected $table = "ref_status_proses";    
    protected $primaryKey = 'id';
}
