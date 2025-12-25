<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use App\Models\VBarangRektoratDetailModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangRektoratCt extends Controller
{
    public function index()
    {
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();

        if(request()->ajax()) {
            return datatables()->of(VBarangRektoratDetailModel::
            where('id_ur',$barisrektorat->id_ur)
            ->orderBy('kd_brg')
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Barang.Rektorat.index');
    }
}