<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePenggunaModel extends Model
{
    use HasFactory;
    protected $table = "role_pengguna";
    protected $fillable = ["nama_rp, posisi_pb, user_id, created_at, updated_at"];
    protected $primaryKey = 'id';
}
