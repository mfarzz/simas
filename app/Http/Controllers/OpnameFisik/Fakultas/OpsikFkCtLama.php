<?php

namespace App\Http\Controllers\OpnameFisik\Fakultas;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\FakultasJabatanModel;
use App\Models\OpfkakModel;
use App\Models\OpfkbkfModel;
use App\Models\OpfkbmfModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpfkopModel;
use App\Models\OpsikFakultasModel;
use App\Models\OpsikFkDetModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikFkCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(OpsikFakultasModel::
            where('opsik_fakultas.id_fk',$datafakultas->id_fk)
            ->whereYear('opsik_fakultas.tgl_opfk',$tahun_anggaran)
            ->get())
            ->addColumn('id_opfk', function ($data) {
                return $data->id_opfk; 
            })
            ->addColumn('id_opfk_en', function ($data) {
                $id_opfk_en = Crypt::encryptString($data->id_opfk);
                return $id_opfk_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('OpnameFisik.Fakultas.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($request->id_opfk == "")
        {     
            $jumlah_belum = OpsikFakultasModel::where('id_fk', $id_fk)->where('status_opfk', 0)->count();
            if($jumlah_belum >0)
            {                
                return response()->json(['status' => 5]);
            }
            else
            {   
                
                $jumlah = OpsikFakultasModel::where('id_fk', $id_fk)->where('thn_opfk', $request->tahun)->where('sem_opfk', $request->semester)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {                    
                    
                    $data = new OpsikFakultasModel();
                    $data->id_fk = $id_fk;
                    $data->no_opfk = $request->no_opfk;
                    $data->tgl_opfk = $request->tgl_opfk;
                    $data->sem_opfk = $request->semester;
                    $data->thn_opfk = $request->tahun;
                    $data->status_opfk = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = OpsikFakultasModel::where('id_fk', $id_fk)->where('id_opfk', $request->id_opfk)->first();
            if($cekData->no_opfk == $request->no_opfk and $cekData->tgl_opfk == $request->tgl_opfk and $cekData->sem_opfk == $request->semester and $cekData->thn_opfk == $request->tahun )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->sem_opfk != $request->semester)
                {
                    $jumlah = OpsikFakultasModel::where('id_fk', $id_fk)->where('sem_opfk', $request->semester)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($cekData->thn_opfk != $request->tahun)
                {
                    $jumlah = OpsikFakultasModel::where('id_fk', $id_fk)->where('thn_opfk', $request->tahun)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = OpsikFakultasModel::where('id_opfk', $request->id_opfk)->first();
                    $data->no_opfk = $request->no_opfk;
                    $data->tgl_opfk = $request->tgl_opfk;
                    $data->sem_opfk = $request->semester;
                    $data->thn_opfk = $request->tahun;
                    $data->status_opfk = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = OpsikFakultasModel::where('id_opfk',$request->id_opfk)->first();
        return Response()->json($data);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;        
        $jumlah_opfk = OpsikFkDetModel::where('id_opfk', $request->id_opfk)->count();        
        if($jumlah_opfk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {   
            $dataopsikdetail = OpsikFkDetModel::where('id_opfk', $request->id_opfk)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($dataopsikdetail as $barisopfk)
            {
                $t_jmlh_bmf = 0;
                $t_harga_bmf = 0;
                $databmf = BarangMasukFakultasModel::where('id_fk', $id_fk)
                ->where('kd_brg','=',$barisopfk->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databmf as $barisbmf)
                {
                    $hrg_bmf = $barisbmf->jmlh_awal_bmf * $barisbmf->hrg_bmf;
                    $t_harga_bmf = $t_harga_bmf + $hrg_bmf;
                    $t_jmlh_bmf = $barisbmf->jmlh_awal_bmf + $t_jmlh_bmf;
                }
                $data = new OpfkbmfModel();
                $data->id_opfkdet = $barisopfk->id_opfkdet;
                $data->jmlh_opfkbmf = $t_jmlh_bmf;
                $data->total_opfkbmf = $t_harga_bmf;
                $data->user_id = $user_id;
                $data->save();

                $t_jmlh_bkf = 0;
                $t_harga_bkf = 0;
                $databkf = BarangKeluarFakultasModel::where('id_fk', $id_fk)
                ->where('kd_brg','=',$barisopfk->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databkf as $barisbkf)
                {
                    $t_jmlh_bkfd = 0;
                    $t_harga_bkfd = 0;
                    $databkfd = BarangKeluarFakultasDetailModel::
                    join('barang_masuk_fakultas','barang_keluar_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
                    ->where('id_bkf', $barisbkf->id_bkf)
                    ->orderBy('barang_keluar_fakultas_detail.id_bmf','asc')
                    ->get();
                    foreach($databkfd as $barisbkfd)
                    {
                        $hrg_bkfd = $barisbkfd->jmlh_bkfd * $barisbkfd->hrg_bmf;
                        $t_harga_bkfd = $t_harga_bkfd + $hrg_bkfd;
                        $t_jmlh_bkfd = $barisbkfd->jmlh_bkfd + $t_jmlh_bkfd;
                    }
                    $t_jmlh_bkf = $t_jmlh_bkf + $t_jmlh_bkfd;
                    $t_harga_bkf = $t_harga_bkf + $t_harga_bkfd;
                }
                
                $dataopfkbkf = new OpfkbkfModel();
                $dataopfkbkf->id_opfkdet = $barisopfk->id_opfkdet;
                $dataopfkbkf->jmlh_opfkbkf = $t_jmlh_bkf;
                $dataopfkbkf->total_opfkbkf = $t_harga_bkf;
                $dataopfkbkf->user_id = $user_id;
                $dataopfkbkf->save();

                $stok_sistem_opfkdet = $barisopfk->stok_sistem_opfkdet;
                $stok_opsik_opfkdet = $barisopfk->stok_opsik_opfkdet;                
                $jumlahbmf = BarangMasukFakultasModel::where('id_fk','=',$id_fk)->where('kd_brg','=',$barisopfk->kd_brg)->where('sisa_bmf','>',0)->orderBy('tglperolehan_bmf','asc')->count();                
                if($stok_sistem_opfkdet < $stok_opsik_opfkdet) // kecil
                {
                    if($jumlahbmf==0)
                    {
                        $barisbmf = BarangMasukFakultasModel::where('id_fk','=',$id_fk)->where('kd_brg','=',$barisopfk->kd_brg)->orderBy('tglperolehan_bmf','desc')->first();
                        $selisih = $stok_opsik_opfkdet - $stok_sistem_opfkdet;                    
                        $data = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();
                        $data->jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf + $selisih;
                        $data->sisa_bmf = $barisbmf->sisa_bmf + $selisih;
                        $data->save();  
                    }
                    else
                    {
                        $tnilai_baru=0;
                        $proses=0;            
                        $selisih = $stok_opsik_opfkdet - $stok_sistem_opfkdet ;
                        $bariscekbmf = BarangKeluarFakultasDetailModel::
                        join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                        ->where('id_fk','=',$id_fk)->where('kd_brg','=',$barisopfk->kd_brg)->orderBy('id_bmf','desc')->get();
                        foreach($bariscekbmf as $barisbmf)
                        {
                            $jmlh_bkfd = $barisbmf->jmlh_bkfd;
                            if($selisih <= $jmlh_bkfd)
                            {
                                $sisa = $jmlh_bkfd - $selisih;                       
                                if($proses==0)
                                {                                    
                                    $datacekbmf = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();

                                    $nilai_baru = $datacekbmf->hrg_bmf * $selisih;

                                    $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                    $databmupdate->sisa_bmf = $selisih + $datacekbmf->sisa_bmf;
                                    $databmupdate->save();
                                    
                                    $dataopfdetail = new OpfkdetitmModel();
                                    $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                    $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                    $dataopfdetail->id_bkfd = $barisbmf->id_bkfd;
                                    $dataopfdetail->jmlh_opfkdetitm = $selisih;
                                    $dataopfdetail->user_id = $user_id;
                                    $dataopfdetail->save();
                                    $proses=1;
                                    $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                    $tnilai_baru = $nilai_baru + $tnilai_baru;
                                    $databmupdateitemnilai->nilai_brg = $nilai_terakhir + $tnilai_baru;
                                    $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $selisih;
                                    $databmupdateitemnilai->save();
                                }
                            }
                            else
                            {
                                if($proses==0)
                                {
                                    $sisa = $selisih - $jmlh_bkfd;
                                    if($sisa >= 0)
                                    {   
                                        $datacekbmf = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();

                                        $nilai_baru = $datacekbmf->hrg_bmf * $jmlh_bkfd;

                                        $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                        $databmupdate->sisa_bmf = $jmlh_bkfd + $datacekbmf->sisa_bmf;
                                        $databmupdate->save();
        
                                        $dataopfdetail = new OpfkdetitmModel();
                                        $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                        $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                        $dataopfdetail->id_bkfd = $barisbmf->id_bkfd;
                                        $dataopfdetail->jmlh_opfkdetitm = $jmlh_bkfd;
                                        $dataopfdetail->user_id = $user_id;
                                        $dataopfdetail->save();
                                        $proses=0;
                                        $selisih = $sisa;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir + $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_bkfd;
                                        $databmupdateitemnilai->save();
                                    }
                                    else
                                    {
                                        $sisa = $jmlh_bkfd - $selisih;
                                        $datacekbmf = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();
                                        $nilai_baru = $datacekbmf->hrg_bmf * $selisih;
                                        
                                        $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                        $databmupdate->sisa_bmf = $jmlh_bkfd + $datacekbmf->sisa_bmf;                            
                                        $databmupdate->save();
        
                                        $dataopfdetail = new OpfkdetitmModel();
                                        $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                        $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                        $dataopfdetail->id_bkfd = $barisbmf->id_bkfd;
                                        $dataopfdetail->jmlh_opfkdetitm = $selisih;
                                        $dataopfdetail->user_id = $user_id;
                                        $dataopfdetail->save();                                
                                        $proses=1;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir + $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $selisih;
                                        $databmupdateitemnilai->save();
                                    }
                                }
                            }
                        }
                    }                                     
                }
                else
                {
                    if($stok_sistem_opfkdet == $stok_opsik_opfkdet) // kecil
                    {
                        /*$proses=0;            
                        $tnilai_baru=0;
                        $bariscekbmf = BarangMasukFakultasModel::where('id_fk','=',$id_fk)->where('kd_brg','=',$barisopfk->kd_brg)->where('sisa_bmf','>',0)->orderBy('tglperolehan_bmf','asc')->get();
                        foreach($bariscekbmf as $barisbmf)
                        {
                            $dataopfdetail = new OpfkdetitmModel();
                            $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                            $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                            $dataopfdetail->jmlh_opfkdetitm =$barisbmf->sisa_bmf;
                            $dataopfdetail->user_id = $user_id;
                            $dataopfdetail->save();
                        }*/
                    }
                    else
                    {
                        if($jumlahbmf==0)
                        {
                             
                        }
                        else
                        {            
                            $proses=0;            
                            $tnilai_baru=0;
                            $selisih = $stok_sistem_opfkdet - $stok_opsik_opfkdet ;
                            $bariscekbmf = BarangMasukFakultasModel::where('id_fk','=',$id_fk)->where('kd_brg','=',$barisopfk->kd_brg)->where('sisa_bmf','>',0)->orderBy('tglperolehan_bmf','asc')->get();
                            foreach($bariscekbmf as $barisbmf)
                            {                      
                                $sisabmf = $barisbmf->sisa_bmf;
                                if($selisih <= $sisabmf)
                                {
                                    $sisa = $sisabmf - $selisih;                            
                                    if($proses==0)
                                    {
                                        $nilai_baru = $barisbmf->hrg_bmf * $selisih;
                                        $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                        $databmupdate->sisa_bmf = $sisa;
                                        $databmupdate->save();
                                        
                                        $dataopfdetail = new OpfkdetitmModel();
                                        $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                        $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                        $dataopfdetail->jmlh_opfkdetitm = $selisih;
                                        $dataopfdetail->user_id = $user_id;
                                        $dataopfdetail->save();
                                        $proses=1;
                                        $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                        $tnilai_baru = $nilai_baru + $tnilai_baru;
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $tnilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $selisih;
                                        $databmupdateitemnilai->save();
                                    }
                                }                        
                                else
                                {
                                    if($proses==0)
                                    {
                                        $sisa = $selisih - $sisabmf;
                                        if($sisa >= 0)
                                        {          
                                            $nilai_baru = $barisbmf->hrg_bmf * $sisabmf;
                                            $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                            $databmupdate->sisa_bmf = 0;                            
                                            $databmupdate->save();
            
                                            $dataopfdetail = new OpfkdetitmModel();
                                            $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                            $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                            $dataopfdetail->jmlh_opfkdetitm = $sisabmf;
                                            $dataopfdetail->user_id = $user_id;
                                            $dataopfdetail->save();
                                            $proses=0;
                                            $selisih = $sisa;
            
                                            $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                            $nilai_terakhir = $databarang->nilai_brg;
                                            
                                            $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();
                                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                            $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $sisabmf;
                                            $databmupdateitemnilai->save();
                                        }
                                        else
                                        {
                                            $sisa = $sisabmf - $selisih;
                                            $nilai_baru = $barisbmf->hrg_bmf * $selisih;
                                            $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                            $databmupdate->sisa_bmf = $sisa;                            
                                            $databmupdate->save();
            
                                            $dataopfdetail = new OpfkdetitmModel();
                                            $dataopfdetail->id_opfkdet = $barisopfk->id_opfkdet;
                                            $dataopfdetail->id_bmf = $barisbmf->id_bmf;
                                            $dataopfdetail->jmlh_opfkdetitm = $selisih;
                                            $dataopfdetail->user_id = $user_id;
                                            $dataopfdetail->save();                                
                                            $proses=1;
            
                                            $databarang = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();                                
                                            $nilai_terakhir = $databarang->nilai_brg;
            
                                            $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();
                                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                            $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $selisih;
                                            $databmupdateitemnilai->save();
                                        }
                                    }
                                }


                            }
                        }
                    }
                    
                }
                $jumlahop = OpfkdetitmModel::
                join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                ->where('id_opfkdet', $barisopfk->id_opfkdet)
                ->orderBy('opfik_fakultas_detail_item.id_bmf','asc')
                ->count();
                if($jumlahop==0)
                {
                    $dataop = new OpfkopModel();
                    $dataop->id_opfkdet = $barisopfk->id_opfkdet;
                    $dataop->jmlh_opfkop = 0;
                    $dataop->total_opfkop = 0;
                    $dataop->status_opfkop = 3;                    
                    $dataop->user_id = $user_id;
                    $dataop->save();
                }
                else
                {                
                    $t_jmlh_op = 0;
                    $t_harga_op = 0;
                    $t_bkfd = 0;
                    $dataop = OpfkdetitmModel::
                    join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                    ->where('id_opfkdet', $barisopfk->id_opfkdet)
                    ->orderBy('opfik_fakultas_detail_item.id_bmf','asc')
                    ->get();
                    foreach($dataop as $barisop)
                    {
                        $t_bkfd =$barisop->id_bkfd;
                        $hrg_op = $barisop->jmlh_opfkdetitm * $barisop->hrg_bmf;
                        $t_harga_op = $t_harga_op + $hrg_op;
                        $t_jmlh_op = $barisop->jmlh_opfkdetitm + $t_jmlh_op;
                    }
                    
                    $dataop = new OpfkopModel();
                    $dataop->id_opfkdet = $barisopfk->id_opfkdet;
                    $dataop->jmlh_opfkop = $t_jmlh_op;
                    $dataop->total_opfkop = $t_harga_op;
                    if($t_bkfd > 0)
                    {
                        $dataop->status_opfkop = 1;
                    }
                    else
                    {
                        $dataop->status_opfkop = 2;
                    }
                    $dataop->user_id = $user_id;
                    $dataop->save();
                }

                $t_jmlh_ak = 0;
                $t_harga_ak = 0;
                $dataak = BarangMasukFakultasModel::where('id_fk', $id_fk)
                ->where('kd_brg','=',$barisopfk->kd_brg)
                ->where('sisa_bmf','>',0)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($dataak as $barisak)
                {
                    $hrg_ak = $barisak->sisa_bmf * $barisak->hrg_bmf;
                    $t_harga_ak = $t_harga_ak + $hrg_ak;
                    $t_jmlh_ak = $barisak->sisa_bmf + $t_jmlh_ak;
                }
                
                $dataak = new OpfkakModel();
                $dataak->id_opfkdet = $barisopfk->id_opfkdet;
                $dataak->jmlh_opfkak = $t_jmlh_ak;
                $dataak->total_opfkak = $t_harga_ak;
                $dataak->user_id = $user_id;
                $dataak->save();
                
            }

        $dataupdateopfk = OpsikFakultasModel::where('id_opfk', $request->id_opfk)->first();                   
        $dataupdateopfk->status_opfk = 1;
        $dataupdateopfk->save();
        return response()->json(['status' => 1]);
        }
    }

    public function storeuploadcek(Request $request)
    {   
        $data = OpsikFakultasModel::where('id_opfk',$request->id_opfk)->first();
        return Response()->json($data);
    }

    public function storeupload(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        
        if ($request->hasFile('dokumen')) {            
            $file = $request->file('dokumen');
            if ($file->getClientOriginalExtension() == 'pdf') 
            {
                $fileName = time() . '_' . $file->getClientOriginalName();                
                $encryptedFileName = hash('sha256',$fileName);
                $nama_file = "$encryptedFileName.pdf";
                $filePath = $file->storeAs('berita_acara', $nama_file, 'public'); 
                //Crypt::encryptString($data->id_opfk);
                
                // Menyimpan file dengan nama terenkripsi
                //$filePath = $file->storeAs('berita_acara', $encryptedFileName, 'public');        
                $dataupdateopfk = OpsikFakultasModel::where('id_opfk', $request->id_opfk_cek)->first();                   
                $dataupdateopfk->file_opfk = $nama_file;                
                $dataupdateopfk->save();                
                return response()->json(['status' => 1]);
            }
            else
            {
                return response()->json(['status' => 2]);   
            }            
        }
    }

    
    public function destroy(Request $request)
    {
        $data = OpsikFakultasModel::where('id_opfk', $request->id_opfk)->first();   
        $data->delete();         
        return Response()->json(0);
    }

}