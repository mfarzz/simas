<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangModel;
use App\Models\BmrspModel;
use App\Models\KategoriModel;
use App\Models\User;
use App\Models\VBarangMasukRumahSakitModel;
use App\Models\VBarangModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class BarangMasukRumahSakitKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bmrsp = Crypt::decryptString($encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VBarangMasukRumahSakitModel::
            where('id_urs',$datarumahsakit->id_urs)
            ->where('id_bmrsp', $id_bmrsp)
            ->orderby('id_bmrs')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bmrsp = BmrspModel::where('id_bmrsp',$id_bmrsp)->first();
        $total_hrg = VBarangMasukRumahSakitModel::where('id_bmrsp',$id_bmrsp)->sum('total_hrg');
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('BarangMasuk.Khusus.RumahSakit.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bmrsp' =>$cek_bmrsp, 'total_hrg' =>$total_hrg]);
    }

    public function getItem(Request $request){
        $idItem = VBarangModel::where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_bmrsp = Crypt::decryptString($request->encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
        ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
        ->where('users.id', $user_id)->first();     
        if($request->idBarang == "")
        {
            $idItem = $request->idItem;
        }
        else
        {
            $idItem = $request->idBarang;
        }
        if($request->idItem == "" and $request->idBarang == "")
        {
            return response()->json(['status' => 5]);
        }
        else
        {
            $cek_bmrsp = BmrspModel::where('id_bmrsp',$id_bmrsp)->first();       
            if($request->id_bmrs == "")
            {
                $jumlah_brg = BarangMasukRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('id_bmrsp', $cek_bmrsp->id_bmrsp)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangMasukRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('tglperolehan_bmrs','>', $request->tgl_perolehan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $databmf = new BarangMasukRumahSakitModel();
                        $databmf->id_bmrsp = $id_bmrsp;
                        $databmf->kd_brg = $idItem;
                        $databmf->id_urs = $datarumahsakit->id_urs;
                        $databmf->kd_lks = $datarumahsakit->kd_lks;
                        $databmf->jmlh_awal_bmrs = $request->jumlah;
                        $databmf->sisa_bmrs = $request->jumlah;
                        $databmf->hrg_bmrs = $request->harga;
                        $databmf->tglperolehan_bmrs = $request->tgl_perolehan;
                        $databmf->tglbuku_bmrs = $cek_bmrsp->tgl_bmrsp;
                        $databmf->user_id = $user_id;
                        $databmf->save();

                        $total_hrg_baru = VBarangMasukRumahSakitModel::where('id_bmrsp', $id_bmrsp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                    }    
                }
            }   
            else
            {
                $cekData = BarangMasukRumahSakitModel::where('id_bmrs', $request->id_bmrs)->first();
                if($cekData->kd_brg == $idItem)
                {
                    $databmf = BarangMasukRumahSakitModel::where('id_bmrs', $request->id_bmrs)->first(); 
                    $databmf->jmlh_awal_bmrs = $request->jumlah;
                    $databmf->sisa_bmrs = $request->jumlah;
                    $databmf->hrg_bmrs = $request->harga;
                    $databmf->tglperolehan_bmrs = $request->tgl_perolehan;
                    $databmf->user_id = $user_id;
                    $databmf->save();
                    $total_hrg_baru = VBarangMasukRumahSakitModel::where('id_bmrsp', $id_bmrsp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                }
                else
                {
                    $jumlah_brg = BarangMasukRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('id_bmrsp', $cek_bmrsp->id_bmrsp)->count();
                    if($jumlah_brg>0)
                    {
                        return response()->json(['status' => 4]);
                    }
                    else
                    {
                        $jumlah = BarangMasukRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('tglperolehan_bmrs','>', $request->tgl_perolehan)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                        else
                        {
                            $databmrs = BarangMasukRumahSakitModel::where('id_bmrs', $request->id_bmrs)->first(); 
                            $databmrs->kd_brg = $idItem;
                            $databmrs->jmlh_awal_bmrs = $request->jumlah;
                            $databmrs->sisa_bmrs = $request->jumlah;
                            $databmrs->hrg_bmrs = $request->harga;
                            $databmrs->tglperolehan_bmrs = $request->tgl_perolehan;
                            $databmrs->user_id = $user_id;
                            $databmrs->save();
                            $total_hrg_baru = VBarangMasukRumahSakitModel::where('id_bmrsp', $id_bmrsp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                        }
                    }
                }
            } 
        }
            
    }

    public function edit(Request $request)
    {   
        $data = BarangMasukRumahSakitModel::
        join('barang','barang_masuk_rumah_sakit.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bmrs',$request->id_bmrs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
        ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
        ->where('users.id', $user_id)->first();  

        $datacek = BarangMasukRumahSakitModel::
        where('id_bmrs', $request->id_bmrs)->first();

        $jumlah = BarangMasukRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $datacek->kd_brg)->where('tglperolehan_bmrs','>', $datacek->tglperolehan_bmrs)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 3]);
        }
        else
        {
            $data = BarangMasukRumahSakitModel::where('id_bmrs', $request->id_bmrs)->first();            
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}
