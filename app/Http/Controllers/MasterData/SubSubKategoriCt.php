<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubKategoriModel;
use App\Models\SubSubKategoriModel;
use App\Models\VSubSubKategoriModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class SubSubKategoriCt extends Controller
{
    public function index($encripted_id)
    {
        $id_skt = Crypt::decryptString($encripted_id);        
        $data_subkategori = SubKategoriModel::join('kategori','subkategori.kd_kt','=','kategori.kd_kt')->where('subkategori.id_skt', $id_skt)->first();
        $kd_skt=$data_subkategori->kd_skt;

        $data_kategori = KategoriModel::
        where('kd_kt', $data_subkategori->kd_kt)->first();
        $kd_kt = Crypt::encryptString($data_kategori->id_kt);
        
        $daftar_kelompok = KelompokModel::orderby('kd_kl')->get();

        if(request()->ajax()) {
            return datatables()->of(VSubSubKategoriModel::
            leftjoin('v_lab_nilai2_subsubkategori','v_subsubkategori.kd_sskt','=','v_lab_nilai2_subsubkategori.v_kd_sskt')
            ->where('v_subsubkategori.kd_skt', $kd_skt)
            ->orderBy('no_sskt')
            ->get())
            ->addColumn('id_sskt_en', function ($data) {
                $id_sskt_en = Crypt::encryptString($data->id_sskt);
                return $id_sskt_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Kategori.SubKategori.SubSubKategori.index',['data_subkategori'=>$data_subkategori, 'encripted_id'=>$encripted_id, 'daftar_kelompok'=>$daftar_kelompok, 'kd_kt'=>$kd_kt]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_skt = Crypt::decryptString($request->encripted_id);        
        $data_subkategori = SubKategoriModel::join('kategori','subkategori.kd_kt','=','kategori.kd_kt')->where('subkategori.id_skt', $id_skt)->first();
        $kd_skt=$data_subkategori->kd_skt;
        if($request->id_sskt == "")
        {
            $jumlah = SubSubKategoriModel::where('kd_skt', $kd_skt)->where('nm_sskt', $request->nama)->count();
            if($jumlah>0)
            {            
                return response()->json(['status' => 11]);
            }
            else
            {            
                $jumlah = SubSubKategoriModel::where('kd_skt', $kd_skt)->orderBy('no_sskt','desc')->count();
                if($jumlah==0)
                {
                    $no_sskt_baru_cek = "01";
                }
                else
                {
                    $baris = SubSubKategoriModel::where('kd_skt', $kd_skt)->orderBy('no_sskt','desc')->first();
                    $no_sskt_baru = $baris->no_sskt + 1;
                    if (strlen($no_sskt_baru) == 1) {
                        $no_sskt_baru_cek = "0$no_sskt_baru";
                    }
                    else{
                        $no_sskt_baru_cek = "$no_sskt_baru";
                    }
                }

                $data = new SubSubKategoriModel();
                $data->kd_skt = $kd_skt;
                $data->kd_sskt = "$kd_skt$no_sskt_baru_cek";
                $data->kd_kl = $request->idKelompok;
                $data->no_sskt = $no_sskt_baru_cek;
                $data->nm_sskt = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]); 
            }
        }
        else
        {
            $cekData = SubSubKategoriModel::where('id_sskt', $request->id_sskt)->first();
            if($cekData->kd_kl == $request->idKelompok and $cekData->no_sskt == $request->kode and $cekData->nm_sskt == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                if ($request->id_sskt) {
                    $jumlah=0;         
                    if($cekData->nm_sskt != $request->nama)
                    {
                        $jumlah = SubSubKategoriModel::where('kd_skt', $kd_skt)->where('nm_sskt', $request->nama)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 12]);
                        }
                    }
                    if($jumlah==0)
                    {                    
                        $data = SubSubKategoriModel::where('id_sskt', $request->id_sskt)->first();                   
                        $data->kd_skt = $kd_skt;
                        $data->kd_kl = $request->idKelompok;
                        $data->nm_sskt = $request->nama;
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
        $data = SubSubKategoriModel::where('id_sskt',$request->id_sskt)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = SubSubKategoriModel::where('id_sskt', $request->id_sskt)->first();  
        $kd_sskt = $data->kd_sskt;
        $jumlah = BarangModel::where('kd_sskt', $kd_sskt)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $data->delete();  
            return response()->json(['status' => 1]);
        }
    }
}