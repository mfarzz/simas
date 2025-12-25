<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\Employee;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\VBarangMasukModel;
use Illuminate\Http\Request;
use Datatables;

class BarangMasukKhususCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(VBarangMasukModel::
            get())
            ->addColumn('action', 'components.form-action.form-action-d')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        return view('BarangMasuk.Khusus.index',['daftar_kategori'=> $daftar_kategori]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = BarangModel::where('kd_sskt', $request->item)->pluck('kd_brg','nm_brg');
        return response()->json($idItem);
    }
    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $nilai = $request->jumlah * $request->harga;

        $databarang = BarangModel::where('kd_brg', $request->idItem)->first();
        $databarang->stok_brg = $databarang->stok_brg + $request->jumlah;
        $databarang->nilai_brg = $databarang->nilai_brg + $nilai;
        $databarang->save();

        $simpandata = new BarangMasukModel();
        $simpandata->kd_brg = $request->idItem;
        $simpandata->jmlh_awal_bm = $request->jumlah;
        $simpandata->sisa_bm = $request->jumlah;
        $simpandata->hrg_bm = $request->harga;
        $simpandata->tglperolehan_bm = $request->tgl_perolehan;
        $simpandata->tglbuku_bm = $request->tgl_buku;
        $simpandata->user_id = $user_id;
        $simpandata->save();
    }
    public function destroy(Request $request)
    {
        $data = BarangMasukModel::where('id', $request->id)->first();   
        $nilai = $data->jmlh_awal_bm * $data->hrg_bm;
        $databarang = BarangModel::where('kd_brg', $data->kd_brg)->first();
        $databarang->stok_brg = $databarang->stok_brg - $data->jmlh_awal_bm;
        $databarang->nilai_brg = $databarang->nilai_brg - $nilai;
        $databarang->save();
        $data->delete();         
        return Response()->json(0);
    }
}
