<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\JenisSatuanModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubKategoriModel;
use App\Models\SubSubKategoriModel;
use App\Models\VBarangModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangCt extends Controller
{
    public function index($encripted_id)
    {
        $id_kt = Crypt::decryptString($encripted_id);        
        $data_kategori = KategoriModel::where('id_kt', $id_kt)->first();
        $kd_kt=$data_kategori->kd_kt;

        $daftar_jenis = JenisSatuanModel::orderby('nm_js')->get();

        if(request()->ajax()) {
            //return datatables()->of(VBarangModel::
            return datatables()->of(BarangModel::
            join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where('kd_kt', $kd_kt)
            ->orderBy('no_brg')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Kategori.Barang.index',['data_kategori'=>$data_kategori, 'encripted_id'=>$encripted_id, 'daftar_jenis'=>$daftar_jenis]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_kt = Crypt::decryptString($request->encripted_id);        
        $data_kategori = KategoriModel::where('id_kt', $id_kt)->first();
        $kd_kt=$data_kategori->kd_kt;
        if($request->id_brg == "")
        {
            $jumlah = BarangModel::where('kd_kt', $kd_kt)->where('nm_brg', $request->nama)->count();
            if($jumlah>0)
            {            
                return response()->json(['status' => 11]); 
            }
            else
            {   
                $jumlah = BarangModel::where('kd_kt', $kd_kt)->orderBy('no_brg','desc')->count();
                if($jumlah==0)
                {
                    $no_brg_baru_cek = "01";
                }
                else
                {
                    $baris = BarangModel::where('kd_kt', $kd_kt)->orderBy('no_brg','desc')->first();
                    $no_brg_baru = $baris->no_brg + 1;
                    if (strlen($no_brg_baru) == 1) {
                        $no_brg_baru_cek = "0$no_brg_baru";
                    }
                    else{
                        $no_brg_baru_cek = "$no_brg_baru";
                    }
                }

                $data = new BarangModel();
                $data->kd_kt = $kd_kt;
                $data->kd_brg = "$kd_kt$no_brg_baru_cek";
                $data->id_js = $request->idJenis;
                $data->no_brg = $no_brg_baru_cek;
                $data->nm_brg = $request->nama;
                $data->stok_brg = 0;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);  
            }
        }
        else
        {
            $cekData = BarangModel::where('id_brg', $request->id_brg)->first();
            if($cekData->id_js == $request->idJenis and $cekData->nm_brg == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                if ($request->id_brg) {
                    $jumlah=0;
                    if($cekData->nm_brg != $request->nama)
                    {
                        $jumlah = BarangModel::where('kd_kt', $kd_kt)->where('nm_brg', $request->nama)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 12]);
                        }
                    }
                    if($jumlah==0)
                    {                    
                        $data = BarangModel::where('id_brg', $request->id_brg)->first();                   
                        $data->kd_kt = $kd_kt;
                        $data->id_js = $request->idJenis;
                        $data->nm_brg = $request->nama;
                        $data->user_id = $user_id;
                        $data->save();                        
                        return response()->json(['status' => 4]);
                    }            
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = BarangModel::where('id_brg',$request->id_brg)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = BarangModel::where('id_brg', $request->id_brg)->first();
        $kd_brg = $data->kd_brg;
        $jumlah_bm_fakultas = BarangMasukFakultasModel::where('kd_brg', $kd_brg)->count();
        if($jumlah_bm_fakultas>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $jumlah_bk_fakultas = BarangKeluarFakultasModel::where('kd_brg', $kd_brg)->count();
            if($jumlah_bk_fakultas>0)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah_bm_rektorat = BarangMasukRektoratModel::where('kd_brg', $kd_brg)->count();
                if($jumlah_bm_rektorat>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah_bk_rektorat = BarangKeluarRektoratModel::where('kd_brg', $kd_brg)->count();
                    if($jumlah_bk_rektorat>0)
                    {
                        return response()->json(['status' => 5]);
                    }
                    else
                    {
                        $data->delete();  
                        return response()->json(['status' => 1]);
                    }
                }
            }
        }           
    }
}