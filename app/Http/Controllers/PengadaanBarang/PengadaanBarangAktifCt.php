<?php

namespace App\Http\Controllers\PengadaanBarang;

use App\Http\Controllers\Controller;
use App\Models\JenisSatuanModel;
use App\Models\PengadaanBarangDetailHistoryModel;
use App\Models\PengadaanBarangDetailModel;
use App\Models\PengadaanBarangHistoryModel;
use App\Models\PengadaanBarangModel;
use App\Models\RefPosisiKegiatanModel;
use App\Models\RefStatusProsesModel;
use App\Models\RefStatusProsesUntukModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PengadaanBarangAktifCt extends Controller
{
    public function index()
    {
        $role_id = auth()->user()->role_id;

        $dataposisi = RefPosisiKegiatanModel::where('role_id', $role_id)->where('id_rk',1)->first();
        $posisi = $dataposisi->posisi_rpk;

        $daftar_status = RefStatusProsesModel::
        select('ref_status_proses_untuk.id', 'ref_status_proses.nm_rsp')
        ->join('ref_status_proses_untuk','ref_status_proses.id','=','ref_status_proses_untuk.id_rsp')
        ->join('role_pengguna','ref_status_proses_untuk.role_id_pilihan','=','role_pengguna.id')
        ->where('ref_status_proses_untuk.posisi_pb_pilihan', '=', $posisi)        
        ->where('ref_status_proses_untuk.id_rk', '=',1)
        ->orderBy('nm_rsp')->orderBy('ref_status_proses.id')->get();

        if(request()->ajax()) {
            return datatables()->of(PengadaanBarangModel::select('pengadaan_barang.id', 'pengadaan_barang.nm_pb', 'pengadaan_barang.tgl_pb','ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'ref_status_proses_untuk.role_id_proses', 'role_pengguna.nama_rp', 'v_pengadaan_barang_detail_belum_diproses.jumlah_belum_diproses')
            ->join('ref_status_proses_untuk','pengadaan_barang.id_rspu','=','ref_status_proses_untuk.id')
            ->join('role_pengguna','ref_status_proses_untuk.role_id_proses','=','role_pengguna.id')
            ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id')
            ->leftjoin('v_pengadaan_barang_detail_belum_diproses','pengadaan_barang.id','=','v_pengadaan_barang_detail_belum_diproses.id_pb')
            ->where('pengadaan_barang.status_pb', '=', 1)
            ->where('pengadaan_barang.role_id', '=', $role_id)
            ->get())
            ->addColumn('status_pb', function ($data) {
                return "$data->nm_rsp $data->nm_rspu $data->nama_rp"; 
            })
            ->addColumn('id_pb_en', function ($data) {
                $id_pb_enkripsi = Crypt::encryptString($data->id);
                return $id_pb_enkripsi; 
            })
            ->addColumn('action', 'components.form-action.form-action-udah')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('PengadaanBarang.Aktif.index',['daftar_status'=>$daftar_status]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        if($request->id == "")
        {
            $datastatus = RefStatusProsesUntukModel::where('id_rsp', 0)->where('id_rk', 1)->first();

            $data = new PengadaanBarangModel();
            $data->id_rspu = $datastatus->id;
            $data->nm_pb = $request->nama;
            $data->tgl_pb = $request->tgl;
            $data->status_pb = 1;
            $data->role_id = $role_id;            
            $data->posisi_pb = 1;
            $data->user_id = $user_id;
            $data->save();
            $id_pb = $data->id;

            $datahistory = new PengadaanBarangHistoryModel();
            $datahistory->id_pb = $id_pb;
            $datahistory->id_rspu = $datastatus->id;
            $datahistory->ket_pbh = '';
            $datahistory->user_id = $user_id;
            $datahistory->role_id = $role_id;
            $datahistory->save();

            return response()->json(['status' => 1]);
        }
        else
        {
            $data = PengadaanBarangModel::where('id', $request->id)->first();
            $data->nm_pb = $request->nama;
            $data->tgl_pb = $request->tgl;               
            $data->user_id = $user_id;             
            $data->save();

            return response()->json(['status' => 4]);
        }        
    }

    public function edit(Request $request)
    {   
        $data = PengadaanBarangModel::where('id',$request->id)->first();
        return Response()->json($data);
    }

    public function show(Request $request)
    {   
        $data = PengadaanBarangModel::where('id',$request->id)->first();
        return Response()->json($data);
    }

    public function ajuan(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $dataposisi = RefPosisiKegiatanModel::where('role_id', $role_id)->where('id_rk',1)->first();
        $posisi = $dataposisi->posisi_rpk;

        $datadisposisi = RefStatusProsesUntukModel::where('id', $request->status_ajuan)->first();
        $disposisi = $datadisposisi->role_id_proses;
        $posisibrg = $datadisposisi->posisi_pb_proses;
        $sts_rspu = "$datadisposisi->sts_rspu";

        $id = $request->id_ajuan;
        if ($id) {            
            if($posisi==1)
            {
                $data = PengadaanBarangDetailModel::where('id_pb', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PengadaanBarangDetailHistoryModel();
                    $data->id_pbd = $baris->id;
                    $data->jmlh_pbdh_awal = $baris->jmlh_pbd_awal;
                    $data->jmlh_pbdh = $baris->jmlh_pbd_awal;
                    $data->id_rspd = 2;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();

                    $datadetail = PengadaanBarangDetailModel::where('id', $baris->id)->first();
                    $datadetail->id_rspd = 2;
                    $datadetail->save();
                }                
            }
            else
            {
                $data = PengadaanBarangDetailModel::where('id_pb', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PengadaanBarangDetailHistoryModel();
                    $data->id_pbd = $baris->id;
                    $data->jmlh_pbdh = $baris->jmlh_pbd;
                    $data->jmlh_pbdh_awal = $baris->jmlh_pbd_awal;
                    $data->ket_pdbh = $baris->ket_pdb;
                    $data->id_rspd = $baris->id_rspd;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();
                }
            }

            $datahistory = new PengadaanBarangHistoryModel();
            $datahistory->id_pb = $id;
            $datahistory->id_rspu = "$request->status_ajuan";
            $datahistory->ket_pbh = $request->keterangan;
            $datahistory->user_id = $user_id;
            $datahistory->role_id = $role_id;
            $datahistory->save();
            
            $datapermintaan = PengadaanBarangModel::where('id', $request->id_ajuan)->first();
            $datapermintaan->id_rspu = $request->status_ajuan;
            $datapermintaan->role_id = $disposisi;
            $datapermintaan->posisi_pb = $posisibrg;
            if($sts_rspu=="1")
            {
                $datapermintaan->status_pb = 2;
            }
            $datapermintaan->save();
            return response()->json(['status' => 1]);
        }
    }


    public function destroy(Request $request)
    {
        $data = PengadaanBarangModel::where('id', $request->id)->first();   
        $data->delete();         
        PengadaanBarangDetailModel::where('id_pb', $request->id)->delete();   
        return Response()->json(0);
    }

    public function getHistory(Request $request)
    {
        $id = $request->id;        
        // Ambil data history berdasarkan ID
        $historyData = PengadaanBarangHistoryModel::
        select('pengadaan_barang_history.role_id', 'pengadaan_barang_history.ket_pbh', 'pengadaan_barang_history.created_at', 'users.name',  'ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'role_pengguna.nama_rp')
        ->join('ref_status_proses_untuk','pengadaan_barang_history.id_rspu','=','ref_status_proses_untuk.id')
        ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id')
        ->join('users','pengadaan_barang_history.user_id','=','users.id')
        ->join('role_pengguna','pengadaan_barang_history.role_id','=','role_pengguna.id')
        ->where('id_pb', $id)->get();
        $data = PengadaanBarangModel::where('id', $request->id)->first(); 
        $nm_pb = $data->nm_pb;;
        
        // Kirim data history dalam format yang sesuai ke permintaan AJAX
        return response()->json(['historyData' => $historyData,'nm_pb'=>$nm_pb]);
    }

}