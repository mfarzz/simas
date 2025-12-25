<?php

namespace App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetHapusItemModel;
use App\Models\AsetHapusModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PenghapusanTransferKeluarFakultasKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   
            
        $datalokasi = AsetLokasiModel::where('id_fk', $datafakultas->id_fk)->first();

        if(request()->ajax()) {
            return datatables()->of(AsetHapusModel::
            join('aset_barang', 'aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->join('aset_lokasi', 'aset_hapus.a_kd_al_tujuan','=','aset_lokasi.a_kd_al')
            ->where('a_kd_ajkt','C02')
            ->where('aset_hapus.a_kd_al',$datalokasi->a_kd_al)
            ->get())
            ->addColumn('a_id_ah_en', function ($data) {
                $a_id_ah_en = Crypt::encryptString($data->a_id_ah);
                return $a_id_ah_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        $daftar_lokasi = AsetLokasiModel::orderby('a_kd_al')->get();
        return view('Sis_aset.Penghapusan.TransferKeluar.Khusus.Fakultas.index',['daftar_kategori'=> $daftar_kategori, 'daftar_lokasi'=> $daftar_lokasi]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = AsetKategoriSubModel::where('a_kd_kt', $request->subKat)->pluck('a_kd_kt_sub','a_nm_kt_sub');
        return response()->json($idSubkategori);
    }

    public function getSub2kategori(Request $request){
        $idSub2kategori = AsetKategoriSub2Model::where('a_kd_kt_sub', $request->sub2Kat)->pluck('a_kd_kt_sub_2','a_nm_kt_sub_2');
        return response()->json($idSub2kategori);
    }

    public function getSub3kategori(Request $request){
        $idSub3kategori = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $request->sub3Kat)->pluck('a_kd_kt_sub_3','a_nm_kt_sub_3');
        return response()->json($idSub3kategori);
    }

    public function getBarang(Request $request){
        $idBarang = AsetBarangModel::where('a_kd_kt_sub_3', $request->sub4Kat)->pluck('a_kd_brg','a_nm_brg');
        return response()->json($idBarang);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $thn = date("Y");
        $thn_kode = substr($tgl, 2,2);
        $bln = substr($tgl, 5,2);
        $user_id = auth()->user()->id;
        
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();  
        
        $datalokasi = AsetLokasiModel::where('id_fk', $datafakultas->id_fk)->first();  
        if($request->nup_akhir < $request->nup_awal)
        {
            return response()->json(['status' => 4]);
        }
        else
        {
            $jumlah_barang = AsetPerolehanModel::
            join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
            ->where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_brg', $request->idBarang)->where('a_status_api', 1)->count();
            if($jumlah_barang==0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {                
                $jumlah_tersedia_oke=0;
                for ($i=$request->nup_awal; $i <= $request->nup_akhir ; $i++) { 
                    $jumlah_tersedia = AsetPerolehanModel::
                    join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
                    ->where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_brg', $request->idBarang)->where('a_no_api',$i)->where('a_status_api', 1)->count();
                    if($jumlah_tersedia==0)
                    {
                        $jumlah_tersedia_oke=1;
                        return response()->json(['status' => 3, 'nuk_ada'=> "NUP $i tidak tersedia"]);            
                    }
                }
                
                if($jumlah_tersedia_oke==0)
                {
                    $jumlah_sppa = AsetHapusModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_ajkt', 'C02')->where('a_thn_ah', $thn)->count();
                    if($jumlah_sppa == 0)
                    {
                        $no_sppa = "1";
                    }
                    else
                    {
                        $no_sppa = $jumlah_sppa + 1;
                    }
                    $kdsppa = "C02$thn_kode$no_sppa";
                    
                    $dataperubahan = new AsetHapusModel();
                    $dataperubahan->a_kd_al = $datalokasi->a_kd_al; 
                    $dataperubahan->a_kd_ajkt = "C02";
                    $dataperubahan->a_kd_brg = $request->idBarang;
                    $dataperubahan->a_kdsppa_ah = $kdsppa;
                    $dataperubahan->a_nosppa_ah = $no_sppa;
                    $dataperubahan->a_jmlh_ah = ($request->nup_akhir - $request->nup_awal) + 1;
                    $dataperubahan->a_no_awal_ah = $request->nup_awal;
                    $dataperubahan->a_no_akhir_ah = $request->nup_akhir;
                    $dataperubahan->a_tgl_buku_ah = $request->tgl_buku;
                    $dataperubahan->a_no_sk_ah = $request->no_sk;
                    $dataperubahan->a_tgl_sk_ah = $request->tgl_sk;
                    $dataperubahan->a_ket_sk_ah = $request->ket_sk;
                    $dataperubahan->a_thn_ah = $thn;
                    $dataperubahan->a_periode_ah = $bln;
                    $dataperubahan->a_kondisi_ah = $request->kondisi;
                    $dataperubahan->a_kd_al_tujuan = $request->idLokasi;
                    $dataperubahan->user_id = $user_id;
                    $dataperubahan->save();
                    $a_id_ah = $dataperubahan->a_id_ah;

                    for ($a=$request->nup_awal; $a <= $request->nup_akhir ; $a++) { 
                        $baris_aset_item = AsetPerolehanModel::
                        join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
                        ->where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_brg', $request->idBarang)->where('a_no_api',$a)->where('a_status_api', 1)->first();
                        
                        $dataperubahanitem = new AsetHapusItemModel();
                        $dataperubahanitem->a_id_ah = $a_id_ah;
                        $dataperubahanitem->a_kd_brg_api = $baris_aset_item->a_kd_brg_api;
                        $dataperubahanitem->a_no_api = $baris_aset_item->a_no_api;
                        $dataperubahanitem->user_id = $user_id;
                        $dataperubahanitem->save();
                        
                        
                        $baris_aset_item_up = AsetPerolehanItemModel::where('a_kd_brg_api',$baris_aset_item->a_kd_brg_api)->first();
                        $baris_aset_item_up->a_status_api = 0;
                        $baris_aset_item_up->save();
                    }
                    return response()->json(['status' => 1]);
                }
            }
        }
    }

    public function destroy(Request $request)
    {      
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $dataperubahanitem = AsetHapusItemModel::where('a_id_ah', $request->a_id_ah)->get();
        foreach($dataperubahanitem as $baris)
        {
            $baris_aset_item_up = AsetPerolehanItemModel::where('a_kd_brg_api',$baris->a_kd_brg_api)->first();
            $baris_aset_item_up->a_status_api = 1;
            $baris_aset_item_up->save();

            $dataperubahanitemdelete = AsetHapusItemModel::where('a_kd_brg_api', $baris->a_kd_brg_api);
            $dataperubahanitemdelete->delete();
        }

        $dataperubahan = AsetHapusModel::where('a_id_ah', $request->a_id_ah)->first();
        $dataperubahan->delete();
        return response()->json(['status' => 1]);
    }
}
