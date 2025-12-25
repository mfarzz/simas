<?php

namespace App\Http\Controllers\OpnameFisik\RumahSakit;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangModel;
use App\Models\OpsikRumahSakitModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpursakModel;
use App\Models\OpursbkrsModel;
use App\Models\OpursbmrsModel;
use App\Models\OpursdetitmModel;
use App\Models\OpursopModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikUrsCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(OpsikRumahSakitModel::
            where('opsik_rumah_sakit.id_urs',$datarumahsakit->id_urs)
            ->whereYear('opsik_rumah_sakit.tgl_opurs',$tahun_anggaran)
            ->get())
            ->addColumn('id_opurs', function ($data) {
                return $data->id_opurs; 
            })
            ->addColumn('id_opurs_en', function ($data) {
                $id_opurs_en = Crypt::encryptString($data->id_opurs);
                return $id_opurs_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('OpnameFisik.RumahSakit.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;
        if($request->id_opurs == "")
        {     
            $jumlah_belum = OpsikRumahSakitModel::where('id_urs', $id_urs)->where('status_opurs', 0)->count();
            if($jumlah_belum >0)
            {                
                return response()->json(['status' => 5]);
            }
            else
            {   
                
                $jumlah = OpsikRumahSakitModel::where('id_urs', $id_urs)->where('thn_opurs', $request->tahun)->where('sem_opurs', $request->semester)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {   
                    $data = new OpsikRumahSakitModel();
                    $data->id_urs = $id_urs;
                    $data->no_opurs = $request->no_opurs;
                    $data->tgl_opurs = $request->tgl_opurs;
                    $data->sem_opurs = $request->semester;
                    $data->thn_opurs = $request->tahun;
                    $data->status_opurs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = OpsikRumahSakitModel::where('id_urs', $id_urs)->where('id_opurs', $request->id_opurs)->first();
            if($cekData->no_opurs == $request->no_opurs and $cekData->tgl_opurs == $request->tgl_opurs and $cekData->sem_opurs == $request->semester and $cekData->thn_opurs == $request->tahun )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->sem_opurs != $request->semester)
                {
                    $jumlah = OpsikRumahSakitModel::where('id_urs', $id_urs)->where('sem_opurs', $request->semester)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($cekData->thn_opurs != $request->tahun)
                {
                    $jumlah = OpsikRumahSakitModel::where('id_urs', $id_urs)->where('thn_opurs', $request->tahun)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = OpsikRumahSakitModel::where('id_opurs', $request->id_opurs)->first();
                    $data->no_opurs = $request->no_opurs;
                    $data->tgl_opurs = $request->tgl_opurs;
                    $data->sem_opurs = $request->semester;
                    $data->thn_opurs = $request->tahun;
                    $data->status_opurs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = OpsikRumahSakitModel::where('id_opurs',$request->id_opurs)->first();
        return Response()->json($data);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;        
        $jumlah_opurs = OpsikUrsDetModel::where('id_opurs', $request->id_opurs)->count();        
        if($jumlah_opurs == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {   
            $dataopsikdetail = OpsikUrsDetModel::where('id_opurs', $request->id_opurs)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($dataopsikdetail as $barisopurs)
            {
                $t_jmlh_bmrs = 0;
                $t_harga_bmrs = 0;
                $databmrs = BarangMasukRumahSakitModel::where('id_urs', $id_urs)
                ->where('kd_brg','=',$barisopurs->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databmrs as $barisbmrs)
                {
                    $hrg_bmrs = $barisbmrs->jmlh_awal_bmrs * $barisbmrs->hrg_bmrs;
                    $t_harga_bmrs = $t_harga_bmrs + $hrg_bmrs;
                    $t_jmlh_bmrs = $barisbmrs->jmlh_awal_bmrs + $t_jmlh_bmrs;
                }                
                $data = new OpursbmrsModel();
                $data->id_opursdet = $barisopurs->id_opursdet;
                $data->jmlh_opursbmrs = $t_jmlh_bmrs;
                $data->total_opursbmrs = $t_harga_bmrs;
                $data->user_id = $user_id;
                $data->save();

                

                $t_jmlh_bkrs = 0;
                $t_harga_bkrs = 0;
                $databkrs = BarangKeluarRumahSakitModel::where('id_urs', $id_urs)
                ->where('kd_brg','=',$barisopurs->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databkrs as $barisbkrs)
                {
                    $t_jmlh_bkrsd = 0;
                    $t_harga_bkrsd = 0;
                    $databkrsd = BarangKeluarRumahSakitDetailModel::
                    join('barang_masuk_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                    ->where('id_bkrs', $barisbkrs->id_bkrs)
                    ->orderBy('barang_keluar_rumah_sakit_detail.id_bmrs','asc')
                    ->get();
                    foreach($databkrsd as $barisbkrsd)
                    {
                        $hrg_bkrsd = $barisbkrsd->jmlh_bkrsd * $barisbkrsd->hrg_bmrs;
                        $t_harga_bkrsd = $t_harga_bkrsd + $hrg_bkrsd;
                        $t_jmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $t_jmlh_bkrsd;
                    }
                    $t_jmlh_bkrs = $t_jmlh_bkrs + $t_jmlh_bkrsd;
                    $t_harga_bkrs = $t_harga_bkrs + $t_harga_bkrsd;
                }
                
                $dataopfkbkrs = new OpursbkrsModel();
                $dataopfkbkrs->id_opursdet = $barisopurs->id_opursdet;
                $dataopfkbkrs->jmlh_opursbkrs = $t_jmlh_bkrs;
                $dataopfkbkrs->total_opursbkrs = $t_harga_bkrs;
                $dataopfkbkrs->user_id = $user_id;
                $dataopfkbkrs->save();
                
                $stok_sistem_opursdet = $barisopurs->stok_sistem_opursdet;
                $stok_opsik_opursdet = $barisopurs->stok_opsik_opursdet;                
                $jumlahbmrs = BarangMasukRumahSakitModel::where('id_urs','=',$id_urs)->where('kd_brg','=',$barisopurs->kd_brg)->where('sisa_bmrs','>',0)->orderBy('tglperolehan_bmrs','asc')->count();                
                if($stok_sistem_opursdet < $stok_opsik_opursdet) // kecil
                {
                    if($jumlahbmrs==0)
                    {
                        $barisbmrs = BarangMasukRumahSakitModel::where('id_urs','=',$id_urs)->where('kd_brg','=',$barisopurs->kd_brg)->orderBy('tglperolehan_bmrs','desc')->first();
                        $selisih = $stok_opsik_opursdet - $stok_sistem_opursdet;                    
                        $data = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();
                        $data->jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs + $selisih;
                        $data->sisa_bmrs = $barisbmrs->sisa_bmrs + $selisih;
                        $data->save();  
                    }
                    else
                    {
                        $tnilai_baru=0;
                        $proses=0;            
                        $selisih = $stok_opsik_opursdet - $stok_sistem_opursdet ;
                        $bariscekbmrs = BarangKeluarRumahSakitDetailModel::
                        join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                        ->where('id_urs','=',$id_urs)->where('kd_brg','=',$barisopurs->kd_brg)->orderBy('id_bmrs','desc')->get();
                        foreach($bariscekbmrs as $barisbmrs)
                        {
                            $jmlh_bkrsd = $barisbmrs->jmlh_bkrsd;
                            if($selisih <= $jmlh_bkrsd)
                            {
                                $sisa = $jmlh_bkrsd - $selisih;                       
                                if($proses==0)
                                {                                    
                                    $datacekbmrs = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();

                                    $nilai_baru = $datacekbmrs->hrg_bmrs * $selisih;

                                    $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                    $databmupdate->sisa_bmrs = $selisih + $datacekbmrs->sisa_bmrs;
                                    $databmupdate->save();
                                    
                                    $dataoprsdetail = new OpursdetitmModel();
                                    $dataoprsdetail->id_opursdet = $barisopurs->id_opursdet;
                                    $dataoprsdetail->id_bmrs = $barisbmrs->id_bmrs;
                                    $dataoprsdetail->id_bkrsd = $barisbmrs->id_bkrsd;
                                    $dataoprsdetail->jmlh_opursdetitm = $selisih;
                                    $dataoprsdetail->user_id = $user_id;
                                    $dataoprsdetail->save();
                                    $proses=1;
                                    $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
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
                                    $sisa = $selisih - $jmlh_bkrsd;
                                    if($sisa >= 0)
                                    {   
                                        $datacekbmrs = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();

                                        $nilai_baru = $datacekbmrs->hrg_bmrs * $jmlh_bkrsd;

                                        $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                        $databmupdate->sisa_bmrs = $jmlh_bkrsd + $datacekbmrs->sisa_bmrs;
                                        $databmupdate->save();
        
                                        $dataoprdetail = new OpursdetitmModel();
                                        $dataoprdetail->id_opursdet = $barisopurs->id_opursdet;
                                        $dataoprdetail->id_bmrs = $barisbmrs->id_bmrs;
                                        $dataoprdetail->id_bkrsd = $barisbmrs->id_bkrsd;
                                        $dataoprdetail->jmlh_opursdetitm = $jmlh_bkrsd;
                                        $dataoprdetail->user_id = $user_id;
                                        $dataoprdetail->save();
                                        $proses=0;
                                        $selisih = $sisa;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir + $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_bkrsd;
                                        $databmupdateitemnilai->save();
                                    }
                                    else
                                    {
                                        $sisa = $jmlh_bkrsd - $selisih;
                                        $datacekbmrs = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();
                                        $nilai_baru = $datacekbmrs->hrg_bmrs * $selisih;
                                        
                                        $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                        $databmupdate->sisa_bmrs = $jmlh_bkrsd + $datacekbmrs->sisa_bmrs;                            
                                        $databmupdate->save();
        
                                        $dataoprsdetail = new OpursdetitmModel();
                                        $dataoprsdetail->id_opurdet = $barisopurs->id_opursdet;
                                        $dataoprsdetail->id_bmr = $barisbmrs->id_bmrs;
                                        $dataoprsdetail->id_bkrd = $barisbmrs->id_bkrsd;
                                        $dataoprsdetail->jmlh_opursdetitm = $selisih;
                                        $dataoprsdetail->user_id = $user_id;
                                        $dataoprsdetail->save();                                
                                        $proses=1;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();
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
                    if($jumlahbmrs==0)
                    {
                        
                    }
                    else
                    {            
                        $proses=0;            
                        $tnilai_baru=0;
                        $selisih = $stok_sistem_opursdet - $stok_opsik_opursdet ;
                        $bariscekbmrs = BarangMasukRumahSakitModel::where('id_urs','=',$id_urs)->where('kd_brg','=',$barisopurs->kd_brg)->where('sisa_bmrs','>',0)->orderBy('tglperolehan_bmrs','asc')->get();
                        foreach($bariscekbmrs as $barisbmrs)
                        {                      
                            $sisabmrs = $barisbmrs->sisa_bmrs;
                            if($selisih <= $sisabmrs)
                            {
                                $sisa = $sisabmrs - $selisih;                            
                                if($proses==0)
                                {
                                    $nilai_baru = $barisbmrs->hrg_bmrs * $selisih;
                                    $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                    $databmupdate->sisa_bmrs = $sisa;
                                    $databmupdate->save();
                                    
                                    $dataoprdetail = new OpursdetitmModel();
                                    $dataoprdetail->id_opursdet = $barisopurs->id_opursdet;
                                    $dataoprdetail->id_bmrs = $barisbmrs->id_bmrs;
                                    $dataoprdetail->jmlh_opursdetitm = $selisih;
                                    $dataoprdetail->user_id = $user_id;
                                    $dataoprdetail->save();
                                    $proses=1;
                                    $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
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
                                    $sisa = $selisih - $sisabmrs;
                                    if($sisa >= 0)
                                    {          
                                        $nilai_baru = $barisbmrs->hrg_bmrs * $sisabmrs;
                                        $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                        $databmupdate->sisa_bmrs = 0;                            
                                        $databmupdate->save();
        
                                        $dataoprsdetail = new OpursdetitmModel();
                                        $dataoprsdetail->id_opursdet = $barisopurs->id_opurdet;
                                        $dataoprsdetail->id_bmrs = $barisbmrs->id_bmrs;
                                        $dataoprsdetail->jmlh_opursdetitm = $sisabmrs;
                                        $dataoprsdetail->user_id = $user_id;
                                        $dataoprsdetail->save();
                                        $proses=0;
                                        $selisih = $sisa;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $sisabmrs;
                                        $databmupdateitemnilai->save();
                                    }
                                    else
                                    {
                                        $sisa = $sisabmrs - $selisih;
                                        $nilai_baru = $barisbmrs->hrg_bmrs * $selisih;
                                        $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                        $databmupdate->sisa_bmrs = $sisa;                            
                                        $databmupdate->save();
        
                                        $dataoprsdetail = new OpursdetitmModel();
                                        $dataoprsdetail->id_opursdet = $barisopurs->id_opurdet;
                                        $dataoprsdetail->id_bmrs = $barisbmrs->id_bmrs;
                                        $dataoprsdetail->jmlh_opursdetitm = $selisih;
                                        $dataoprsdetail->user_id = $user_id;
                                        $dataoprsdetail->save();                                
                                        $proses=1;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $selisih;
                                        $databmupdateitemnilai->save();
                                    }
                                }
                            }
                        }
                    }
                }
                $t_jmlh_op = 0;
                $t_harga_op = 0;
                $t_bkrsd = 0;
                $dataop = OpursdetitmModel::
                join('barang_masuk_rumah_sakit','opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                ->where('id_opursdet', $barisopurs->id_opursdet)
                ->orderBy('opfik_rumah_sakit_detail_item.id_bmrs','asc')
                ->get();
                foreach($dataop as $barisop)
                {
                    $t_bkrsd =$barisop->id_bkrsd;
                    $hrg_op = $barisop->jmlh_opursdetitm * $barisop->hrg_bmrs;
                    $t_harga_op = $t_harga_op + $hrg_op;
                    $t_jmlh_op = $barisop->jmlh_opursdetitm + $t_jmlh_op;
                }
                
                $dataop = new OpursopModel();
                $dataop->id_opursdet = $barisopurs->id_opursdet;
                $dataop->jmlh_opursop = $t_jmlh_op;
                $dataop->total_opursop = $t_harga_op;
                if($t_bkrsd > 0)
                {
                    $dataop->status_opursop = 1;
                }
                else
                {
                    $dataop->status_opursop = 2;
                }
                $dataop->user_id = $user_id;
                $dataop->save();
                
                $t_jmlh_ak = 0;
                $t_harga_ak = 0;
                $dataak = BarangMasukRumahSakitModel::where('id_urs', $id_urs)
                ->where('kd_brg','=',$barisopurs->kd_brg)
                ->where('sisa_bmrs','>',0)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($dataak as $barisak)
                {
                    $hrg_ak = $barisak->sisa_bmrs * $barisak->hrg_bmrs;
                    $t_harga_ak = $t_harga_ak + $hrg_ak;
                    $t_jmlh_ak = $barisak->sisa_bmrs + $t_jmlh_ak;
                }                
                
                $dataak = new OpursakModel();
                $dataak->id_opursdet = $barisopurs->id_opursdet;
                $dataak->jmlh_opursak = $t_jmlh_ak;
                $dataak->total_opursak = $t_harga_ak;
                $dataak->user_id = $user_id;
                $dataak->save();
            }

        $dataupdateopurs = OpsikRumahSakitModel::where('id_opurs', $request->id_opurs)->first();                   
        $dataupdateopurs->status_opurs = 1;
        $dataupdateopurs->save();
        return response()->json(['status' => 1]);
        }
    }

    public function storeuploadcek(Request $request)
    {   
        $data = OpsikRumahSakitModel::where('id_opurs',$request->id_opurs)->first();
        return Response()->json($data);
    }

    public function storeupload(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_fk = $barisrumahsakit->id_urs;
        
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
                $dataupdateopurs = OpsikRumahSakitModel::where('id_opurs', $request->id_opurs_cek)->first();                   
                $dataupdateopurs->file_opurs = $nama_file;                
                $dataupdateopurs->save();                
                return response()->json(['status' => 1]);
            }
            else
            {
                return response()->json(['status' => 2]);   
            }            
        }
    }

    /*

    public function destroy(Request $request)
    {
        $data = BmfsModel::where('id_bmfs', $request->id_bmfs)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    */
}