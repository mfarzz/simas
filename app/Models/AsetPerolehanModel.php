<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPerolehanModel extends Model
{
    use HasFactory;
    protected $table = "aset_perolehan";
    protected $primaryKey = 'a_id_ap';
}
