<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetRuanganRektoratModel;
use App\Models\User;
use App\Models\VAsetRuanganModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class AsetRuanganCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if(request()->ajax()) {
            return datatables()->of(VAsetRuanganModel::
            orderBy('a_kd_ar')
            ->get())
            ->addColumn('a_id_ar', function ($data) {
                return $data->a_id_ar; 
            })            
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Ruangan.index');
    }
}