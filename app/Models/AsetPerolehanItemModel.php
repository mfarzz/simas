<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPerolehanItemModel extends Model
{
    use HasFactory;
    protected $table = "aset_perolehan_item";
    protected $primaryKey = 'a_id_api';
}
