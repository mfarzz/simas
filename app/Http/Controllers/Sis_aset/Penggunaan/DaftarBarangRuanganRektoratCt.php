<?php

namespace App\Http\Controllers\Sis_aset\Penggunaan;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetDaftarRuanganRektoratDetailModel;
use App\Models\AsetDaftarRuanganRektoratModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetRuanganFakultasModel;
use App\Models\AsetRuanganRektoratModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class DaftarBarangRuanganRektoratCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(AsetDaftarRuanganRektoratModel::
            join('aset_barang', 'aset_daftar_ruangan_rektorat.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->join('aset_ruangan_rektorat', 'aset_daftar_ruangan_rektorat.a_id_arr','=','aset_ruangan_rektorat.a_id_arr')
            ->where('id_ur',$datarektorat->id_ur)
            ->get())
            ->addColumn('a_id_adrr_en', function ($data) {
                $a_id_adrr_en = Crypt::encryptString($data->a_id_adrr);
                return $a_id_adrr_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        $daftar_ruangan = AsetRuanganRektoratModel::where('id_ur', $datarektorat->id_ur)->orderby('a_nm_arr')->get();
        return view('Sis_aset.Penggunaan.DaftarRuangan.Rektorat.index',['daftar_kategori'=> $daftar_kategori, 'daftar_ruangan'=> $daftar_ruangan]);
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
        $user_id = auth()->user()->id;
        
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();    
        
        $jumlah_barang = AsetDaftarRuanganRektoratDetailModel::join("aset_daftar_ruangan_rektorat",'aset_daftar_ruangan_rektorat_detail.a_id_adrr','=', 'aset_daftar_ruangan_rektorat.a_id_adrr')
        ->where('a_kd_brg', $request->idBarang)
        ->whereBetween('a_kd_brg_api', [$request->no_urut_awal, $request->no_urut_akhir])
        ->count();

        $a_jmlh_arf = ($request->no_urut_akhir - $request->no_urut_awal) + 1;

        if($jumlah_barang == 0)
        {
            $datadaftar = new AsetDaftarRuanganRektoratModel();
            $datadaftar->a_kd_brg = $request->idBarang;
            $datadaftar->a_id_arr = $request->idRuangan;
            $datadaftar->a_jmlh_arr = $a_jmlh_arf;
            $datadaftar->a_no_awal_adrr = $request->no_urut_awal;
            $datadaftar->a_no_akhir_adrr = $request->no_urut_akhir;
            $datadaftar->user_id = $user_id;
            $datadaftar->save();
            $a_id_adrr = $datadaftar->a_id_adrr;

            for ($i=1; $i <= $a_jmlh_arf ; $i++) {
                $a_kd_brg_api = "$request->idBarang$i";
                $datadaftardetail = new AsetDaftarRuanganRektoratDetailModel();
                $datadaftardetail->a_id_adrr = $a_id_adrr;
                $datadaftardetail->a_kd_brg_api = $a_kd_brg_api;
                $datadaftardetail->user_id = $user_id;
                $datadaftardetail->save();
            }
            return response()->json(['status' => 1]);
        }
        else
        {
            return response()->json(['status' => 2]);  
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

        $datadaftar = AsetDaftarRuanganRektoratModel::where('a_id_adrr', $request->a_id_adrr)->first();
        $datadaftar->delete();
        $datadaftardetail = AsetDaftarRuanganRektoratDetailModel::where('a_id_adrr', $request->a_id_adrr);
        $datadaftardetail->delete();
        return response()->json(['status' => 1]);      
    }
}
