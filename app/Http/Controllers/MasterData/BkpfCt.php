<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BkfnModel;
use App\Models\BkpfModel;
use App\Models\FakultasJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class BkpfCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first(); 
        if(request()->ajax()) {
            return datatables()->of(BkpfModel::
            where('barang_keluar_penerima_fakultas.id_fk',$datafakultas->id_fk)
            ->get())            
            ->addColumn('id_bkpf', function ($data) {
                return $data->id_bkpf; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Penerima.Fakultas.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($request->id_bkpf == "")
        {
            $jumlah = BkpfModel::where('id_fk', $id_fk)->where('nm_bkpf', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new BkpfModel();
                $data->id_fk = $id_fk;
                $data->nm_bkpf = $request->nama;
                $data->status_bkpf = $request->status;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = BkpfModel::where('id_bkpf', $request->id_bkpf)->first();
            if($cekData->nm_bkpf == $request->nama and $cekData->status_bkpf == $request->status )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_bkp != $request->nama)
                {
                    $jumlah = BkpfModel::where('id_fk', $id_fk)->where('nm_bkpf', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BkpfModel::where('id_bkpf', $request->id_bkpf)->first();                   
                    $data->nm_bkpf = $request->nama;
                    $data->status_bkpf = $request->status;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = BkpfModel::where('id_bkpf',$request->id_bkpf)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = BkfnModel::where('id_bkpf',$request->id_bkpf)->count();
        if($jumlah==0)
        {
            return response()->json(['status' => 1]);
            $data = BkpfModel::where('id_bkpf', $request->id_bkpf)->first();   
            $data->delete();
        }
        else
        {
            return response()->json(['status' => 2]);
        }
    }
}