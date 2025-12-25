<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetHapusItemModel extends Model
{
    use HasFactory;
    protected $table = "aset_hapus_item";
    protected $primaryKey = 'a_id_ahi';
}
