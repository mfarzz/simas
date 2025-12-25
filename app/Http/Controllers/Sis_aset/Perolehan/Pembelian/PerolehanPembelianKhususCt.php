<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\Pembelian;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianKibModel;
use App\Models\AsetPerolehanRincianModel;
use App\Models\User;
use App\Models\VAsetPerolehanModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanPembelianKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if(request()->ajax()) {
            return datatables()->of(VAsetPerolehanModel::
           
            where('a_kd_ajkt','A02')
            ->get())
            ->addColumn('a_id_ap_en', function ($data) {
                $a_id_ap_en = Crypt::encryptString($data->a_id_ap);
                return $a_id_ap_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        return view('Sis_aset.Perolehan.Pembelian.Khusus.index',['daftar_kategori'=> $daftar_kategori]);
    }
}
