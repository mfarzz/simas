<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabpenuuModel;
use App\Models\JabuuModel;
use Illuminate\Http\Request;
use Datatables;

class JabpenuuCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        
        if(request()->ajax()) {            
            return datatables()->of(JabpenuuModel::
            join('jabatan_universitas','jabatan_pengesahan_universitas.id_jabuni','=','jabatan_universitas.id_jabuni')
            ->get())
            ->addColumn('id_jabpenuni', function ($data) {
                return $data->id_jabpenuni; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_jabatan = JabuuModel::orderby('nm_jabuni')->get();
        return view('MasterData.Jabatan.Universitas.Pengesahan.index', ['daftar_jabatan'=>$daftar_jabatan]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        if($request->id_jabpenuni == "")
        {
            $jumlah = JabpenuuModel::where('id_jabuni', $request->jabatan)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabpenuuModel();
                $data->id_jabuni = $request->jabatan;
                $data->nm_jabpenuni = $request->nama;
                $data->nik_jabpenuni = $request->nik;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabpenuuModel::where('id_jabpenuni', $request->id_jabpenuni)->first();
            if($cekData->nm_jabpenuni == $request->nama and $cekData->nik_jabpenuni == $request->nik and $cekData->id_jabuni == $request->jabatan )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->id_jabuni != $request->jabatan)
                {
                    $jumlah = JabpenuuModel::where('id_jabuni', $request->jabatan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabpenuuModel::where('id_jabpenuni', $request->id_jabpenuni)->first();
                    $data->id_jabuni = $request->jabatan;
                    $data->nm_jabpenuni = $request->nama;
                    $data->nik_jabpenuni = $request->nik;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabpenuuModel::where('id_jabpenuni',$request->id_jabpenuni)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = JabpenuuModel::where('id_jabpenuni', $request->id_jabpenuni)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}