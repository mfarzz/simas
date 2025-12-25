<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetLokasiModel;
use App\Models\UnitRektoratModel;
use Illuminate\Http\Request;
use Datatables;

class AsetLokasiRektoratCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(AsetLokasiModel::
            join('unit_rektorat','aset_lokasi.id_ur','=','unit_rektorat.id_ur')      
            ->where('aset_lokasi.id_fk',0)
            ->orderBy('a_kd_al')
            ->get())
            ->addColumn('a_id_al', function ($data) {
                return $data->a_id_al; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_unit_rektorat = UnitRektoratModel::orderby('nm_ur')->get();
        return view('Sis_aset.MasterData.Lokasi.Rektorat.index',['daftar_unit_rektorat'=>$daftar_unit_rektorat]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_kd_apbw = "023170800";
        if($request->a_id_al == "")
        {            
            $jumlah = AsetLokasiModel::where('a_uakpb', $request->kode)->where('a_no_al', $request->no_uapkb)->count();            
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $a_kd_al = "$a_kd_apbw$request->kode$request->no_uapkb$request->jenis_kelompok";
                $data = new AsetLokasiModel();
                $data->a_kd_apbw = $a_kd_apbw;
                $data->a_kd_al = $a_kd_al;
                $data->a_uakpb = $request->kode;
                $data->a_no_al = $request->no_uapkb;
                $data->a_jk_al = $request->jenis_kelompok;
                $data->id_fk = 0;
                $data->id_ur = $request->unit_rektorat;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = AsetLokasiModel::where('a_id_al', $request->a_id_al)->first();
           
            if($cekData->a_uakpb == $request->kode and $cekData->a_no_al == $request->no_uapkb and $cekData->a_jk_al == $request->jenis_kelompok and $cekData->id_ur == $request->unit_rektorat)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->a_uakpb != $request->kode)
                {
                    /*$jumlah = AsetKategoriSubModel::where('a_kd_kt', $cekData->a_kd_kt)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 13]);
                    }
                    else
                    {*/
                        $jumlah = AsetLokasiModel::where('a_uakpb', $request->kode)->where('a_no_al', $request->no_uapkb)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 11]);
                        }
                    //}                        
                }
                if($cekData->a_no_al != $request->no_uapkb)
                {
                    $jumlah = AsetLokasiModel::where('a_uakpb', $request->kode)->where('a_no_al', $request->no_uapkb)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                { 
                    $a_kd_al = "$a_kd_apbw$request->kode$request->no_uapkb$request->jenis_kelompok";
                    $data = AsetLokasiModel::where('a_id_al', $request->a_id_al)->first();
                    $data->a_kd_apbw = $a_kd_apbw;
                    $data->a_kd_al = $a_kd_al;
                    $data->a_uakpb = $request->kode;
                    $data->a_no_al = $request->no_uapkb;
                    $data->a_jk_al = $request->jenis_kelompok;
                    $data->id_ur = $request->unit_rektorat;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }                
            }        
        }
    }

    public function edit(Request $request)
    {   
        $data = AsetLokasiModel::where('a_id_al',$request->a_id_al)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = AsetLokasiModel::where('a_id_al', $request->a_id_al)->first();
        //$jumlah = AsetKategoriSubModel::where('a_kd_kt', $data->a_kd_kt)->count();
        //if($jumlah>0)
        //{
         //   return response()->json(['status' => 2]);
        //}
        //else
        //{
              
            $data->delete();   
            return response()->json(['status' => 1]);
        //}
    }
}