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
                
                $dataopurbkr = new OpurbkrModel();
                $dataopurbkr->id_opurdet = $barisopur->id_opurdet;
                $dataopurbkr->jmlh_opurbkr = $t_jmlh_bkr;
                $dataopurbkr->total_opurbkr = $t_harga_bkr;
                $dataopurbkr->user_id = $user_id;
                $dataopurbkr->save();

                $stok_sistem_opurdet = $barisopur->stok_sistem_opurdet;
                $stok_opsik_opurdet = $barisopur->stok_opsik_opurdet;
                if($stok_sistem_opurdet < $stok_opsik_opurdet) // sistem kecil dari rill
                {
                    $databmrterakhir = BarangMasukRektoratModel::where('id_ur', $id_ur)
                    ->where('kd_brg','=',$barisopur->kd_brg)
                    ->orderBy('tglperolehan_bmr','desc')
                    ->first();
                    $hargabmrterakhir = $databmrterakhir->hrg_bmr;                    
                    $sisa = $stok_opsik_opurdet - $stok_sistem_opurdet;
                    $total_oprkop = $sisa * $hargabmrterakhir;
                    $dataop = new OpuropModel();
                    $dataop->id_opurdet = $barisopur->id_opurdet;
                    $dataop->jmlh_opurop = $sisa;
                    $dataop->total_opurop = $total_oprkop;
                    $dataop->status_opurop = 1;                    
                    $dataop->user_id = $user_id;
                    $dataop->save();

                    
                    $databmrterakhir->sisa_bmr = $sisa + $databmrterakhir->sisa_bmr;
                    $databmrterakhir->user_id = $user_id;
                    $databmrterakhir->save();

                    $t_jmlh_ak = $stok_sistem_opurdet + $sisa;
                    $t_harga_ak = $t_harga_bmr + $total_oprkop;
                    $dataak = new OpurakModel();
                    $dataak->id_opurdet = $barisopur->id_opurdet;
                    $dataak->jmlh_opurak = $t_jmlh_ak;
                    $dataak->total_opurak = $t_harga_ak;
                    $dataak->user_id = $user_id;
                    $dataak->save();
                }
                else
                {
                    if($stok_sistem_opurdet == $stok_opsik_opurdet) // sistem sama dengan rill
                    {
                        $dataop = new OpuropModel();
                        $dataop->id_opurdet = $barisopur->id_opurdet;
                        $dataop->jmlh_opurop = 0;
                        $dataop->total_opurop = 0;
                        $dataop->status_opurop = 2;                    
                        $dataop->user_id = $user_id;
                        $dataop->save();

                        $total_hrg_bmr = 0;
                        $databmrterakhir = BarangMasukRektoratModel::where('id_ur', $id_ur)
                        ->where('kd_brg','=',$barisopur->kd_brg)
                        ->where('sisa_bmr','>',0)
                        ->orderBy('tglperolehan_bmr','asc')
                        ->get();
                        foreach($databmrterakhir as $barisbmrterakhir)
                        {
                            $total_hrg_bmr = $total_hrg_bmr + ($barisbmrterakhir->sisa_bmr * $barisbmrterakhir->hrg_bmr); 
                        }
                        
                        $dataak = new OpurakModel();
                        $dataak->id_opurdet = $barisopur->id_opurdet;
                        $dataak->jmlh_opurak = $stok_sistem_opurdet;
                        $dataak->total_opurak = $total_hrg_bmr;
                        $dataak->user_id = $user_id;
                        $dataak->save();
                    }
                    else //sistem besar dari rill
                    {
                        $proses = 1;
                        $sisa_proses= $stok_sistem_opurdet - $stok_opsik_opurdet;
                        $sisa = $stok_sistem_opurdet - $stok_opsik_opurdet;
                        $sisa_awal = $stok_sistem_opurdet - $stok_opsik_opurdet;
                        $total_opurop=0;
                        $total_hrg_bmr = 0;
                        $databmrterakhir = BarangMasukRektoratModel::where('id_ur', $id_ur)
                        ->where('kd_brg','=',$barisopur->kd_brg)
                        ->where('sisa_bmr','>',0)
                        ->orderBy('tglperolehan_bmr','asc')
                        ->get();
                        foreach($databmrterakhir as $barisbmrterakhir)
                        {        
                            $total_hrg_bmr = $total_hrg_bmr + ($barisbmrterakhir->sisa_bmr * $barisbmrterakhir->hrg_bmr); 
                            if($proses == 0)
                            {
                                
                            }   
                            else
                            {
                                $sisa_bmr = $barisbmrterakhir->sisa_bmr;    
                                $hrg_bmr = $barisbmrterakhir->hrg_bmr;                            
                                if($sisa_proses <= $sisa_bmr)
                                {   
                                    $databmrterakhircek = BarangMasukRektoratModel::where('id_bmr','=',$barisbmrterakhir->id_bmr)->first(); 
                                    $databmrterakhircek->sisa_bmr = $sisa_bmr - $sisa_proses;
                                    $databmrterakhircek->user_id = $user_id;
                                    $databmrterakhircek->save();

                                    $total_opurop = $total_opurop + ($sisa_proses * $hrg_bmr);
                                    $proses=0;
                                    $sisa_proses=0;
                                }
                                else
                                {
                                    $total_opurop = $total_opurop + ($sisa_bmr * $hrg_bmr);
                                    $sisa_proses = $sisa_proses - $sisa_bmr;
                                    $proses=1;
                                    $databmrterakhircek = BarangMasukRektoratModel::where('id_bmr','=',$barisbmrterakhir->id_bmr)->first(); 
                                    $databmrterakhircek->sisa_bmr = $sisa_proses;
                                    $databmrterakhircek->user_id = $user_id;
                                    $databmrterakhircek->save();
                                }
                            }
                            
                        }

                        $dataop = new OpuropModel();
                        $dataop->id_opurdet = $barisopur->id_opurdet;
                        $dataop->jmlh_opurop = $sisa;
                        $dataop->total_opurop = $total_opurop;
                        $dataop->status_opurop = 2;                    
                        $dataop->user_id = $user_id;
                        $dataop->save();

                        $t_harga_ak = $total_hrg_bmr - $total_opurop;
                        $dataak = new OpurakModel();
                        $dataak->id_opurdet = $barisopur->id_opurdet;
                        $dataak->jmlh_opurak = $stok_opsik_opurdet;
                        $dataak->total_opurak = $t_harga_ak;
                        $dataak->user_id = $user_id;
                        $dataak->save();
                    }
                   
                }
                
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