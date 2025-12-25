<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpfkdetitmModel extends Model
{
    use HasFactory;
    protected $table = "opfik_fakultas_detail_item";
    protected $primaryKey = 'id_opfkdetitm';
}
