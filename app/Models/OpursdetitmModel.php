<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpursdetitmModel extends Model
{
    use HasFactory;
    protected $table = "opfik_rumah_sakit_detail_item";
    protected $primaryKey = 'id_opursdetitm';
}
