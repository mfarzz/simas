<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetUbahItemModel extends Model
{
    use HasFactory;
    protected $table = "aset_ubah_item";
    protected $primaryKey = 'a_id_aui';
}
