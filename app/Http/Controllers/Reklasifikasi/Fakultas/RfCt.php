<?php

namespace App\Http\Controllers\Reklasifikasi\Fakultas;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\BmfsModel;
use App\Models\FakultasJabatanModel;
use App\Models\RfdModel;
use App\Models\RfModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class RfCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        if(request()->ajax()) {
            return datatables()->of(RfModel::join('barang_masuk_fakultas_sp2d','barang_masuk_fakultas_sp2d.id_bmfs','=','reklasifikasi_fakultas.id_bmfs')
            ->where('reklasifikasi_fakultas.id_fk',$datafakultas->id_fk)
            ->get())
            ->addColumn('id_rf', function ($data) {
                return $data->id_rf; 
            })
            ->addColumn('id_rf_en', function ($data) {
                $id_rf_en = Crypt::encryptString($data->id_rf);
                return $id_rf_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_bm = BmfsModel::where('id_fk',$datafakultas->id_fk)->where('status_bmfs',1)->orderby('no_bmfs')->get();  
        return view('Reklasifikasi.Fakultas.index',['daftar_bm'=>$daftar_bm]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($request->id_rf == "")
        {
            $jumlah_belum = RfModel::where('id_fk', $id_fk)->where('status_rf', 0)->count();
            if($jumlah_belum >0)
            {
                return response()->json(['status' => 5]);
            }
            else
            {
                $jumlah = RfModel::where('id_fk', $id_fk)->where('no_rf', $request->no_berita_acara)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {
                    $data = new RfModel();
                    $data->id_fk = $id_fk;
                    $data->id_bmfs = $request->no_sp2d;
                    $data->no_rf = $request->no_berita_acara;
                    $data->tgl_rf = $request->tgl_berita_acara;
                    $data->status_rf = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = RfModel::where('id_fk', $id_fk)->where('id_rf', $request->id_rf)->first();
            if($cekData->no_rf == $request->no_berita_acara and $cekData->tgl_rf == $request->tgl_berita_acara and $cekData->id_bmfs == $request->no_sp2d )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->no_rf != $request->no_berita_acara)
                {
                    $jumlah = RfModel::where('id_fk', $id_fk)->where('no_rf', $request->no_berita_acara)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = RfModel::where('id_rf', $request->id_rf)->first();                   
                    $data->no_rf = $request->no_berita_acara;
                    $data->tgl_rf = $request->tgl_berita_acara;
                    $data->id_bmfs = $request->no_sp2d;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = RfModel::where('id_rf',$request->id_rf)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = RfModel::where('id_rf', $request->id_rf)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;

        $jumlah_rfd = RfdModel::where('id_rf', $request->id_rf)->count();
        if($jumlah_rfd == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = RfModel::where('id_rf', $request->id_rf)->first();
            $datareklasifikasi = RfdModel::where('id_rf', $cek_data->id_rf)
            ->orderBy('id_bmf','asc')
            ->get();
            foreach($datareklasifikasi as $barisbkf)
            {

                $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbkf->id_bmf)->first();                   
                $databmupdate->kd_brg = $barisbkf->kd_brg_baru;
                $databmupdate->save();
            }
            $cek_data->status_rf = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}