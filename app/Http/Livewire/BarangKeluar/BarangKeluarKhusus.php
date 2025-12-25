<?php

namespace App\Http\Livewire\BarangKeluar;

use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\LancarKategoriItemModel;
use App\Models\LancarKategoriModel;
use App\Models\LancarKategoriSubModel;
use App\Models\SubSubKategoriModel;
use App\Models\UnitRektoratModel;
use Livewire\Component;
use Livewire\WithPagination;

class BarangKeluarKhusus extends Component
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
        return view('BarangKeluar.Khusus.index', [
            'data' => BarangKeluarModel::join('barang','barang_keluar.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('unit_rektorat','barang_keluar.id_ur','=','unit_rektorat.id_ur')
            ->where(function ($sub_query) {
                $sub_query->where('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('tglambil_bk','desc')->paginate($page)
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
        $this->validate([
            'id_ur' => 'required',
            'nm_penerima' => 'required',
            'jumlah' => 'required',
            'tgl_keluar' => 'required'
        ]);
        
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
                $stok = BarangMasukModel::where('kd_brg', $this->idItem)->sum('sisa_bm');
                
                if($this->jumlah > $stok)
                {
                    session()->flash('message-gagal-insert', 'Maaf, jumlah yang anda keluarkan melebihi jumlah stok yang tersedia');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
                else
                {
                    $stok_terakhir = $stok - $this->jumlah;
                    $databk = new BarangKeluarModel();       
                    $databk->id_ur = $this->id_ur;
                    $databk->nm_penerima = $this->nm_penerima;             
                    $databk->kd_brg = $this->idItem;
                    $databk->jmlh_bk = $this->jumlah;
                    $databk->tglambil_bk = $this->tgl_keluar;
                    $databk->user_id = $user_id;
                    $databk->save();
                    $id_bk = $databk->id_bk;

                    $totalbk=0;
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
                    $databmupdateitem->save();
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
        $data = BarangKeluarModel::join('barang','barang_keluar.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->where('id_bk', $id)->first();      
        $this->tabel_id = $id;        
        $this->nama = $data->nm_lki;       
        $this->jumlah = $data->jmlh_bk;  
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $tnilai_baru=0;
            $databkd = BarangKeluarDetailModel::where('id_bk', $this->tabel_id)->get();
            foreach($databkd as $barisbkd)
            {                
                $databm = BarangMasukModel::where('id_bm', $barisbkd->id_bm)->first();
                $stok_skrg = $databm->sisa_bm + $barisbkd->jmlh_bkd;
                $nilai_baru = $barisbkd->jmlh_bkd * $databm->hrg_bm;

                $databmupdate = BarangMasukModel::where('id_bm', $barisbkd->id_bm)->first();                   
                $databmupdate->sisa_bm = $stok_skrg;                            
                $databmupdate->save();

                $datadeletebkd = BarangKeluarDetailModel::where('id_bkd', $barisbkd->id_bkd)->first();
                $datadeletebkd->delete();                  
                $tnilai_baru = $nilai_baru + $tnilai_baru;
            }
            $data = BarangKeluarModel::where('id_bk', $this->tabel_id)->first(); 
            $jmlh_bk = $data->jmlh_bk;
            
            $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
            $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bk;
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
