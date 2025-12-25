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
use App\Models\AsetPerolehanRincianModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanTransferMasukFakultasKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   
            
        $datalokasi = AsetLokasiModel::where('id_fk', $datafakultas->id_fk)->first();

        if(request()->ajax()) {
            return datatables()->of(AsetPerolehanModel::
            join('aset_barang', 'aset_perolehan.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->where('a_kd_ajkt','A03')
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
        return view('Sis_aset.Perolehan.TransferMasuk.Khusus.Fakultas.index',['daftar_kategori'=> $daftar_kategori]);
    }
    
    public function destroy(Request $request)
    {      
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        $datacek = AsetPerolehanModel::
        where('a_id_ap', $request->a_id_ap)->first();
        
        $baris_aset_hapus_up = AsetHapusModel::
        where('a_kd_al',$datacek->a_kd_al_asal)
        ->where('a_kdsppa_ah',$datacek->a_kdsppa_asal)
        ->first();
        $baris_aset_hapus_up->a_kd_al_proses = 0;
        $baris_aset_hapus_up->save();

        $datatransferkeluaritemcek = AsetHapusItemModel::where('a_id_ah', $baris_aset_hapus_up->a_id_ah)->get();
        foreach($datatransferkeluaritemcek as $baris)
        {
            $baris_aset_item_up = AsetPerolehanItemModel::where('a_kd_brg_api',$baris->a_kd_brg_api)->first();
            $baris_aset_item_up->a_status_api = 1;
            $baris_aset_item_up->save();
        }
        $dataperolehan = AsetPerolehanModel::where('a_id_ap', $request->a_id_ap)->first();
        $dataperolehan->delete();
        $dataperolehanitem = AsetPerolehanItemModel::where('a_id_ap', $request->a_id_ap);
        $dataperolehanitem->delete();

        return response()->json(['status' => 1]);
    }
}
