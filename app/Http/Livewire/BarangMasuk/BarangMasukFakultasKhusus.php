<?php

namespace App\Http\Livewire\BarangMasuk;

use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class BarangMasukFakultasKhusus extends Component
{
    use WithPagination;
    public $idKategori, $idSubkategori, $idItem, $barcode, $jumlah, $harga, $tgl_perolehan, $tgl_buku, $nama, $tabel_id, $no, $databarangmasuk, $nm_item;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kategori = [];
    protected $paginationTheme = 'bootstrap';
    public $cariSubkategori = null;
    public $cariItemkategori = null;

    public function render()
    {   
        $this->daftar_kategori = KelompokModel::orderby('kd_kl')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;
        return view('BarangMasuk.Khusus.Fakultas.index', [
            'data' => BarangMasukFakultasModel::join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->where(function ($sub_query) {
                $sub_query->where('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('tglperolehan_bmf','desc')->paginate($page)
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
        $this->idKategori = '';
        $this->idSubkategori = '';
        $this->idItem = '';
        $this->jumlah = '';
        $this->harga = '';
        $this->barcode = '';
        $this->tgl_perolehan = '';
        $this->tgl_buku = '';
        $this->cariSubkategori = null;
        $this->cariItemkategori = null;
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $this->validate([
            'jumlah' => 'required',
            'tgl_perolehan' => 'required',
            'tgl_buku' => 'required'
        ]);
        $nilai = $this->jumlah * $this->harga;
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

            }
            else
            {
                $stok = BarangMasukModel::where('kd_brg', $this->idItem)->sum('sisa_bm');
                //$stok = $databarang->sisa_bm;
                
                if($this->jumlah > $stok)
                {
                    session()->flash('message-gagal-insert', 'Maaf, jumlah yang anda entrikan melebihi jumlah stok yang tersedia');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
                else
                {
                    $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
                    ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
                    ->where('id', $user_id)->first();                   

                    $databmf = new BarangMasukFakultasModel();                    
                    $databmf->kd_brg = $this->idItem;
                    $databmf->id_fk = $datafakultas->id_fk;
                    $databmf->kd_lks = $datafakultas->kd_lks;
                    $databmf->jmlh_awal_bmf = $this->jumlah;
                    $databmf->sisa_bmf = $this->jumlah;
                    $databmf->tglperolehan_bmf = $this->tgl_perolehan;
                    $databmf->tglbuku_bmf = $this->tgl_buku;
                    $databmf->user_id = $user_id;
                    $databmf->save();
                    $id_bmf = $databmf->id_bmf;

                    $proses=0;
                    $jumlah_masuk = $this->jumlah;
                    $databarangmasuk = BarangMasukModel::where('kd_brg', $this->idItem)
                    ->where('sisa_bm',  '!=', 0)
                    ->orderBy('tglperolehan_bm','asc')
                    ->get();
                    foreach($databarangmasuk as $barisbm)
                    {
                        $sisabm = $barisbm->sisa_bm;
                        if($jumlah_masuk <= $sisabm)
                        {
                            $sisa = $sisabm - $jumlah_masuk;                            
                            if($proses==0)
                            {
                                $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                $databmupdate->sisa_bm = $sisa;
                                $databmupdate->save();

                                $databmfdetail = new BarangMasukFakultasDetailModel();
                                $databmfdetail->id_bmf = $id_bmf;
                                $databmfdetail->id_bm = $barisbm->id_bm;
                                $databmfdetail->jmlh_bmfd = $jumlah_masuk;
                                $databmfdetail->sisa_bmfd = $jumlah_masuk;
                                $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                                $databmfdetail->user_id = $user_id;
                                $databmfdetail->save();
                                $proses=1;                         
                            }
                        }                        
                        else
                        {
                            if($proses==0)
                            {
                                $sisa = $jumlah_masuk - $sisabm;
                                if($sisa >= 0)
                                {   
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = 0;                            
                                    $databmupdate->save();

                                    $databmfdetail = new BarangMasukFakultasDetailModel();
                                    $databmfdetail->id_bmf = $id_bmf;
                                    $databmfdetail->id_bm = $barisbm->id_bm;
                                    $databmfdetail->jmlh_bmfd = $sisabm;
                                    $databmfdetail->sisa_bmfd = $sisabm;
                                    $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                                    $databmfdetail->user_id = $user_id;
                                    $databmfdetail->save();
                                    $proses=0;
                                    $jumlah_masuk = $sisa;
                                }
                                else
                                {
                                    $sisa = $sisabm - $jumlah_masuk;
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = $sisa;                            
                                    $databmupdate->save();

                                    $databmfdetail = new BarangMasukFakultasDetailModel();
                                    $databmfdetail->id_bmf = $id_bmf;
                                    $databmfdetail->id_bm = $barisbm->id_bm;
                                    $databmfdetail->jmlh_bmfd = $sisabm;
                                    $databmfdetail->sisa_bmfd = $sisabm;
                                    $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                                    $databmfdetail->user_id = $user_id;
                                    $databmfdetail->save();                                
                                    $proses=1;
                                }
                            }
                        }
                    }
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
        $data = BarangMasukFakultasModel::join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->where('id_bmf', $id)->first();      
        $this->tabel_id = $id;        
        $this->nama = $data->nm_brg;       
        $this->jumlah = $data->jmlh_awal_bmf;  
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $databmfd = BarangMasukFakultasDetailModel::where('id_bmf', $this->tabel_id)->get();
            foreach($databmfd as $barisbmfd)
            {                
                $databm = BarangMasukModel::where('id_bm', $barisbmfd->id_bm)->first();
                $stok_skrg = $databm->sisa_bm + $barisbmfd->jmlh_bmfd;

                $databmupdate = BarangMasukModel::where('id_bm', $barisbmfd->id_bm)->first();                   
                $databmupdate->sisa_bm = $stok_skrg;                            
                $databmupdate->save();

                $datadeletebmfd = BarangMasukFakultasDetailModel::where('id_bmfd', $barisbmfd->id_bmfd)->first();
                $datadeletebmfd->delete();                  
            }

            $datadeletebmf = BarangMasukFakultasModel::where('id_bmf', $this->tabel_id)->first();
            $datadeletebmf->delete(); 

            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }

    public function detail($id)
    {        
        $this->databarangmasuk = BarangMasukFakultasDetailModel::join('barang_masuk_fakultas','barang_masuk_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')        
        ->where('barang_masuk_fakultas_detail.id_bmf', $id)->get();      

        $databmf = BarangMasukFakultasModel::        
        join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('id_bmf', $id)->first();
        $this->nm_item = $databmf->nm_brg;       
        //$this->tabel_id = $id;        
        //$this->nama = $data->nm_lki;       
        //$this->jumlah = $data->jmlh_bk;  
    }
}
