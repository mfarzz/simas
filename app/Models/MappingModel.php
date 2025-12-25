<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingModel extends Model
{
    use HasFactory;
    protected $table = "mapping";
    protected $primaryKey = 'id_mapping';
}
