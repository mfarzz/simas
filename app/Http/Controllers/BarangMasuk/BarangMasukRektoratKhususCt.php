<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\BmrpModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VBarangMasukRektoratModel;
use App\Models\VBarangModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class BarangMasukRektoratKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bmrp = Crypt::decryptString($encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VBarangMasukRektoratModel::
            where('id_ur',$datarektorat->id_ur)
            ->where('id_bmrp', $id_bmrp)
            ->orderby('id_bmr')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bmrp = BmrpModel::where('id_bmrp',$id_bmrp)->first();
        $total_hrg = VBarangMasukRektoratModel::where('id_bmrp',$id_bmrp)->sum('total_hrg');
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('BarangMasuk.Khusus.Rektorat.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bmrp' =>$cek_bmrp, 'total_hrg' =>$total_hrg]);
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
        $id_bmrp = Crypt::decryptString($request->encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
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
            $cek_bmrp = BmrpModel::where('id_bmrp',$id_bmrp)->first();       
            if($request->id_bmr == "")
            {
                $jumlah_brg = BarangMasukRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('id_bmrp', $cek_bmrp->id_bmrp)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangMasukRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('tglperolehan_bmr','>', $request->tgl_perolehan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $databmf = new BarangMasukRektoratModel();
                        $databmf->id_bmrp = $id_bmrp;
                        $databmf->kd_brg = $idItem;
                        $databmf->id_ur = $datarektorat->id_ur;
                        $databmf->kd_lks = $datarektorat->kd_lks;
                        $databmf->jmlh_awal_bmr = $request->jumlah;
                        $databmf->sisa_bmr = $request->jumlah;
                        $databmf->hrg_bmr = $request->harga;
                        $databmf->tglperolehan_bmr = $request->tgl_perolehan;
                        $databmf->tglbuku_bmr = $cek_bmrp->tgl_bmrp;
                        $databmf->user_id = $user_id;
                        $databmf->save();

                        $total_hrg_baru = VBarangMasukRektoratModel::where('id_bmrp', $id_bmrp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                    }    
                }
            }   
            else
            {
                $cekData = BarangMasukRektoratModel::where('id_bmr', $request->id_bmr)->first();
                if($cekData->kd_brg == $idItem)
                {
                    $databmf = BarangMasukRektoratModel::where('id_bmr', $request->id_bmr)->first(); 
                    $databmf->jmlh_awal_bmr = $request->jumlah;
                    $databmf->sisa_bmr = $request->jumlah;
                    $databmf->hrg_bmr = $request->harga;
                    $databmf->tglperolehan_bmr = $request->tgl_perolehan;
                    $databmf->user_id = $user_id;
                    $databmf->save();
                    $total_hrg_baru = VBarangMasukRektoratModel::where('id_bmrp', $id_bmrp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                }
                else
                {
                    $jumlah_brg = BarangMasukRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('id_bmrp', $cek_bmrp->id_bmrp)->count();
                    if($jumlah_brg>0)
                    {
                        return response()->json(['status' => 4]);
                    }
                    else
                    {
                        $jumlah = BarangMasukRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $idItem)->where('tglperolehan_bmr','>', $request->tgl_perolehan)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                        else
                        {
                            $databmf = BarangMasukRektoratModel::where('id_bmr', $request->id_bmr)->first(); 
                            $databmf->kd_brg = $idItem;
                            $databmf->jmlh_awal_bmr = $request->jumlah;
                            $databmf->sisa_bmr = $request->jumlah;
                            $databmf->hrg_bmr = $request->harga;
                            $databmf->tglperolehan_bmr = $request->tgl_perolehan;
                            $databmf->user_id = $user_id;
                            $databmf->save();
                            $total_hrg_baru = VBarangMasukRektoratModel::where('id_bmrp', $id_bmrp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                        }
                    }
                }
            } 
        }
            
    }

    public function edit(Request $request)
    {   
        $data = BarangMasukRektoratModel::
        join('barang','barang_masuk_rektorat.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bmr',$request->id_bmr)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)->first();  

        $datacek = BarangMasukRektoratModel::
        where('id_bmr', $request->id_bmr)->first();

        $jumlah = BarangMasukRektoratModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $datacek->kd_brg)->where('tglperolehan_bmr','>', $datacek->tglperolehan_bmr)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 3]);
        }
        else
        {
            $data = BarangMasukRektoratModel::where('id_bmr', $request->id_bmr)->first();            
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}
