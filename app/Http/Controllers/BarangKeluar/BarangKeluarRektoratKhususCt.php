<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BkrnModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use App\Models\VBarangKeluarRektoratModel;
use App\Models\VStokBarangMasukRektoratSemuaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangKeluarRektoratKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bkrn = Crypt::decryptString($encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   
        if(request()->ajax()) {
            return datatables()->of(VBarangKeluarRektoratModel::
            where('id_ur',$datarektorat->id_ur)
            ->where('id_bkrn', $id_bkrn)
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bkrn = BkrnModel::where('id_bkrn',$id_bkrn)->first();
        $daftar_barang = VStokBarangMasukRektoratSemuaModel::where('id_ur', $datarektorat->id_ur)->orderby('nm_brg')->get();
        return view('BarangKeluar.Khusus.Rektorat.index',['encripted_id'=> $encripted_id, 'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bkrn' =>$cek_bkrn]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  

        $idItem = VStokBarangMasukRektoratSemuaModel::where('id_ur', $datarektorat->id_ur)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $id_bkrn = Crypt::decryptString($request->encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  

        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;
        $cek_bkrn = BkrnModel::where('id_bkrn',$id_bkrn)->first();

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
            if($request->id_bkr == "")
            {
                $jumlah_brg = BarangKeluarRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('id_bkrn', $cek_bkrn->id_bkrn)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangKeluarRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('tglambil_bkr','>', $cek_bkrn->tgl_bkrn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokrektorat = BarangMasukRektoratModel::where('id_ur', $id_ur)->where('kd_brg', $idItem)->sum('sisa_bmr');

                        if($request->jumlah > $stokrektorat)
                        {
                            return response()->json(['status' => 2]);
                        }
                        else
                        {                            
                            $databkr = new BarangKeluarRektoratModel();
                            $databkr->id_ur = $id_ur;
                            $databkr->id_bkrn = $id_bkrn;
                            $databkr->kd_brg = $idItem;
                            $databkr->jmlh_bkr = $request->jumlah;
                            $databkr->tglambil_bkr = $cek_bkrn->tgl_bkrn;
                            $databkr->user_id = $user_id;
                            $databkr->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
            else
            {     
                $cekData = BarangKeluarRektoratModel::where('id_bkr', $request->id_bkr)->first();
                if($cekData->kd_brg == $idItem)
                {    
                    $stokrektorat = BarangMasukRektoratModel::where('id_ur', $id_ur)->where('kd_brg', $idItem)->sum('sisa_bmr');

                    if($request->jumlah > $stokrektorat)
                    {
                        return response()->json(['status' => 2]); 
                    }
                    else
                    {
                        $databkr = BarangKeluarRektoratModel::where('id_bkr', $request->id_bkr)->first(); 
                        $databkr->jmlh_bkr = $request->jumlah;
                        $databkr->user_id = $user_id;
                        $databkr->save();
                        return response()->json(['status' => 1]);
                    }
                }
                else
                {
                    $jumlah = BarangKeluarRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('tglambil_bkr','>', $cek_bkrn->tgl_bkrn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokrektorat = BarangMasukRektoratModel::where('id_ur', $id_ur)->where('kd_brg', $idItem)->sum('sisa_bmr');

                        if($request->jumlah > $stokrektorat)
                        {
                            return response()->json(['status' => 2]); 
                        }
                        else
                        {
                            $databkr = BarangKeluarRektoratModel::where('id_bkr', $request->id_bkr)->first(); 
                            $databkr->kd_brg = $idItem;
                            $databkr->jmlh_bkr = $request->jumlah;
                            $databkr->user_id = $user_id;
                            $databkr->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
        }

        
        
    }

    public function edit(Request $request)
    {   
        $data = BarangKeluarRektoratModel::
        join('barang','barang_keluar_rektorat.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bkr',$request->id_bkr)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
            
        $datacek = BarangKeluarRektoratModel::
        where('id_bkr', $request->id_bkr)->first();
        
        $jumlah = BarangKeluarRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $datacek->kd_brg)->where('tglambil_bkr','>', $datacek->tglambil_bkr)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $data = BarangKeluarRektoratModel::where('id_bkr', $request->id_bkr)->first(); 
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}