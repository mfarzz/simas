<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk;

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
use App\Models\FakultasModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanTransferMasukRektoratKhususCekCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   
            
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();

        if(request()->ajax()) {
            return datatables()->of(AsetHapusModel::
            join('aset_barang', 'aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->join('aset_lokasi', 'aset_hapus.a_kd_al_tujuan','=','aset_lokasi.a_kd_al')
            ->where('a_kd_ajkt','C02')
            ->where('aset_hapus.a_kd_al_tujuan',$datalokasi->a_kd_al)
            ->where('aset_hapus.a_kd_al_proses',0)
            ->get())
            ->addColumn('a_id_ah_en', function ($data) {
                $a_id_ah_en = Crypt::encryptString($data->a_id_ah);
                return $a_id_ah_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_fakultas = FakultasModel::orderby('nm_fk')->get();
        $daftar_kategori = AsetKategoriModel::get();
        $daftar_kategori_sub = AsetKategoriSubModel::get();
        $daftar_kategori_sub_2 = AsetKategoriSub2Model::get();
        $daftar_kategori_sub_3 = AsetKategoriSub3Model::get();
        $daftar_barang = AsetBarangModel::get();
        return view('Sis_aset.Perolehan.TransferMasuk.Khusus.Rektorat.CekMasuk.index',['daftar_fakultas'=>$daftar_fakultas,'daftar_kategori'=>$daftar_kategori, 'daftar_kategori_sub'=>$daftar_kategori_sub, 'daftar_kategori_sub_2'=>$daftar_kategori_sub_2, 'daftar_kategori_sub_3'=>$daftar_kategori_sub_3, 'daftar_barang'=>$daftar_barang]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $thn = date("Y");
        $thn_kode = substr($tgl, 2,2);
        $bln = substr($tgl, 5,2);
        $user_id = auth()->user()->id;
        
        $datarektorat = User::join('unit_rektorat','users.id_urj','=','unit_rektorat_jabatan.id_fkj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)->first();  
        
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();          
        $jumlah = AsetPerolehanModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_ajkt', 'A03')->where('a_kd_brg', $request->idBarang)->count();        
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {   
            
            $jumlah_barang = AsetPerolehanModel::
            join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
            ->where('a_kd_al', $datalokasi->a_kd_al)
            ->where('a_kd_brg', $request->idBarang)
            ->count();
            if($jumlah_barang == 0)
            {
                $no_awal_ap = "1";
            }
            else
            {                
                $cek_barang = AsetPerolehanModel::join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
                ->where('a_kd_al', $datalokasi->a_kd_al)
                ->where('a_kd_brg', $request->idBarang)
                ->orderBy('a_no_api','desc')
                ->first();
                $no_awal_ap = $cek_barang->a_no_api + 1;
            }

            $jumlah_sppa = AsetPerolehanModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_ajkt', 'A03')->where('a_thn_ap', $thn)->count();
            if($jumlah_sppa == 0)
            {
                $no_sppa = "1";
            }
            else
            {
                $no_sppa = $jumlah_sppa + 1;
            }

            $datatransferkeluar = AsetHapusModel::where('a_id_ah', $request->a_id_ah)->first();
            $datatransferkeluaritem = AsetHapusItemModel::
            join('aset_perolehan_item','aset_hapus_item.a_kd_brg_api','=','aset_perolehan_item.a_kd_brg_api')
            ->where('a_id_ah', $request->a_id_ah)->first();

            $dataperolehan = AsetPerolehanModel::where('a_id_ap', $datatransferkeluaritem->a_id_ap)->first();

            $kdsppa = "A03$thn_kode$no_sppa";
            $jumlah_item = $datatransferkeluar->a_jmlh_ah;
            $no_akhir_ap = ($jumlah_item + $no_awal_ap) - 1;
            
            $datatransfermasuk = new AsetPerolehanModel();
            $datatransfermasuk->a_kd_al = $datalokasi->a_kd_al;           
            $datatransfermasuk->a_kd_ajkt = "A03";
            $datatransfermasuk->a_kd_brg = $request->idBarang;
            $datatransfermasuk->a_kdsppa_ap = $kdsppa;
            $datatransfermasuk->a_nosppa_ap = $no_sppa;
            $datatransfermasuk->a_jmlh_ap = $jumlah_item;
            $datatransfermasuk->a_no_awal_ap = $no_awal_ap;
            $datatransfermasuk->a_no_akhir_ap = $no_akhir_ap;
            $datatransfermasuk->a_tgl_perolehan_ap = $dataperolehan->a_tgl_perolehan_ap;
            $datatransfermasuk->a_tgl_buku_ap = $request->tgl_buku;
            $datatransfermasuk->a_kuantitas_ap = $datatransferkeluaritem->a_kuantitas_api;
            $datatransfermasuk->a_nilai_ap = $datatransferkeluaritem->a_nilai_api;
            $datatransfermasuk->a_asal_ap = $request->asal_perolehan;
            $datatransfermasuk->a_no_bukti_ap = $request->no_bukti_perolehan;
            $datatransfermasuk->a_merk_ap = $dataperolehan->a_merk_ap;
            $datatransfermasuk->a_ket_ap = $request->ket;
            $datatransfermasuk->a_dsr_hrg_ap = $dataperolehan->a_dsr_hrg_ap;
            $datatransfermasuk->a_tercatat_ap = $dataperolehan->a_tercatat_ap;
            $datatransfermasuk->a_thn_ap = $dataperolehan->a_thn_ap;
            $datatransfermasuk->a_periode_ap = $dataperolehan->a_periode_ap;
            $datatransfermasuk->a_tgl_bast_ap = $request->tgl_bast;
            $datatransfermasuk->a_kd_al_asal = $datatransferkeluar->a_kd_al;
            $datatransfermasuk->a_kdsppa_asal = $datatransferkeluar->a_kdsppa_ah;
            $datatransfermasuk->user_id = $user_id;
            $datatransfermasuk->save(); 
            $a_id_ap = $datatransfermasuk->a_id_ap;
            
            $a_no_api = 0;
            for ($i=1; $i <= $jumlah_item ; $i++) { 
                $a_no_api = ($no_awal_ap + $i) - 1;
                $a_kd_brg_api = "$datalokasi->a_kd_al$request->idBarang$a_no_api";
                $datahibahitem = new AsetPerolehanItemModel();
                $datahibahitem->a_id_ap = $a_id_ap;
                $datahibahitem->a_kd_brg_api = $a_kd_brg_api;
                $datahibahitem->a_no_api = $a_no_api;
                $datahibahitem->a_kuantitas_api = $datatransferkeluaritem->a_kuantitas_api;
                $datahibahitem->a_nilai_api = $datatransferkeluaritem->a_nilai_api;
                $datahibahitem->a_kondisi_api = $datatransferkeluaritem->a_kondisi_api;
                $datahibahitem->user_id = $user_id;
                $datahibahitem->save();
            }
            $datatransferkeluaritemcek = AsetHapusItemModel::where('a_id_ah', $request->a_id_ah)->get();
            foreach($datatransferkeluaritemcek as $baris)
            {
                $baris_aset_item_up = AsetPerolehanItemModel::where('a_kd_brg_api',$baris->a_kd_brg_api)->first();
                $baris_aset_item_up->a_status_api = 0;
                $baris_aset_item_up->save();
            }
            $baris_aset_hapus_up = AsetHapusModel::where('a_id_ah',$request->a_id_ah)->first();
            $baris_aset_hapus_up->a_kd_al_proses = 1;
            $baris_aset_hapus_up->save();
            return response()->json(['status' => 1]);
        }
    }

    public function edit(Request $request)
    {   
        $data = AsetHapusModel::join('aset_barang', 'aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->where('a_id_ah',$request->a_id_ah)->first();
        return Response()->json($data);
    }
}