<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BkrsnModel;
use App\Models\KategoriModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use App\Models\VBarangKeluarRumahSakitModel;
use App\Models\VStokBarangMasukRumahSakitSemuaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangKeluarRumahSakitKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bkrsn = Crypt::decryptString($encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();   
        if(request()->ajax()) {
            return datatables()->of(VBarangKeluarRumahSakitModel::
            where('id_urs',$datarumahsakit->id_urs)
            ->where('id_bkrsn', $id_bkrsn)
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bkrsn = BkrsnModel::where('id_bkrsn',$id_bkrsn)->first();
        $daftar_barang = VStokBarangMasukRumahSakitSemuaModel::where('id_urs', $datarumahsakit->id_urs)->orderby('nm_brg')->get();
        return view('BarangKeluar.Khusus.RumahSakit.index',['encripted_id'=> $encripted_id, 'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bkrsn' =>$cek_bkrsn]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  

        $idItem = VStokBarangMasukRumahSakitSemuaModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $id_bkrsn = Crypt::decryptString($request->encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  

        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;
        $cek_bkrsn = BkrsnModel::where('id_bkrsn',$id_bkrsn)->first();

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
            if($request->id_bkrs == "")
            {
                $jumlah_brg = BarangKeluarRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('id_bkrsn', $cek_bkrsn->id_bkrsn)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangKeluarRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('tglambil_bkrs','>', $cek_bkrsn->tgl_bkrsn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokrumahsakit = BarangMasukRumahSakitModel::where('id_urs', $id_urs)->where('kd_brg', $idItem)->sum('sisa_bmrs');

                        if($request->jumlah > $stokrumahsakit)
                        {
                            return response()->json(['status' => 2]);
                        }
                        else
                        {                            
                            $databkr = new BarangKeluarRumahSakitModel();
                            $databkr->id_urs = $id_urs;
                            $databkr->id_bkrsn = $id_bkrsn;
                            $databkr->kd_brg = $idItem;
                            $databkr->jmlh_bkrs = $request->jumlah;
                            $databkr->tglambil_bkrs = $cek_bkrsn->tgl_bkrsn;
                            $databkr->user_id = $user_id;
                            $databkr->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
            else
            {     
                $cekData = BarangKeluarRumahSakitModel::where('id_bkrs', $request->id_bkrs)->first();
                if($cekData->kd_brg == $idItem)
                {    
                    $stokrumahsakit = BarangMasukRumahSakitModel::where('id_urs', $id_urs)->where('kd_brg', $idItem)->sum('sisa_bmrs');

                    if($request->jumlah > $stokrumahsakit)
                    {
                        return response()->json(['status' => 2]); 
                    }
                    else
                    {
                        $databkr = BarangKeluarRumahSakitModel::where('id_bkrs', $request->id_bkrs)->first(); 
                        $databkr->jmlh_bkrs = $request->jumlah;
                        $databkr->user_id = $user_id;
                        $databkr->save();
                        return response()->json(['status' => 1]);
                    }
                }
                else
                {
                    $jumlah = BarangKeluarRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $idItem)->where('tglambil_bkrs','>', $cek_bkrsn->tgl_bkrsn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokrumahsakit = BarangMasukRumahSakitModel::where('id_urs', $id_urs)->where('kd_brg', $idItem)->sum('sisa_bmrs');

                        if($request->jumlah > $stokrumahsakit)
                        {
                            return response()->json(['status' => 2]); 
                        }
                        else
                        {
                            $databkrs = BarangKeluarRumahSakitModel::where('id_bkrs', $request->id_bkrs)->first(); 
                            $databkrs->kd_brg = $idItem;
                            $databkrs->jmlh_bkr = $request->jumlah;
                            $databkrs->user_id = $user_id;
                            $databkrs->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
        }

        
        
    }

    public function edit(Request $request)
    {   
        $data = BarangKeluarRumahSakitModel::
        join('barang','barang_keluar_rumah_sakit.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bkrs',$request->id_bkrs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  
            
        $datacek = BarangKeluarRumahSakitModel::
        where('id_bkrs', $request->id_bkrs)->first();
        
        $jumlah = BarangKeluarRumahSakitModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $datacek->kd_brg)->where('tglambil_bkrs','>', $datacek->tglambil_bkrs)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $data = BarangKeluarRumahSakitModel::where('id_bkrs', $request->id_bkrs)->first(); 
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}