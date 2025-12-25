<?php

namespace App\Http\Controllers\OpnameFisik\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpurakModel;
use App\Models\OpurbkrModel;
use App\Models\OpurbmrModel;
use App\Models\OpurdetitmModel;
use App\Models\OpuropModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikUrCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(OpsikRektoratModel::
            where('opsik_rektorat.id_ur',$datarektorat->id_ur)
            ->whereYear('opsik_rektorat.tgl_opur',$tahun_anggaran)
            ->get())
            ->addColumn('id_opur', function ($data) {
                return $data->id_opur; 
            })
            ->addColumn('id_opur_en', function ($data) {
                $id_opur_en = Crypt::encryptString($data->id_opur);
                return $id_opur_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('OpnameFisik.Rektorat.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;
        if($request->id_opur == "")
        {     
            $jumlah_belum = OpsikRektoratModel::where('id_ur', $id_ur)->where('status_opur', 0)->count();
            if($jumlah_belum >0)
            {                
                return response()->json(['status' => 5]);
            }
            else
            {   
                
                $jumlah = OpsikRektoratModel::where('id_ur', $id_ur)->where('thn_opur', $request->tahun)->where('sem_opur', $request->semester)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {                    
                    
                    $data = new OpsikRektoratModel();
                    $data->id_ur = $id_ur;
                    $data->no_opur = $request->no_opur;
                    $data->tgl_opur = $request->tgl_opur;
                    $data->sem_opur = $request->semester;
                    $data->thn_opur = $request->tahun;
                    $data->status_opur = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = OpsikRektoratModel::where('id_ur', $id_ur)->where('id_opur', $request->id_opur)->first();
            if($cekData->no_opur == $request->no_opur and $cekData->tgl_opur == $request->tgl_opur and $cekData->sem_opur == $request->semester and $cekData->thn_opur == $request->tahun )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->sem_opur != $request->semester)
                {
                    $jumlah = OpsikRektoratModel::where('id_ur', $id_ur)->where('sem_opur', $request->semester)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($cekData->thn_opur != $request->tahun)
                {
                    $jumlah = OpsikRektoratModel::where('id_ur', $id_ur)->where('thn_opur', $request->tahun)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = OpsikRektoratModel::where('id_opur', $request->id_opur)->first();
                    $data->no_opur = $request->no_opur;
                    $data->tgl_opur = $request->tgl_opur;
                    $data->sem_opur = $request->semester;
                    $data->thn_opur = $request->tahun;
                    $data->status_opur = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = OpsikRektoratModel::where('id_opur',$request->id_opur)->first();
        return Response()->json($data);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;        
        $jumlah_opur = OpsikUrDetModel::where('id_opur', $request->id_opur)->count();        
        if($jumlah_opur == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {   
            $dataopsikdetail = OpsikUrDetModel::where('id_opur', $request->id_opur)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($dataopsikdetail as $barisopur)
            {
                $t_jmlh_bmr = 0;
                $t_harga_bmr = 0;
                $databmr = BarangMasukRektoratModel::where('id_ur', $id_ur)
                ->where('kd_brg','=',$barisopur->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databmr as $barisbmr)
                {
                    $hrg_bmr = $barisbmr->jmlh_awal_bmr * $barisbmr->hrg_bmr;
                    $t_harga_bmr = $t_harga_bmr + $hrg_bmr;
                    $t_jmlh_bmr = $barisbmr->jmlh_awal_bmr + $t_jmlh_bmr;
                }
                $data = new OpurbmrModel();
                $data->id_opurdet = $barisopur->id_opurdet;
                $data->jmlh_opurbmr = $t_jmlh_bmr;
                $data->total_opurbmr = $t_harga_bmr;
                $data->user_id = $user_id;
                $data->save();

                $t_jmlh_bkr = 0;
                $t_harga_bkr = 0;
                $databkr = BarangKeluarRektoratModel::where('id_ur', $id_ur)
                ->where('kd_brg','=',$barisopur->kd_brg)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databkr as $barisbkr)
                {
                    $t_jmlh_bkrd = 0;
                    $t_harga_bkrd = 0;
                    $databkrd = BarangKeluarRektoratDetailModel::
                    join('barang_masuk_rektorat','barang_keluar_rektorat_detail.id_bmr','=','barang_masuk_rektorat.id_bmr')
                    ->where('id_bkr', $barisbkr->id_bkr)
                    ->orderBy('barang_keluar_rektorat_detail.id_bmr','asc')
                    ->get();
                    foreach($databkrd as $barisbkrd)
                    {
                        $hrg_bkrd = $barisbkrd->jmlh_bkrd * $barisbkrd->hrg_bmr;
                        $t_harga_bkrd = $t_harga_bkrd + $hrg_bkrd;
                        $t_jmlh_bkrd = $barisbkrd->jmlh_bkrd + $t_jmlh_bkrd;
                    }
                    $t_jmlh_bkr = $t_jmlh_bkr + $t_jmlh_bkrd;
                    $t_harga_bkr = $t_harga_bkr + $t_harga_bkrd;
                }
                
                $dataopfkbkr = new OpurbkrModel();
                $dataopfkbkr->id_opurdet = $barisopur->id_opurdet;
                $dataopfkbkr->jmlh_opurbkr = $t_jmlh_bkr;
                $dataopfkbkr->total_opurbkr = $t_harga_bkr;
                $dataopfkbkr->user_id = $user_id;
                $dataopfkbkr->save();

                $stok_sistem_opurdet = $barisopur->stok_sistem_opurdet;
                $stok_opsik_opurdet = $barisopur->stok_opsik_opurdet;                
                $jumlahbmr = BarangMasukRektoratModel::where('id_ur','=',$id_ur)->where('kd_brg','=',$barisopur->kd_brg)->where('sisa_bmr','>',0)->orderBy('tglperolehan_bmr','asc')->count();                
                if($stok_sistem_opurdet < $stok_opsik_opurdet) // kecil
                {
                    if($jumlahbmr==0)
                    {
                        $barisbmr = BarangMasukRektoratModel::where('id_ur','=',$id_ur)->where('kd_brg','=',$barisopur->kd_brg)->orderBy('tglperolehan_bmr','desc')->first();
                        $selisih = $stok_opsik_opurdet - $stok_sistem_opurdet;                    
                        $data = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();
                        $data->jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr + $selisih;
                        $data->sisa_bmr = $barisbmr->sisa_bmr + $selisih;
                        $data->save();  
                    }
                    else
                    {
                        $tnilai_baru=0;
                        $proses=0;            
                        $selisih = $stok_opsik_opurdet - $stok_sistem_opurdet ;
                        $bariscekbmr = BarangKeluarRektoratDetailModel::
                        join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                        ->where('id_ur','=',$id_ur)->where('kd_brg','=',$barisopur->kd_brg)->orderBy('id_bmr','desc')->get();
                        foreach($bariscekbmr as $barisbmr)
                        {
                            $jmlh_bkrd = $barisbmr->jmlh_bkrd;
                            if($selisih <= $jmlh_bkrd)
                            {
                                $sisa = $jmlh_bkrd - $selisih;                       
                                if($proses==0)
                                {                                    
                                    $datacekbmr = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();

                                    $nilai_baru = $datacekbmr->hrg_bmr * $selisih;

                                    $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                    $databmupdate->sisa_bmr = $selisih + $datacekbmr->sisa_bmr;
                                    $databmupdate->save();
                                    
                                    $dataoprdetail = new OpurdetitmModel();
                                    $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                    $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                    $dataoprdetail->id_bkrd = $barisbmr->id_bkrd;
                                    $dataoprdetail->jmlh_opurdetitm = $selisih;
                                    $dataoprdetail->user_id = $user_id;
                                    $dataoprdetail->save();
                                    $proses=1;
                                    $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
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
                                    $sisa = $selisih - $jmlh_bkrd;
                                    if($sisa >= 0)
                                    {   
                                        $datacekbmr = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();

                                        $nilai_baru = $datacekbmr->hrg_bmr * $jmlh_bkrd;

                                        $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                        $databmupdate->sisa_bmr = $jmlh_bkrd + $datacekbmr->sisa_bmr;
                                        $databmupdate->save();
        
                                        $dataoprdetail = new OpurdetitmModel();
                                        $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                        $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                        $dataoprdetail->id_bkrd = $barisbmr->id_bkrd;
                                        $dataoprdetail->jmlh_opurdetitm = $jmlh_bkrd;
                                        $dataoprdetail->user_id = $user_id;
                                        $dataoprdetail->save();
                                        $proses=0;
                                        $selisih = $sisa;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir + $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_bkrd;
                                        $databmupdateitemnilai->save();
                                    }
                                    else
                                    {
                                        $sisa = $jmlh_bkrd - $selisih;
                                        $datacekbmr = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();
                                        $nilai_baru = $datacekbmr->hrg_bmr * $selisih;
                                        
                                        $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                        $databmupdate->sisa_bmr = $jmlh_bkrd + $datacekbmr->sisa_bmr;                            
                                        $databmupdate->save();
        
                                        $dataoprdetail = new OpurdetitmModel();
                                        $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                        $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                        $dataoprdetail->id_bkrd = $barisbmr->id_bkrd;
                                        $dataoprdetail->jmlh_opurdetitm = $selisih;
                                        $dataoprdetail->user_id = $user_id;
                                        $dataoprdetail->save();                                
                                        $proses=1;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();
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
                    if($jumlahbmr==0)
                    {
                        
                    }
                    else
                    {            
                        $proses=0;            
                        $tnilai_baru=0;
                        $selisih = $stok_sistem_opurdet - $stok_opsik_opurdet ;
                        $bariscekbmr = BarangMasukRektoratModel::where('id_ur','=',$id_ur)->where('kd_brg','=',$barisopur->kd_brg)->where('sisa_bmr','>',0)->orderBy('tglperolehan_bmr','asc')->get();
                        foreach($bariscekbmr as $barisbmr)
                        {                      
                            $sisabmr = $barisbmr->sisa_bmr;
                            if($selisih <= $sisabmr)
                            {
                                $sisa = $sisabmr - $selisih;                            
                                if($proses==0)
                                {
                                    $nilai_baru = $barisbmr->hrg_bmr * $selisih;
                                    $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                    $databmupdate->sisa_bmr = $sisa;
                                    $databmupdate->save();
                                    
                                    $dataoprdetail = new OpurdetitmModel();
                                    $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                    $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                    $dataoprdetail->jmlh_opurdetitm = $selisih;
                                    $dataoprdetail->user_id = $user_id;
                                    $dataoprdetail->save();
                                    $proses=1;
                                    $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
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
                                    $sisa = $selisih - $sisabmr;
                                    if($sisa >= 0)
                                    {          
                                        $nilai_baru = $barisbmr->hrg_bmr * $sisabmr;
                                        $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                        $databmupdate->sisa_bmr = 0;                            
                                        $databmupdate->save();
        
                                        $dataoprdetail = new OpurdetitmModel();
                                        $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                        $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                        $dataoprdetail->jmlh_opurdetitm = $sisabmr;
                                        $dataoprdetail->user_id = $user_id;
                                        $dataoprdetail->save();
                                        $proses=0;
                                        $selisih = $sisa;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
                                        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();
                                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                        $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $sisabmr;
                                        $databmupdateitemnilai->save();
                                    }
                                    else
                                    {
                                        $sisa = $sisabmr - $selisih;
                                        $nilai_baru = $barisbmr->hrg_bmr * $selisih;
                                        $databmupdate = BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)->first();                   
                                        $databmupdate->sisa_bmr = $sisa;                            
                                        $databmupdate->save();
        
                                        $dataoprdetail = new OpurdetitmModel();
                                        $dataoprdetail->id_opurdet = $barisopur->id_opurdet;
                                        $dataoprdetail->id_bmr = $barisbmr->id_bmr;
                                        $dataoprdetail->jmlh_opurdetitm = $selisih;
                                        $dataoprdetail->user_id = $user_id;
                                        $dataoprdetail->save();                                
                                        $proses=1;
        
                                        $databarang = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();                                
                                        $nilai_terakhir = $databarang->nilai_brg;
        
                                        $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();
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
                $t_bkrd = 0;
                $dataop = OpurdetitmModel::
                join('barang_masuk_rektorat','opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                ->where('id_opurdet', $barisopur->id_opurdet)
                ->orderBy('opfik_rektorat_detail_item.id_bmr','asc')
                ->get();
                foreach($dataop as $barisop)
                {
                    $t_bkrd =$barisop->id_bkrd;
                    $hrg_op = $barisop->jmlh_opurdetitm * $barisop->hrg_bmr;
                    $t_harga_op = $t_harga_op + $hrg_op;
                    $t_jmlh_op = $barisop->jmlh_opurdetitm + $t_jmlh_op;
                }
                
                $dataop = new OpuropModel();
                $dataop->id_opurdet = $barisopur->id_opurdet;
                $dataop->jmlh_opurop = $t_jmlh_op;
                $dataop->total_opurop = $t_harga_op;
                if($t_bkrd > 0)
                {
                    $dataop->status_opurop = 1;
                }
                else
                {
                    $dataop->status_opurop = 2;
                }
                $dataop->user_id = $user_id;
                $dataop->save();

                $t_jmlh_ak = 0;
                $t_harga_ak = 0;
                $dataak = BarangMasukRektoratModel::where('id_ur', $id_ur)
                ->where('kd_brg','=',$barisopur->kd_brg)
                ->where('sisa_bmr','>',0)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($dataak as $barisak)
                {
                    $hrg_ak = $barisak->sisa_bmr * $barisak->hrg_bmr;
                    $t_harga_ak = $t_harga_ak + $hrg_ak;
                    $t_jmlh_ak = $barisak->sisa_bmr + $t_jmlh_ak;
                }
                
                $dataak = new OpurakModel();
                $dataak->id_opurdet = $barisopur->id_opurdet;
                $dataak->jmlh_opurak = $t_jmlh_ak;
                $dataak->total_opurak = $t_harga_ak;
                $dataak->user_id = $user_id;
                $dataak->save();
                
            }

        $dataupdateopur = OpsikRektoratModel::where('id_opur', $request->id_opur)->first();                   
        $dataupdateopur->status_opur = 1;
        $dataupdateopur->save();
        return response()->json(['status' => 1]);
        }
    }

    public function storeuploadcek(Request $request)
    {   
        $data = OpsikRektoratModel::where('id_opur',$request->id_opur)->first();
        return Response()->json($data);
    }

    public function storeupload(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_fk = $barisrektorat->id_ur;
        
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
                $dataupdateopur = OpsikRektoratModel::where('id_opur', $request->id_opur_cek)->first();                   
                $dataupdateopur->file_opur = $nama_file;                
                $dataupdateopur->save();                
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
        $data = OpsikRektoratModel::where('id_opur', $request->id_opur)->first();   
        $data->delete();         
        return Response()->json(0);
    }

}