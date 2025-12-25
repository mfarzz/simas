<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetRuanganRektoratModel extends Model
{
    use HasFactory;
    protected $table = "aset_ruangan_rektorat";
    protected $primaryKey = 'a_id_arr';
}
