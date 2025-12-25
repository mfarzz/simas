<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpurdetitmModel extends Model
{
    use HasFactory;
    protected $table = "opfik_rektorat_detail_item";
    protected $primaryKey = 'id_opurdetitm';
}
