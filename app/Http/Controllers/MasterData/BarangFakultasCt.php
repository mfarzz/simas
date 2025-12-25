<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VBarangFakultasDetailModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangFakultasCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VBarangFakultasDetailModel::
            where('id_fk',$datafakultas->id_fk)
            ->orderBy('kd_brg')
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Barang.Fakultas.index');
    }
}