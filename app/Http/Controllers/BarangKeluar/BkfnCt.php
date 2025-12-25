<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BkfnModel;
use App\Models\BkpfModel;
use App\Models\FakultasJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BkfnCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(BkfnModel::join('barang_keluar_penerima_fakultas','barang_keluar_fakultas_nota.id_bkpf','=','barang_keluar_penerima_fakultas.id_bkpf')
            ->where('barang_keluar_fakultas_nota.id_fk',$datafakultas->id_fk)
            ->whereYear('barang_keluar_fakultas_nota.tgl_bkfn',$tahun_anggaran)
            ->get())
            ->addColumn('id_bkfn', function ($data) {
                return $data->id_bkfn; 
            })
            ->addColumn('id_bkfn_en', function ($data) {
                $id_bkfn_en = Crypt::encryptString($data->id_bkfn);
                return $id_bkfn_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_penerima = BkpfModel::where('id_fk',$datafakultas->id_fk)->where('status_bkpf',1)->orderby('nm_bkpf')->get();  
        return view('BarangKeluar.Khusus.Fakultas.Nota.index',['daftar_penerima'=>$daftar_penerima]);
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }
    
    public function getPenerima(Request $request){
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $idUnitjabatan = BkpfModel::where('id_fk', $datafakultas->id_fk)->pluck('id_bkpf','nm_bkpf');
        return response()->json($idUnitjabatan);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        $thn_nota = substr($request->tgl_nota,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_nota)
        {
            if($request->id_bkfn == "")
            {
                $jumlah_belum = BkfnModel::where('id_fk', $id_fk)->where('status_bkfn', 0)->count();
                if($jumlah_belum >0)
                {
                    return response()->json(['status' => 5]);
                }
                else
                {                
                    $jumlah = BkfnModel::where('id_fk', $id_fk)->where('no_bkfn', $request->no_nota)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {                    
                        $data = new BkfnModel();
                        $data->id_fk = $id_fk;
                        $data->id_bkpf = $request->penerima;
                        $data->no_bkfn = $request->no_nota;
                        $data->tgl_bkfn = $request->tgl_nota;
                        $data->status_bkfn = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
            else
            {
                $cekData = BkfnModel::where('id_fk', $id_fk)->where('id_bkfn', $request->id_bkfn)->first();
                if($cekData->no_bkfn == $request->no_nota and $cekData->tgl_bkfn == $request->tgl_nota and $cekData->id_bkpf == $request->penerima )
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $jumlah=0;               
                    if($cekData->no_bkfn != $request->no_nota)
                    {
                        $jumlah = BkfnModel::where('id_fk', $id_fk)->where('no_bkfn', $request->no_nota)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]); 
                        }
                    }
                    if($jumlah==0)
                    { 
                        $data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();                   
                        $data->no_bkfn = $request->no_nota;
                        $data->tgl_bkfn = $request->tgl_nota;
                        $data->id_bkpf = $request->penerima;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 4]);
                    }
                }            
            }
        }
        else
        {
            return response()->json(['status' => 6]);
        }
    }

    public function edit(Request $request)
    {   
        $data = BkfnModel::where('id_bkfn',$request->id_bkfn)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;

        $jumlah_bk = BarangKeluarFakultasModel::where('id_bkfn', $request->id_bkfn)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();
            $databarangkeluarfakultas = BarangKeluarFakultasModel::where('id_bkfn', $cek_data->id_bkfn)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangkeluarfakultas as $barisbkf)
            {
                $stoktotal = BarangModel::where('kd_brg', $barisbkf->kd_brg)->sum('stok_brg');
                $proses=0;
                $tnilai_baru=0;
                $jumlah_keluar = $barisbkf->jmlh_bkf;
                $databarangmasukfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $barisbkf->kd_brg)
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
                            $databkfdetail->id_bkf = $barisbkf->id_bkf;
                            $databkfdetail->id_bmf = $barisbmf->id_bmf;
                            $databkfdetail->jmlh_bkfd = $jumlah_keluar;
                            $databkfdetail->user_id = $user_id;
                            $databkfdetail->save();
                            $proses=1;
                            $databarang = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;
                            
                            $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();                                
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
                                $databkdetail->id_bkf = $barisbkf->id_bkf;
                                $databkdetail->id_bmf = $barisbmf->id_bmf;
                                $databkdetail->jmlh_bkfd = $sisabmf;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();
                                $proses=0;
                                $jumlah_keluar = $sisa;

                                $databarang = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;
                                
                                $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();
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
                                $databkdetail->id_bkf = $barisbkf->id_bkf;
                                $databkdetail->id_bmf = $barisbmf->id_bmf;
                                $databkdetail->jmlh_bkfd = $sisabmf;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();                                
                                $proses=1;

                                $databarang = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;

                                $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                                $databmupdateitemnilai->save();
                            }
                        }
                    }
                }
                $databmupdateitem = BarangModel::where('kd_brg', $barisbkf->kd_brg)->first();                   
                $databmupdateitem->stok_brg = $stoktotal - $barisbkf->jmlh_bkf;                    
                $databmupdateitem->save();
            }
            $cek_data->status_bkfn = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}