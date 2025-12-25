<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\FakultasJabatanModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VBarangKeluarFakultasModel;
use App\Models\VStokBarangMasukFakultasSemuaModel;
use App\Models\VStokBarangMasukModel;
use App\Models\VStokBarangMasukSemuaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangKeluarFakultasKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bkfn = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   
        if(request()->ajax()) {
            return datatables()->of(VBarangKeluarFakultasModel::
            where('id_fk',$datafakultas->id_fk)
            ->where('id_bkfn', $id_bkfn)
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();        
        return view('BarangKeluar.Khusus.Fakultas.index',['encripted_id'=> $encripted_id, 'daftar_kategori'=> $daftar_kategori]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $idItem = VStokBarangMasukFakultasSemuaModel::where('id_fk', $datafakultas->id_fk)->where('kd_sskt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $stok = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->sum('sisa_bmf');
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;

        $jumlah = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->where('tglambil_bkf','>', $request->tgl_keluar)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 3]);
        }
        else
        {
            $stokfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $request->idItem)->sum('sisa_bmf');
            $stoktotal = BarangModel::where('kd_brg', $request->idItem)->sum('stok_brg');
                    
            if($request->jumlah > $stokfakultas)
            {
                return response()->json(['status' => 2]); 
            }
            else
            {
                $databkr = new BarangKeluarFakultasModel();
                $databkr->id_fk = $id_fk;
                $databkr->nm_penerima = $request->nm_penerima;             
                $databkr->kd_brg = $request->idItem;
                $databkr->jmlh_bkf = $request->jumlah;
                $databkr->tglambil_bkf = $request->tgl_keluar;
                $databkr->user_id = $user_id;
                $databkr->save();
                $id_bkf = $databkr->id_bkf;

                $totalbkf=0;
                $proses=0;
                $tnilai_baru=0;
                $jumlah_keluar = $request->jumlah;
                $databarangmasukfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $request->idItem)
                ->where('sisa_bmf',  '!=', 0)
                ->orderBy('tglperolehan_bmf','asc')
                ->get();
                foreach($databarangmasukfakultas as $barisbmf)
                {
                    $sisabmf = $barisbmf->sisa_bmf;
                    if($jumlah_keluar <= $sisabmf)
                    {
                        $sisa = $sisabmf - $jumlah_keluar;                            
                        if($proses==0)
                        {
                            $nilai_baru = $barisbmf->hrg_bmf * $jumlah_keluar;
                            $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                            $databmupdate->sisa_bmf = $sisa;
                            $databmupdate->save();

                            $databkfdetail = new BarangKeluarFakultasDetailModel();
                            $databkfdetail->id_bkf = $id_bkf;
                            $databkfdetail->id_bmf = $barisbmf->id_bmf;
                            $databkfdetail->jmlh_bkfd = $jumlah_keluar;
                            $databkfdetail->user_id = $user_id;
                            $databkfdetail->save();
                            $proses=1;
                            $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;

                            $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $tnilai_baru = $nilai_baru + $tnilai_baru;
                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $tnilai_baru;
                            $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                            $databmupdateitemnilai->save();
                        }
                    }                        
                    else
                    {
                        if($proses==0)
                        {
                            $sisa = $jumlah_keluar - $sisabmf;
                            if($sisa >= 0)
                            {          
                                $nilai_baru = $barisbmf->hrg_bmf * $sisabmf;
                                $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                $databmupdate->sisa_bmf = 0;                            
                                $databmupdate->save();

                                $databkdetail = new BarangKeluarFakultasDetailModel();
                                $databkdetail->id_bkf = $id_bkf;
                                $databkdetail->id_bmf = $barisbmf->id_bmf;
                                $databkdetail->jmlh_bkfd = $sisabmf;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();
                                $proses=0;
                                $jumlah_keluar = $sisa;

                                $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;
                                
                                $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                                $databmupdateitemnilai->save();
                            }
                            else
                            {
                                $sisa = $sisabmf - $jumlah_keluar;
                                $nilai_baru = $barisbmf->hrg_bmf * $jumlah_keluar;
                                $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                $databmupdate->sisa_bmf = $sisa;                            
                                $databmupdate->save();

                                $databkdetail = new BarangKeluarFakultasDetailModel();
                                $databkdetail->id_bkf = $id_bkf;
                                $databkdetail->id_bmf = $barisbmf->id_bmf;
                                $databkdetail->jmlh_bkfd = $sisabmf;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();                                
                                $proses=1;

                                $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;

                                $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                                $databmupdateitemnilai->save();
                            }
                        }
                    }
                }
                $databmupdateitem = BarangModel::where('kd_brg', $request->idItem)->first();                   
                $databmupdateitem->stok_brg = $stoktotal - $request->jumlah;                    
                $databmupdateitem->save();
                return response()->json(['status' => 1]);
            }
        }
    }
    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
            
        $datacek = BarangKeluarFakultasModel::
        where('id_bkf', $request->id_bkf)->first();
        
        $jumlah = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $datacek->kd_brg)->where('tglambil_bkf','>', $datacek->tglambil_bkf)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $tnilai_baru=0;
            $databkfd = BarangKeluarFakultasDetailModel::where('id_bkf', $request->id_bkf)->get();
            foreach($databkfd as $barisbkfd)
            {                
                $databmf = BarangMasukFakultasModel::where('id_bmf', $barisbkfd->id_bmf)->first();
                $stok_skrg = $databmf->sisa_bmf + $barisbkfd->jmlh_bkfd;
                $nilai_baru = $barisbkfd->jmlh_bkfd * $databmf->hrg_bmf;

                $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbkfd->id_bmf)->first();                   
                $databmupdate->sisa_bmf = $stok_skrg;                            
                $databmupdate->save();

                $datadeletebkd = BarangKeluarFakultasDetailModel::where('id_bkfd', $barisbkfd->id_bkfd)->first();
                $datadeletebkd->delete();                  
                $tnilai_baru = $nilai_baru + $tnilai_baru;
            }
            $data = BarangKeluarFakultasModel::where('id_bkf', $request->id_bkf)->first(); 
            $jmlh_bkf = $data->jmlh_bkf;
            
            $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
            $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bkf;
            $databmupdateitem->nilai_brg = $databmupdateitem->nilai_brg + $tnilai_baru;
            $databmupdateitem->save();

            $data->delete();

            return response()->json(['status' => 1]);
        }
    }
}