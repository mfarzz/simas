<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\Pembelian;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianKibModel;
use App\Models\AsetPerolehanRincianModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanPembelianRektoratKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   
            
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();

        if(request()->ajax()) {
            return datatables()->of(AsetPerolehanModel::
            join('aset_barang', 'aset_perolehan.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->where('a_kd_ajkt','A02')
            ->where('a_kd_al',$datalokasi->a_kd_al)
            ->get())
            ->addColumn('a_id_ap_en', function ($data) {
                $a_id_ap_en = Crypt::encryptString($data->a_id_ap);
                return $a_id_ap_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        return view('Sis_aset.Perolehan.Pembelian.Khusus.Rektorat.index',['daftar_kategori'=> $daftar_kategori]);
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
        
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)->first();  
        
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();  
        
        $jumlah = AsetPerolehanModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_ajkt', 'A02')->where('a_kd_brg', $request->idBarang)->where('a_tgl_perolehan_ap','>', $request->tgl_perolehan)->count();        
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
                $cek_barang = AsetPerolehanModel::
                join('aset_perolehan_item','aset_perolehan.a_id_ap','=','aset_perolehan_item.a_id_ap')
                ->where('a_kd_al', $datalokasi->a_kd_al)
                ->where('a_kd_brg', $request->idBarang)
                ->orderBy('a_no_api','desc')
                ->first();
                $no_awal_ap = $cek_barang->a_no_api + 1;
            }

            $jumlah_sppa = AsetPerolehanModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_ajkt', 'A02')->where('a_thn_ap', $thn)->count();
            if($jumlah_sppa == 0)
            {
                $no_sppa = "1";
            }
            else
            {
                $no_sppa = $jumlah_sppa + 1;
            }

            $kdsppa = "A02$thn_kode$no_sppa";
            $jumlah_item = $request->jumlah_item;
            $no_akhir_ap = ($jumlah_item + $no_awal_ap) - 1;
            
            $datapembelian = new AsetPerolehanModel();
            $datapembelian->a_kd_al = $datalokasi->a_kd_al;           
            $datapembelian->a_kd_ajkt = "A02";
            $datapembelian->a_kd_brg = $request->idBarang;
            $datapembelian->a_kdsppa_ap = $kdsppa;
            $datapembelian->a_nosppa_ap = $no_sppa;
            $datapembelian->a_jmlh_ap = $jumlah_item;
            $datapembelian->a_no_awal_ap = $no_awal_ap;
            $datapembelian->a_no_akhir_ap = $no_akhir_ap;
            $datapembelian->a_tgl_perolehan_ap = $request->tgl_perolehan;
            $datapembelian->a_tgl_buku_ap = $request->tgl_buku;
            $datapembelian->a_kuantitas_ap = $request->kuantitas;
            $datapembelian->a_nilai_ap = $request->nilai_aset_peritem;
            $datapembelian->a_asal_ap = $request->asal_perolehan;
            $datapembelian->a_no_bukti_ap = $request->no_bukti_perolehan;
            $datapembelian->a_merk_ap = $request->merk_aset;
            $datapembelian->a_ket_ap = $request->ket;
            $datapembelian->a_dsr_hrg_ap = $request->dasar_harga;
            $datapembelian->a_tercatat_ap = $request->tercatat;
            $datapembelian->a_thn_ap = $thn;
            $datapembelian->a_periode_ap = $bln;

            $datapembelian->user_id = $user_id;
            $datapembelian->save(); 
            $a_id_ap = $datapembelian->a_id_ap;

            $datapembelianrincian = new AsetPerolehanRincianModel();
            $datapembelianrincian->a_id_ap = $a_id_ap;
            $datapembelianrincian->a_no_sp2d = $request->no_sp2d;
            $datapembelianrincian->a_tgl_sp2d = $request->tgl_sp2d;
            $datapembelianrincian->a_kl_belanja_apr = $request->kel_belanja;
            $datapembelianrincian->a_nilai_spm = $request->nilai_spm;
            $datapembelianrincian->user_id = $user_id;
            $datapembelianrincian->save();

            $a_no_api = 0;
            for ($i=1; $i <= $jumlah_item ; $i++) { 
                $a_no_api = ($no_awal_ap + $i) - 1;
                $a_kd_brg_api = "$datalokasi->a_kd_al$request->idBarang$a_no_api";
                $datapembelianitem = new AsetPerolehanItemModel();
                $datapembelianitem->a_id_ap = $a_id_ap;
                $datapembelianitem->a_kd_brg_api = $a_kd_brg_api;
                $datapembelianitem->a_no_api = $a_no_api;
                $datapembelianitem->a_kondisi_api = $request->kondisi;
                $datapembelianitem->a_kuantitas_api = $request->kuantitas;
                $datapembelianitem->a_nilai_api = $request->nilai_aset_peritem;
                $datapembelianitem->user_id = $user_id;
                $datapembelianitem->save();
            }
            if($request->tercatat == 3)
            {
                for ($i=1; $i <= $jumlah_item ; $i++) { 
                    $a_no_aprk = ($no_awal_ap + $i) - 1;
                    $a_kd_brg_api = "$datalokasi->a_kd_al$request->idBarang$a_no_aprk";
                    $datapembeliankib = new AsetPerolehanRincianKibModel();
                    $datapembeliankib->a_id_ap = $a_id_ap;
                    $datapembeliankib->a_kd_brg_api = $a_kd_brg_api;
                    $datapembeliankib->a_no_aprk = $a_no_aprk;
                    $datapembeliankib->a_status_proses_aprk = 0;
                    $datapembeliankib->user_id = $user_id;
                    $datapembeliankib->save();
                }
            }
            return response()->json(['status' => 1]);
        }
    }

    public function destroy(Request $request)
    {      
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)->first();  
        
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();

        $datacek = AsetPerolehanModel::
        where('a_id_ap', $request->a_id_ap)->first();

        $jumlah = AsetPerolehanModel::where('a_kd_al', $datalokasi->a_kd_al)->where('a_kd_brg', $datacek->a_kd_brg)->where('a_tgl_perolehan_ap','>', $datacek->a_tgl_perolehan_ap)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 3]);
        }
        else
        {
            $dataperolehan = AsetPerolehanModel::where('a_id_ap', $request->a_id_ap)->first();
            $dataperolehan->delete();
            $dataperolehanitem = AsetPerolehanItemModel::where('a_id_ap', $request->a_id_ap);
            $dataperolehanitem->delete();
            $dataperolehanrincian = AsetPerolehanRincianModel::where('a_id_ap', $request->a_id_ap);
            $dataperolehanrincian->delete();
            return response()->json(['status' => 1]);
        }
    }
}
