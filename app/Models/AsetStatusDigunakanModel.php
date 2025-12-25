<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetStatusDigunakanModel extends Model
{
    use HasFactory;
    protected $table = "aset_status_digunakan";
    protected $primaryKey = 'a_id_asd';
}
