<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JenisSatuanModel;
use App\Models\RefKegiatanMOdel;
use App\Models\RefStatusProsesModel;
use App\Models\RefStatusProsesUntukModel;
use App\Models\RolePenggunaModel;
use Illuminate\Http\Request;
use Datatables;

class RefStatusProsesUntukCt extends Controller
{
    public function index()
    {
        $daftar_status = RefStatusProsesModel::get();
        $daftar_level = RolePenggunaModel::get();
        $daftar_kegiatan = RefKegiatanMOdel::get();

        if(request()->ajax()) {
            return datatables()->of(RefStatusProsesUntukModel::
            join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id')
            ->join('ref_kegiatan','ref_status_proses_untuk.id_rk','=','ref_kegiatan.id')
            ->get())
            ->addColumn('action', 'components.form-action.form-action-ud')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.StatusProsesUntuk.index',['daftar_status'=>$daftar_status, 'daftar_level'=>$daftar_level, 'daftar_kegiatan'=>$daftar_kegiatan]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        if($request->id == "")
        {
            $jumlah = RefStatusProsesUntukModel::
                where('id_rsp', $request->idStatus)
                ->where('nm_rspu', $request->nama)
                ->where('role_id_proses', $request->role_proses)
                ->where('role_id_pilihan', $request->role_pilihan)
                ->where('id_rk', $request->kegiatan)
                ->where('posisi_pb_proses', $request->posisi_proses)
                ->where('posisi_pb_pilihan', $request->posisi_pilihan)
                ->where('sts_rspu', $request->status_data)
                ->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new RefStatusProsesUntukModel();
                $data->id_rsp = $request->idStatus;
                $data->nm_rspu = $request->nama;
                $data->role_id_proses = $request->role_proses;
                $data->role_id_pilihan = $request->role_pilihan;
                $data->kondisi_rspu = $request->kondisi;
                $data->posisi_pb_proses = $request->posisi_proses;
                $data->posisi_pb_pilihan = $request->posisi_pilihan; 
                $data->id_rk = $request->kegiatan;
                $data->sts_rspu = $request->status_data;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = RefStatusProsesUntukModel::where('id', $request->id)->first();
            if($cekData->id_rsp == $request->idStatus and $cekData->nm_rspu == $request->nama and $cekData->role_id_proses == $request->role_proses and $cekData->role_id_pilihan == $request->role_pilihan and $cekData->kondisi_id == $request->kondisi and $cekData->posisi_pb_proses == $request->posisi_proses and $cekData->posisi_pb_pilihan == $request->posisi_pilihan and $cekData->id_rk == $request->kegiatan and $cekData->status_data == $request->status_data)
            if($cekData->nm_js == $request->nama )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_rspu == $request->nama or $cekData->id_rsp == $request->idStatus or $cekData->role_id_proses == $request->role_proses or $cekData->role_id_pilihan == $request->pilihan  or $cekData->posisi_pb_proses == $request->posisi_proses or $cekData->posisi_pb_pilihan == $request->posisi_pilihan or $cekData->id_rk == $request->kegiatan)
                {
                    $jumlah = RefStatusProsesUntukModel::
                    where('id_rsp', $request->idStatus)
                    ->where('nm_rspu', $request->nama)
                    ->where('role_id_proses', $request->role_proses)
                    ->where('role_id_pilihan', $request->role_pilihan)
                    ->where('id_rk', $request->kegiatan)
                    ->where('posisi_pb_proses', $request->posisi_proses)
                    ->where('posisi_pb_pilihan', $request->posisi_pilihan)
                    ->where('kondisi_rspu', $request->kondisi)
                    ->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = RefStatusProsesUntukModel::where('id_rspu', $request->id)->first();                   
                    $data->id_rsp = $request->idStatus;
                    $data->nm_rspu = $request->nama;
                    $data->role_id_proses = $request->role_proses;
                    $data->role_id_pilihan = $request->role_pilihan;
                    $data->kondisi_rspu = $request->kondisi;
                    $data->posisi_pb_proses = $request->posisi_proses;
                    $data->posisi_pb_pilihan = $request->posisi_pilihan;
                    $data->id_rk = $request->kegiatan;
                    $data->sts_rspu = $request->status_data;
                    $data->user_id = $user_id;           
                    $data->save();
                    return response()->json(['status' => 4]);
                }                
            }            
        }
    }

    public function edit(Request $request)
    {   
        $data = RefStatusProsesUntukModel::where('id',$request->id)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = RefStatusProsesUntukModel::where('id', $request->id)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}