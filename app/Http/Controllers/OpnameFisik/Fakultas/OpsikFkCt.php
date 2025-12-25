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
use PhpParser\Node\Stmt\Foreach_;

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
            ->where('opsik_fakultas.thn_opfk',$tahun_anggaran)
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
                
                /*$jumlah = OpsikFakultasModel::where('id_fk', $id_fk)->where('thn_opfk', $request->tahun)->where('sem_opfk', $request->semester)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                { */                   
                    
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
                //}
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
                /*if($cekData->sem_opfk != $request->semester)
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
                }*/
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
            $data = OpsikFakultasModel::where('id_opfk',$request->id_opfk)->first();
            $tgl_opfk = $data->tgl_opfk;

            $jmlh_bk = BarangKeluarFakultasModel::
            where('id_fk', $id_fk)
            ->where('tglambil_bkf', '>=', $tgl_opfk )
            ->count();

            if($jmlh_bk == 0)
            {
                $dataopsikdetail = OpsikFkDetModel::where('id_opfk', $request->id_opfk)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($dataopsikdetail as $barisopfk)
                { 
                    $stok_sistem_opfkdet = $barisopfk->stok_sistem_opfkdet;
                    $stok_opsik_opfkdet = $barisopfk->stok_opsik_opfkdet;

                    if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                    {
                        $proses=0;
                        $jumlah_keluar = $stok_sistem_opfkdet - $stok_opsik_opfkdet;
                        $databarangmasukfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $barisopfk->kd_brg)
                        ->where('sisa_bmf',  '!=', 0)
                        ->orderBy('tglperolehan_bmf','asc')
                        ->get();
                        foreach($databarangmasukfakultas as $barisbmf)
                        {
                            $sisa_bmf = $barisbmf->sisa_bmf;
                            $jmlh_diambil_bmf = $barisbmf->jmlh_diambil_bmf;
                            $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                            $hrg_bmf = $barisbmf->hrg_bmf;
                            if($jumlah_keluar <= $sisa_bmf)
                            {
                                $sisa = $sisa_bmf - $jumlah_keluar;                            
                                if($proses==0)
                                {
                                    $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                    $databmupdate->sisa_bmf = $sisa;
                                    $databmupdate->jmlh_diambil_bmf = $jmlh_diambil_bmf + $jumlah_keluar;
                                    $databmupdate->save();
                                    
                                    
                                    $databkfdetail = new BarangKeluarFakultasDetailModel();
                                    $databkfdetail->id_opfk = $request->id_opfk;
                                    $databkfdetail->id_bmf = $barisbmf->id_bmf;
                                    $databkfdetail->jmlh_bkfd = $jumlah_keluar;
                                    $databkfdetail->user_id = $barisopfk->user_id;
                                    $databkfdetail->save();
                                    $proses=1;

                                    $dataopfkbkf = new OpfkbkfModel();
                                    $dataopfkbkf->id_opfkdet = $barisopfk->id_opfkdet;
                                    $dataopfkbkf->jmlh_opfkbkf = $jumlah_keluar;
                                    $dataopfkbkf->total_opfkbkf = $jumlah_keluar * $hrg_bmf;
                                    $dataopfkbkf->user_id = $barisopfk->user_id;
                                    $dataopfkbkf->save();
                                }
                            }
                            else
                            {
                                if($proses==0)
                                {
                                    $sisa = $jumlah_keluar - $sisa_bmf;
                                    if($sisa >= 0)
                                    {   
                                        $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                        $databmupdate->sisa_bmf = 0;    
                                        $databmupdate->jmlh_diambil_bmf = $jmlh_awal_bmf;                        
                                        $databmupdate->save();

                                        $databkdetail = new BarangKeluarFakultasDetailModel();
                                        $databkdetail->id_opfk = $request->id_opfk;
                                        $databkdetail->id_bmf = $barisbmf->id_bmf;
                                        $databkdetail->jmlh_bkfd = $sisa_bmf;
                                        $databkdetail->user_id = $barisopfk->user_id;
                                        $databkdetail->save();
                                        $proses=0;
                                        $jumlah_keluar = $sisa;

                                        $dataopfkbkf = new OpfkbkfModel();
                                        $dataopfkbkf->id_opfkdet = $barisopfk->id_opfkdet;
                                        $dataopfkbkf->jmlh_opfkbkf = $jumlah_keluar;
                                        $dataopfkbkf->total_opfkbkf = $jumlah_keluar * $hrg_bmf;
                                        $dataopfkbkf->user_id = $barisopfk->user_id;
                                        $dataopfkbkf->save();
                                    }
                                    else
                                    {
                                        $sisa = $sisa_bmf - $jumlah_keluar;
                                        $databmupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                        $databmupdate->sisa_bmf = $sisa;    
                                        //$databmupdate->jmlh_diambil_bmf = $jmlh_diambil_bmf + $jumlah_keluar;                        
                                        $databmupdate->save();

                                        $databkdetail = new BarangKeluarFakultasDetailModel();
                                        $databkdetail->id_opfk = $request->id_opfk;
                                        $databkdetail->id_bmf = $barisbmf->id_bmf;
                                        $databkdetail->jmlh_bkfd = $sisa_bmf;
                                        $databkdetail->user_id = $barisopfk->user_id;
                                        $databkdetail->save();                                
                                        $proses=1;

                                        $dataopfkbkf = new OpfkbkfModel();
                                        $dataopfkbkf->id_opfkdet = $barisopfk->id_opfkdet;
                                        $dataopfkbkf->jmlh_opfkbkf = $jumlah_keluar;
                                        $dataopfkbkf->total_opfkbkf = $jumlah_keluar * $hrg_bmf;
                                        $dataopfkbkf->user_id = $barisopfk->user_id;
                                        $dataopfkbkf->save();
                                    }
                                }
                            }
                        }
                    }
                    else 
                    {
                        $jumlah_masuk = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                        $databmupdate = BarangMasukFakultasModel::where('id_fk', $id_fk)
                        ->where('kd_brg', $barisopfk->kd_brg)
                        ->orderBy('tglperolehan_bmf','desc')
                        ->first();  
                        $tambahan = $databmupdate->sisa_bmf + $jumlah_masuk;
                        $databmupdate->sisa_bmf = $tambahan;
                        //$databmupdate->jmlh_diambil_bmf = $databmupdate->jmlh_diambil_bmf - $jumlah_masuk;
                        $databmupdate->save(); 
                        $hrg_bmf = $databmupdate->hrg_bmf;

                        $databmf = new OpfkbmfModel();
                        $databmf->id_opfkdet = $barisopfk->id_opfkdet;
                        $databmf->jmlh_opfkbmf = $tambahan;
                        $databmf->total_opfkbmf = $tambahan * $hrg_bmf;
                        $databmf->user_id = $barisopfk->user_id;
                        $databmf->save();
                    }
                }

            $dataupdateopfk = OpsikFakultasModel::where('id_opfk', $request->id_opfk)->first();
            $dataupdateopfk->status_opfk = 1;
            $dataupdateopfk->save();
            return response()->json(['status' => 1]);
            }
            else
            {
                return response()->json(['status' => 4]);
            }

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