<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPerolehanRincianKibModel extends Model
{
    use HasFactory;
    protected $table = "aset_perolehan_rincian_kib";
    protected $primaryKey = 'a_id_aprk';
}
