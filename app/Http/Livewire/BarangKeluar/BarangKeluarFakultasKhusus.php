<?php

namespace App\Http\Livewire\BarangKeluar;

use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangKeluarModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\FakultasJabatanModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\LancarKategoriItemModel;
use App\Models\LancarKategoriModel;
use App\Models\LancarKategoriSubModel;
use App\Models\SubSubKategoriModel;
use App\Models\UnitRektoratModel;
use Livewire\Component;
use Livewire\WithPagination;

class BarangKeluarFakultasKhusus extends Component
{
    use WithPagination;
    public $id_ur, $nm_penerima, $idKategori, $idSubkategori, $idItem, $barcode, $jumlah, $tgl_keluar, $nama, $tabel_id, $no, $nm_item;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kategori = [];
    public $databarangkeluar = [];
    public $daftar_unit_rektorat = [];
    protected $paginationTheme = 'bootstrap';
    public $cariSubkategori = null;
    public $cariItemkategori = null;

    public function render()
    {   
        $this->daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        $this->daftar_unit_rektorat = UnitRektoratModel::orderby('nm_ur')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('BarangKeluar.Khusus.Fakultas.index', [
            'data' => BarangKeluarFakultasModel::join('barang','barang_keluar_fakultas.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('fakultas','barang_keluar_fakultas.id_fk','=','fakultas.id_fk')
            ->where(function ($sub_query) {
                $sub_query->where('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('tglambil_bkf','desc')->paginate($page)
        ]);
    }

    public function updatedIdKategori($class_id)
    {
        $this->cariSubkategori = SubSubKategoriModel::where('kd_kl', $class_id)->orderby('kd_kl')->get();
        $this->cariItemkategori = null;
    }

    public function updatedIdSubkategori($class_id)
    {
        $this->cariItemkategori = BarangModel::where('kd_sskt', $class_id)->orderby('kd_brg')->get();
    }

    public function resetInputFields()
    {   
        $this->id_ur = '';
        $this->nm_penerima = '';
        $this->idKategori = '';
        $this->idSubkategori = '';
        $this->idItem = '';
        $this->jumlah = '';        
        $this->barcode = '';
        $this->tgl_keluar = '';
        $this->daftar_kategori = null;
        $this->cariSubkategori = null;
        $this->cariItemkategori = null;
        $this->nm_item = '';
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $this->validate([
            'nm_penerima' => 'required',
            'jumlah' => 'required',
            'tgl_keluar' => 'required'
        ]);

        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($this->barcode =="" and $this->idItem == "")
        {
            session()->flash('message-gagal-insert', 'maaf, untuk menyimpan data anda wajib pilih salah satu item bisa menggunakan barcode atau pilih item langsung.');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {
            if($this->barcode != "")
            {
                $jumlah = LancarKategoriItemModel::where('barcode_brg', $this->barcode)->count();
                if($jumlah==0)
                {            
                    session()->flash('message-gagal-insert', 'Maaf, kode barcode yang anda entrikan tidak ditemukan');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
                else
                {
                    $databarang = BarangModel::where('barcode_brg', $this->barcode)->first();
                    $databarang->stok_brg = $databarang->stok_brg + $this->jumlah;
                    $databarang->save();
                    $data = new BarangKeluarModel();
                    $data->id_ur = $this->id_ur;
                    $data->nm_penerima = $this->nm_penerima;
                    $data->kd_brg = $databarang->kd_brg;
                    $data->jmlh_awal_bm = $this->jumlah;
                    $data->sisa_bm = $this->jumlah;
                    $data->hrg_bm = $this->harga;
                    $data->tglperolehan_bm = $this->tgl_beli;                
                    $data->user_id = $user_id;
                    $data->save();
                    session()->flash('message', 'Data berhasil disimpan');
                    session()->flash('class', 'success');
                    $this->resetInputFields();
                    $this->emit('tutup_tambah');
                }
            }
            else
            {
                $stok = BarangMasukFakultasModel::where('kd_brg', $this->idItem)->sum('sisa_bmf');
                
                if($this->jumlah > $stok)
                {
                    session()->flash('message-gagal-insert', 'Maaf, jumlah yang anda keluarkan melebihi jumlah stok yang tersedia');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
                else
                {
                    $stok_terakhir = $stok - $this->jumlah;
                    $databkf = new BarangKeluarFakultasModel();       
                    $databkf->id_fk = $id_fk;
                    $databkf->nm_penerima = $this->nm_penerima;             
                    $databkf->kd_brg = $this->idItem;
                    $databkf->jmlh_bkf = $this->jumlah;
                    $databkf->tglambil_bkf = $this->tgl_keluar;
                    $databkf->user_id = $user_id;
                    $databkf->save();
                    $id_bkf = $databkf->id_bkf;

                    $totalbk=0;
                    $proses=0;
                    $tnilai_baru=0;
                    $awal=0;
                    $sisa=0;
                    $jumlah_keluar_fakultas = $this->jumlah;
                    $databarangmasukfakultas = BarangMasukFakultasModel::where('kd_brg', $this->idItem)
                    ->where('sisa_bmf',  '!=', 0)
                    ->orderBy('tglperolehan_bmf','asc')
                    ->get();
                    foreach($databarangmasukfakultas as $barisbmf)
                    {
                        if($proses==0)
                        {
                            $sisabmf = $barisbmf->sisa_bmf;
                            if($jumlah_keluar_fakultas <= $sisabmf)
                            {
                                $stokbmfterakhir = $sisabmf - $jumlah_keluar_fakultas;
                                $databmfupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                $databmfupdate->sisa_bmf = $stokbmfterakhir;
                                $databmfupdate->save();
                                
                                $proses1=0;
                                $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                                join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                                ->where('id_bmf', $barisbmf->id_bmf)
                                ->where('sisa_bmfd',  '!=', 0)
                                ->orderBy('tglperolehan_bm','asc')
                                ->get();
                                foreach($databarangmasukfakultasdetail as $barisbmfd)
                                {
                                    if($proses1==0)
                                    {
                                        $sisabmfd = $barisbmfd->sisa_bmfd;
                                        if($jumlah_keluar_fakultas <= $sisabmfd)
                                        {
                                            $harga = $barisbmfd->hrg_bm * $jumlah_keluar_fakultas;
                                            $databupdate = BarangModel::where('kd_brg', $this->idItem)->first();                   
                                            $databupdate->nilai_brg = $databupdate->nilai_brg - $harga;
                                            $databupdate->stok_brg = $databupdate->stok_brg - $jumlah_keluar_fakultas;
                                            $databupdate->save();

                                            $sisa2 = $sisabmfd - $jumlah_keluar_fakultas;
                                            $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();                   
                                            $databmfdupdate->sisa_bmfd = $sisa2;
                                            $databmfdupdate->save();

                                            $databkfddetail = new BarangKeluarFakultasDetailModel();
                                            $databkfddetail->id_bkf = $id_bkf;
                                            $databkfddetail->id_bmfd = $barisbmfd->id_bmfd;
                                            $databkfddetail->jmlh_bkfd = $jumlah_keluar_fakultas;
                                            $databkfddetail->user_id = $user_id;
                                            $databkfddetail->save();

                                            $proses1=1;
                                            $proses=1;
                                        }
                                        else
                                        {
                                            $sisa = $jumlah_keluar_fakultas - $sisabmfd;
                                            if($sisa >= 0)
                                            {   
                                                $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();                   
                                                $databmfdupdate->sisa_bmfd = 0;                            
                                                $databmfdupdate->save();

                                                $harga = $barisbmfd->hrg_bm * $sisabmfd;
                                                $databupdate = BarangModel::where('kd_brg', $this->idItem)->first();                   
                                                $databupdate->nilai_brg = $databupdate->nilai_brg - $harga;
                                                $databupdate->stok_brg = $databupdate->stok_brg - $sisabmfd;
                                                $databupdate->save();


                                                $databkfddetail = new BarangKeluarFakultasDetailModel();
                                                $databkfddetail->id_bkf = $id_bkf;
                                                $databkfddetail->id_bmfd = $barisbmfd->id_bmfd;
                                                $databkfddetail->jmlh_bkfd = $sisabmfd;
                                                $databkfddetail->user_id = $user_id;
                                                $databkfddetail->save();
                                                $proses1=0;
                                                $jumlah_keluar_fakultas = $sisa;
                                            }
                                            else
                                            {
                                                $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();                   
                                                $databmfdupdate->sisa_bmfd = $sisa;
                                                $databmfdupdate->save();

                                                $harga = $barisbmfd->hrg_bm * $sisa;
                                                $databupdate = BarangModel::where('kd_brg', $this->idItem)->first();                   
                                                $databupdate->nilai_brg = $databupdate->nilai_brg - $harga;
                                                $databupdate->stok_brg = $databupdate->stok_brg - $sisa;
                                                $databupdate->save();

                                                $databkfddetail = new BarangKeluarFakultasDetailModel();
                                                $databkfddetail->id_bkf = $id_bkf;
                                                $databkfddetail->id_bmfd = $barisbmfd->id_bmfd;
                                                $databkfddetail->jmlh_bkfd = $sisa;
                                                $databkfddetail->user_id = $user_id;
                                                $databkfddetail->save();
                                                $proses1=1;
                                                $proses=1;
                                                $jumlah_keluar_fakultas = $sisa;
                                            }
                                        }
                                    }
                                    else
                                    {   
                                        
                                    }
                                    
                                }
                            }
                            else
                            {
                                $databmfupdate = BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)->first();                   
                                $databmfupdate->sisa_bmf = 0;
                                $databmfupdate->save();

                                $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                                join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                                ->where('id_bmf', $barisbmf->id_bmf)
                                ->where('sisa_bmfd',  '!=', 0)
                                ->orderBy('tglperolehan_bm','asc')
                                ->get();
                                foreach($databarangmasukfakultasdetail as $barisbmfd)
                                {
                                    $harga = $barisbmfd->hrg_bm * $barisbmfd->sisa_bmfd;
                                    $databupdate = BarangModel::where('kd_brg', $this->idItem)->first();                   
                                    $databupdate->nilai_brg = $databupdate->nilai_brg - $harga;
                                    $databupdate->stok_brg = $databupdate->stok_brg - $barisbmfd->sisa_bmfd;
                                    $databupdate->save();

                                    $databkfddetail = new BarangKeluarFakultasDetailModel();
                                    $databkfddetail->id_bkf = $id_bkf;
                                    $databkfddetail->id_bmfd = $barisbmfd->id_bmfd;
                                    $databkfddetail->jmlh_bkfd = $barisbmfd->sisa_bmfd;
                                    $databkfddetail->user_id = $user_id;
                                    $databkfddetail->save();

                                    $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();                   
                                    $databmfdupdate->sisa_bmfd = 0;
                                    $databmfdupdate->save();
                                }
                                $jumlah_keluar_fakultas = $jumlah_keluar_fakultas - $sisabmf;
                                $proses=0;
                            }
                        }
                        
                        /*$sisabmf = $barisbmf->sisa_bmf;
                        if($jumlah_keluar_fakultas <= $sisabmf)
                        {                             
                            if($proses==0)
                            {
                                $proses2=0;
                                $databarangmasukfakultasdetail = BarangMasukFakultasDetailModel::
                                join('barang_masuk','barang_masuk_fakultas_detail.id_bm','=','barang_masuk.id_bm')
                                ->where('id_bmf', $barisbmf->id_bmf)
                                ->where('sisa_bmfd',  '!=', 0)
                                ->orderBy('tglperolehan_bm','asc')
                                ->get();
                                foreach($databarangmasukfakultasdetail as $barisbmfd)
                                {
                                    $sisabmfd = $barisbmfd->sisa_bmfd;
                                    if($proses2==0)
                                    {
                                        if($jumlah_keluar_fakultas <= $sisabmfd)
                                        {
                                            $sisa = $sisabmfd - $jumlah_keluar_fakultas;
                                            
                                            $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();                   
                                            $databmfdupdate->sisa_bmfd = $sisa;
                                            $databmfdupdate->save();

                                            $databkfdetail = new BarangKeluarFakultasDetailModel();
                                            $databkfdetail->id_bkf = $id_bkf;
                                            $databkfdetail->id_bmfd = $barisbmfd->id_bmfd;
                                            $databkfdetail->jmlh_bkfd = $jumlah_keluar_fakultas;
                                            $databkfdetail->user_id = $user_id;
                                            $databkfdetail->save();
                                            $proses=1;
                                            $proses2=1;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            $sisa = $jumlah_keluar_fakultas - $sisabmf;
                        }*/
                    }

                    /*$totalbk=0;
                    $proses=0;
                    $tnilai_baru=0;
                    $jumlah_keluar = $this->jumlah;
                    $databarangmasuk = BarangMasukModel::where('kd_brg', $this->idItem)
                    ->where('sisa_bm',  '!=', 0)
                    ->orderBy('tglperolehan_bm','asc')
                    ->get();
                    foreach($databarangmasuk as $barisbm)
                    {
                        $sisabm = $barisbm->sisa_bm;
                        if($jumlah_keluar <= $sisabm)
                        {
                            $sisa = $sisabm - $jumlah_keluar;                            
                            if($proses==0)
                            {
                                $nilai_baru = $barisbm->hrg_bm * $jumlah_keluar;
                                $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                $databmupdate->sisa_bm = $sisa;
                                $databmupdate->save();

                                $databkdetail = new BarangKeluarDetailModel();
                                $databkdetail->id_bk = $id_bk;
                                $databkdetail->id_bm = $barisbm->id_bm;
                                $databkdetail->jmlh_bkd = $jumlah_keluar;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();
                                $proses=1;
                                $databarang = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;

                                $databmupdateitemnilai = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                $tnilai_baru = $nilai_baru + $tnilai_baru;
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $tnilai_baru;
                                $databmupdateitemnilai->save();
                            }
                        }                        
                        else
                        {
                            if($proses==0)
                            {
                                $sisa = $jumlah_keluar - $sisabm;
                                if($sisa >= 0)
                                {          
                                    $nilai_baru = $barisbm->hrg_bm * $sisabm;
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = 0;                            
                                    $databmupdate->save();

                                    $databkdetail = new BarangKeluarDetailModel();
                                    $databkdetail->id_bk = $id_bk;
                                    $databkdetail->id_bm = $barisbm->id_bm;
                                    $databkdetail->jmlh_bkd = $sisabm;
                                    $databkdetail->user_id = $user_id;
                                    $databkdetail->save();
                                    $proses=0;
                                    $jumlah_keluar = $sisa;

                                    $databarang = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                    //$tnilai_baru = $nilai_baru + $tnilai_baru;
                                    $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                    $databmupdateitemnilai->save();
                                }
                                else
                                {
                                    $sisa = $sisabm - $jumlah_keluar;
                                    $nilai_baru = $barisbm->hrg_bm * $jumlah_keluar;
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = $sisa;                            
                                    $databmupdate->save();

                                    $databkdetail = new BarangKeluarDetailModel();
                                    $databkdetail->id_bk = $id_bk;
                                    $databkdetail->id_bm = $barisbm->id_bm;
                                    $databkdetail->jmlh_bkd = $sisabm;
                                    $databkdetail->user_id = $user_id;
                                    $databkdetail->save();                                
                                    $proses=1;

                                    $databarang = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;

                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $this->idItem)->first();
                                    $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                    $databmupdateitemnilai->save();
                                }
                            }
                        }

                    }

                    $databmupdateitem = BarangModel::where('kd_brg', $this->idItem)->first();                   
                    $databmupdateitem->stok_brg = $stok_terakhir;                    
                    $databmupdateitem->save();*/
                    session()->flash('message', 'Data berhasil disimpan');
                    session()->flash('class', 'success');
                    $this->resetInputFields();
                    $this->emit('tutup_tambah');
                }
            }
        }
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }    

    public function hapus($id)
    {        
        $data = BarangKeluarFakultasModel::
        join('barang','barang_keluar_fakultas.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->where('id_bkf', $id)->first();      
        $this->tabel_id = $id;        
        $this->nama = $data->nm_brg;       
        $this->jumlah = $data->jmlh_bkf;  
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $tnilai_baru=0;
            $tstok=0;
            $databkfd = BarangKeluarFakultasDetailModel::where('id_bkf', $this->tabel_id)->get();
            foreach($databkfd as $barisbkfd)
            {                
                $databmfd = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbkfd->id_bmfd)->first();
                $stok_skrg = $databmfd->sisa_bmfd + $barisbkfd->jmlh_bkfd;
                $nilai_baru = $barisbkfd->jmlh_bkfd * $databmfd->hrg_bmfd;

                $databmfdupdate = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbkfd->id_bmfd)->first();                   
                $databmfdupdate->sisa_bmfd = $stok_skrg;
                $databmfdupdate->save();

                $datadeletebkfd = BarangKeluarFakultasDetailModel::where('id_bkfd', $barisbkfd->id_bkfd)->first();
                $datadeletebkfd->delete();
                $tnilai_baru = $nilai_baru + $tnilai_baru;
                $tstok = $stok_skrg + $tstok;

                $databmfupdate = BarangMasukFakultasModel::
                where('id_bmf', $databmfdupdate->id_bmf)->first();                   
                $databmfupdate->sisa_bmf = $databmfupdate->sisa_bmf + $barisbkfd->jmlh_bkfd;
                $databmfupdate->save();
                
            }
            $data = BarangKeluarFakultasModel::where('id_bkf', $this->tabel_id)->first(); 
            $jmlh_bkf = $data->jmlh_bkf;
            
            $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
            $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bkf;
            $databmupdateitem->nilai_brg = $databmupdateitem->nilai_brg + $tnilai_baru;
            $databmupdateitem->save();
            $data->delete();

            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }

    public function detail($id)
    {        
        $this->databarangkeluar = BarangKeluarDetailModel::join('barang_masuk','barang_keluar_detail.id_bm','=','barang_masuk.id_bm')        
        ->where('barang_keluar_detail.id_bk', $id)->get();      

        $databk = BarangKeluarModel::
        join('barang','barang_keluar.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('id_bk', $id)->first();
        $this->nm_item = $databk->nm_brg;
    }
}
