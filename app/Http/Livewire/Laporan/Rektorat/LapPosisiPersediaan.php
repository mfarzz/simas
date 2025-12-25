<?php

namespace App\Http\Livewire\Laporan\Rektorat;

use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\FakultasJabatanModel;
use App\Models\KelompokModel;
use App\Models\LokasiModel;
use App\Models\TempBarangMasukModel;
use App\Models\VNilai3LapRektoratModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class LapPosisiPersediaan extends Component
{
    use WithPagination;
    public $user_id, $filter, $nama, $carilokasi, $lokasi, $cariproses, $caritglawal, $caritglakhir, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_lokasi = [];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $this->user_id = auth()->user()->id;

        /*$jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
        $datadeletetbm->delete();  
        }*/

        $this->daftar_lokasi = LokasiModel::orderby('kd_lks')->get();

        if($this->caritglakhir == ""){ $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;}

        if($this->caritglakhir == ""){ $tgl_akhir = date("Y-m-d"); $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;$tgl_akhir = $this->caritglakhir;}
        $this->filter = Crypt::encryptString("$tgl_akhir");
        $this->lokasi = Crypt::encryptString("$this->carilokasi");

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        /*return view('Laporan.Rektorat.PosisiPersediaan.index', [
            'data' => KelompokModel::
            leftjoin('v_lap_posisi_rektorat4','kelompok.kd_kl','=','v_lap_posisi_rektorat4.v_kd_kl')
            ->where(function ($sub_query) {
                $sub_query->where('v_lap_posisi_rektorat4.user_id', '=', $this->user_id);
            })->orderBy('kd_kl')->paginate($page)
        ]);*/
        return view('Laporan.Rektorat.PosisiPersediaan.index');
    }

    public function resetInputFields()
    {        
        $this->nama = '';        
    }

    public function proses()
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $this->user_id = auth()->user()->id;

        $this->daftar_lokasi = LokasiModel::orderby('kd_lks')->get();

        if($this->caritglakhir == ""){ $tgl_awal = date("Y-m-d"); $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;$tgl_akhir = $this->caritglakhir;}

        $this->filter = Crypt::encryptString("$tgl_akhir");  
        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
        $datadeletetbm->delete();  
        }

        /*if($this->carilokasi == "023170800677513009KD")
        {

                $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
                if($jumlah != 0)
                {
                $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
                $datadeletetbm->delete();  
                }

                $databarangmasuk = BarangMasukModel::
                where('tglperolehan_bm', '<=', $this->caritglakhir )
                ->orderBy('tglperolehan_bm','asc')
                ->get();
                foreach($databarangmasuk as $barisbm)
                {
                    if($barisbm->sisa_bm==$barisbm->jmlh_awal_bm)
                    {
                        $datatbm = new TempBarangMasukModel();                    
                        $datatbm->kd_brg = $barisbm->kd_brg;
                        $datatbm->sisa_tbm = $barisbm->jmlh_awal_bm;
                        $datatbm->hrg_tbm = $barisbm->hrg_bm;
                        $datatbm->user_id = $user_id;
                        $datatbm->save();
                    }
                    else
                    {
                        $tjmlh_bmfd = 0;
                        $databarangmasukfakultas = BarangMasukFakultasDetailModel::
                        join('barang_masuk_fakultas','barang_masuk_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('id_bm', '=', $barisbm->id_bm)
                        ->where('tglperolehan_bmf', '<=', $this->caritglakhir )
                        ->get();
                        foreach($databarangmasukfakultas as $barisbmf)                    
                        {
                            $tjmlh_bmfd = $barisbmf->jmlh_bmfd + $tjmlh_bmfd;
                        }

                        $tjmlh_bkd = 0;
                        $databarangkeluar = BarangKeluarDetailModel::
                        join('barang_keluar','barang_keluar_detail.id_bk','=','barang_keluar.id_bk')
                        ->where('id_bm', '=', $barisbm->id_bm)
                        ->where('tglambil_bk', '<=', $this->caritglakhir )
                        ->get();
                        foreach($databarangkeluar as $barisbkd)                    
                        {
                            $tjmlh_bkd = $barisbkd->jmlh_bkd + $tjmlh_bkd;
                        }
                        $jmlh_awal_bm = $barisbm->jmlh_awal_bm;
                        $diambil = $tjmlh_bmfd + $tjmlh_bkd;
                        $sisa_tbm = $jmlh_awal_bm - $diambil; 

                        $datatbm = new TempBarangMasukModel();                    
                        $datatbm->kd_brg = $barisbm->kd_brg;
                        $datatbm->sisa_tbm = $sisa_tbm;
                        $datatbm->hrg_tbm = $barisbm->hrg_bm;
                        $datatbm->user_id = $user_id;
                        $datatbm->save();
                    }
                }
        }
        else if($this->carilokasi == "023170800677513000KD")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
                if($jumlah > 0)
                {
                $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
                $datadeletetbm->delete();  
                }

                $databarangmasuk = BarangMasukModel::
                where('tglperolehan_bm', '<=', $this->caritglakhir )
                ->orderBy('tglperolehan_bm','asc')
                ->get();
                foreach($databarangmasuk as $barisbm)
                {
                    if($barisbm->sisa_bm==$barisbm->jmlh_awal_bm)
                    {
                        $datatbm = new TempBarangMasukModel();                    
                        $datatbm->kd_brg = $barisbm->kd_brg;
                        $datatbm->sisa_tbm = $barisbm->jmlh_awal_bm;
                        $datatbm->hrg_tbm = $barisbm->hrg_bm;
                        $datatbm->user_id = $user_id;
                        $datatbm->save();
                    }
                    else
                    {
                        $tjmlh_bmfd = 0;
                        $databarangmasukfakultas = BarangMasukFakultasDetailModel::
                        join('barang_masuk_fakultas','barang_masuk_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('id_bm', '=', $barisbm->id_bm)
                        ->where('tglperolehan_bmf', '<=', $this->caritglakhir )
                        ->get();
                        foreach($databarangmasukfakultas as $barisbmf)                    
                        {
                            $tjmlh_bmfd = $barisbmf->jmlh_bmfd + $tjmlh_bmfd;
                        }

                        $tjmlh_bkd = 0;
                        $databarangkeluar = BarangKeluarDetailModel::
                        join('barang_keluar','barang_keluar_detail.id_bk','=','barang_keluar.id_bk')
                        ->where('id_bm', '=', $barisbm->id_bm)
                        ->where('tglambil_bk', '<=', $this->caritglakhir )
                        ->get();
                        foreach($databarangkeluar as $barisbkd)                    
                        {
                            $tjmlh_bkd = $barisbkd->jmlh_bkd + $tjmlh_bkd;
                        }
                        $jmlh_awal_bm = $barisbm->jmlh_awal_bm;
                        $diambil = $tjmlh_bmfd + $tjmlh_bkd;
                        $sisa_tbm = $jmlh_awal_bm - $diambil; 

                        $datatbm = new TempBarangMasukModel();                    
                        $datatbm->kd_brg = $barisbm->kd_brg;
                        $datatbm->sisa_tbm = $sisa_tbm;
                        $datatbm->hrg_tbm = $barisbm->hrg_bm;
                        $datatbm->user_id = $user_id;
                        $datatbm->save();
                    }
                }

                $databarangmasukfakultas = BarangMasukFakultasModel::
                where('tglperolehan_bmf', '<=', $this->caritglakhir )
                ->orderBy('tglperolehan_bmf','asc')
                ->get();
                foreach($databarangmasukfakultas as $barisbmf)
                {                    
                    if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                    {
                        $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                        join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->get();
                        foreach($databarangmasukfakultasdetail as $barisbmfd)
                        {
                            $datatbmf = new TempBarangMasukModel();                    
                            $datatbmf->kd_brg = $barisbmfd->kd_brg;
                            $datatbmf->sisa_tbm = $barisbmfd->jmlh_bmfd;
                            $datatbmf->hrg_tbm = $barisbmfd->hrg_bm;
                            $datatbmf->user_id = $user_id;
                            $datatbmf->save();
                        }
                    }
                    else
                    {
                        $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                        join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->get();
                        foreach($databarangmasukfakultasdetail as $barisbmfd)
                        {
                            $tjmlh_bkfd = 0;
                            $databarangkeluarfakultas = BarangMasukFakultasDetailModel::
                            join('barang_masuk_fakultas','barang_masuk_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
                            ->where('id_bmfd', '=', $barisbmfd->id_bmfd)
                            ->where('tglambil_bkf', '<=', $this->caritglakhir )
                            ->get();
                            foreach($databarangkeluarfakultas as $barisbkfd)                    
                            {
                                $tjmlh_bkfd = $barisbmf->jmlh_bkfd + $tjmlh_bkfd;
                            }
                        }
                        $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                        $diambil = $tjmlh_bkfd + $tjmlh_bkfd;
                        $sisa_tbm = $jmlh_awal_bmf - $diambil; 

                        $datatbmf = new TempBarangMasukModel();                    
                        $datatbmf->kd_brg = $barisbmf->kd_brg;
                        $datatbmf->sisa_tbm = $barisbmf->jmlh_awal_bmf;
                        $datatbmf->hrg_tbm = $barisbmf->hrg_bm;
                        $datatbmf->user_id = $user_id;
                        $datatbmf->save();
                    }
                }
        }
        else if($this->carilokasi == "")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
            $datadeletetbm->delete();  
            }
        }
        else
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
            if($jumlah > 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
            $datadeletetbm->delete();  
            }

            $databarangmasukfakultas = BarangMasukFakultasModel::
                where('tglperolehan_bmf', '<=', $this->caritglakhir )
                ->orderBy('tglperolehan_bmf','asc')
                ->get();
                foreach($databarangmasukfakultas as $barisbmf)
                {                    
                    if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                    {
                        $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                        leftjoin('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->get();
                        foreach($databarangmasukfakultasdetail as $barisbmfd)
                        {
                            $datatbmf = new TempBarangMasukModel();                    
                            $datatbmf->kd_brg = $barisbmfd->kd_brg;
                            $datatbmf->sisa_tbm = $barisbmfd->jmlh_bmfd;
                            $datatbmf->hrg_tbm = $barisbmfd->hrg_bm;
                            $datatbmf->user_id = $user_id;
                            $datatbmf->save();
                        }
         
                    }
                    else
                    {
                        $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                        join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->get();
                        foreach($databarangmasukfakultasdetail as $barisbmfd)
                        {
                            $tjmlh_bkfd = 0;
                            $databarangkeluarfakultas = BarangMasukFakultasDetailModel::
                            join('barang_masuk_fakultas','barang_masuk_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
                            ->where('id_bmfd', '=', $barisbmfd->id_bmfd)
                            ->where('tglambil_bkf', '<=', $this->caritglakhir )
                            ->get();
                            foreach($databarangkeluarfakultas as $barisbkfd)                    
                            {
                                $tjmlh_bkfd = $barisbmf->jmlh_bkfd + $tjmlh_bkfd;
                            }
                        }
                        $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                        $diambil = $tjmlh_bkfd + $tjmlh_bkfd;
                        $sisa_tbm = $jmlh_awal_bmf - $diambil; 

                        $datatbmf = new TempBarangMasukModel();                    
                        $datatbmf->kd_brg = $barisbmf->kd_brg;
                        $datatbmf->sisa_tbm = $barisbmf->jmlh_awal_bmf;
                        $datatbmf->hrg_tbm = $barisbmf->hrg_bm;
                        $datatbmf->user_id = $user_id;
                        $datatbmf->save();
                    }
                }
        }*/
        return redirect()->to('/lap-posisi-persediaan');
    }
}
