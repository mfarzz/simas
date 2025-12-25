<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPerolehanRincianModel extends Model
{
    use HasFactory;
    protected $table = "aset_perolehan_rincian";
    protected $primaryKey = 'a_id_apr';
}
