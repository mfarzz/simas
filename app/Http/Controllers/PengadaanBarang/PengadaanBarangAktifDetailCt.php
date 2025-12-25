<?php

namespace App\Http\Controllers\PengadaanBarang;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\PengadaanBarangDetailHistoryModel;
use App\Models\PengadaanBarangDetailModel;
use App\Models\PengadaanBarangModel;
use App\Models\RefStatusProsesDetailModel;
use App\Models\SubSubKategoriModel;
use App\Models\VStokBarangMasukSemuaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PengadaanBarangAktifDetailCt extends Controller
{
    public function index($encripted_id)
    {

        $role_id = auth()->user()->role_id;
        $id_pb = Crypt::decryptString($encripted_id);
        $data_pengadaan = PengadaanBarangModel::where('id', $id_pb)->first();
        //$namaKebutuhan = $data->nm_pb;
        //$tglPengadaan = substr($data->created_at,0,10);

        $tampil_status = RefStatusProsesDetailModel::
        select('ref_status_proses_detail.id','ref_status_proses_detail.nm_rspd')
        ->join('ref_status_proses_detail_untuk','ref_status_proses_detail.id','=','ref_status_proses_detail_untuk.id_rspd')->where('jns_rspd',1)->where('role_id',$role_id)->get();

        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        
        if(request()->ajax()) {
            return datatables()->of(PengadaanBarangDetailModel::
            select('pengadaan_barang_detail.id', 'kelompok.nm_kl', 'subsubkategori.nm_sskt', 'barang.nm_brg', 'jenis_satuan.nm_js', 'barang.stok_brg', 'pengadaan_barang_detail.hrg_estimasi_pbd', 'pengadaan_barang_detail.jmlh_pbd_awal', 'pengadaan_barang_detail.jmlh_pbd', 'pengadaan_barang_detail.hrg_estimasi_pbd','pengadaan_barang_detail.id_rspd', 'ref_status_proses_detail.nm_rspd')
            ->join('ref_status_proses_detail','pengadaan_barang_detail.id_rspd','=','ref_status_proses_detail.id')
            ->join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id')
            ->get())            
            ->addColumn('total_hrg', function ($data) {
                if(auth()->user()->role_id==2)
                {
                    $total_hrg = $data->hrg_estimasi_pbd * $data->jmlh_pbd_awal;
                }
                else if(auth()->user()->role_id==4)
                {
                    $total_hrg = $data->hrg_estimasi_pbd * $data->jmlh_pbd;
                }
                return "$total_hrg"; 
            })
            ->addColumn('action', 'components.form-action.form-action-subkategori')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('PengadaanBarang.Aktif.Detail.index',['daftar_kategori'=>$daftar_kategori, 'encripted_id'=>$encripted_id,'data_pengadaan'=>$data_pengadaan, 'tampil_status'=>$tampil_status]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = VStokBarangMasukSemuaModel::where('kd_sskt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $id_pb = Crypt::decryptString($request->encripted_id);        
        if($request->id == "")
        {
            $jumlah = PengadaanBarangDetailModel::where('kd_brg', $request->idItem)->count();
            if($jumlah>0)
            {            
                return response()->json(['status' => 2]); 
            }
            else
            {             
                $data = new PengadaanBarangDetailModel();
                $data->id_pb = $id_pb;
                $data->id_rspd = "0";
                $data->kd_brg = $request->idItem;
                $data->jmlh_pbd_awal = $request->jumlah;                
                $data->hrg_estimasi_pbd = $request->estimasi_harga;
                $data->status_pbd = "0";
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            if(auth()->user()->role_id==2)
            {                
                $cekData = PengadaanBarangDetailModel::where('id', $request->id)->first();
                if($cekData->kd_brg == $request->idItem and $cekData->jmlh_pbd_awal == $request->jumlah_awal and $cekData->hrg_estimasi_pbd == $request->estimasi_harga)
                {
                   return response()->json(['status' => 3]);  
                }  
                else
                {                    
                    if ($request->id) {                        
                        $jumlah=0;               
                        if($cekData->kd_brg != $request->idItem)
                        {
                            $jumlah = PengadaanBarangDetailModel::where('kd_brg', $request->idItem)->count();
                            if($jumlah>0)
                            {
                                return response()->json(['status' => 2]);
                            }
                        }                
                        if($jumlah==0)
                        {  
                            $data = PengadaanBarangDetailModel::where('id', $request->id)->first();                   
                            $data->kd_brg = $request->idItem;                        
                            $data->jmlh_pbd_awal = $request->jumlah;                        
                            $data->hrg_estimasi_pbd = $request->estimasi_harga;
                            $data->id_rspd = 0;
                            $data->user_id = $user_id;
                            $data->save();
                            
                            return response()->json(['status' => 4]);
                        }            
                    }
                }            
            }    
            else
            {
                $data = PengadaanBarangDetailModel::where('id_pbd', $request->id)->first();
                $data->jmlh_pbd = $request->jumlah;
                $datahistori = PengadaanBarangDetailHistoryModel::where('id_pbd', $request->id)->orderby('id_pbdh','desc')->first();
                $data->jmlh_pbd_awal = $datahistori->jmlh_pbdh;
                $data->ket_pdb = $request->keterangan;
                $data->id_rspd = $request->status_barang;              
                $data->save();
                
                return response()->json(['status' => 4]);
            } 
        }    
    }
    public function edit(Request $request)
    {   
        $data = PengadaanBarangDetailModel::
        select('pengadaan_barang_detail.id', 'kelompok.kd_kl', 'kelompok.nm_kl','subsubkategori.kd_sskt', 'subsubkategori.nm_sskt', 'barang.kd_brg', 'barang.nm_brg', 'barang.stok_brg', 'pengadaan_barang_detail.jmlh_pbd_awal', 'pengadaan_barang_detail.hrg_estimasi_pbd')
        ->join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('pengadaan_barang_detail.id',$request->id)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = PengadaanBarangDetailModel::where('id', $request->id)->first();      
        $data->delete();
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $id_pb = Crypt::decryptString($request->encripted_id);        
        $data = PengadaanBarangDetailModel::where('id', $request->id_validasi)->first();
        $data->jmlh_pbd = $request->jumlah_disetujui;
        $datahistori = PengadaanBarangDetailHistoryModel::where('id_pbd', $request->id_validasi)->orderby('id','desc')->first();
        $data->jmlh_pbd_awal = $datahistori->jmlh_pbdh;
        $data->ket_pdb = $request->ket_validasi;
        $data->id_rspd = $request->status_barang;              
        $data->save();
        return response()->json(['status' => 1]);
    }
}