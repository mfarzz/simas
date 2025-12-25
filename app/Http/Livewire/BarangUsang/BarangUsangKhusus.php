<?php

namespace App\Http\Livewire\BarangUsang;

use App\Models\BarangKeluarDetailModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\BarangUsangDetailModel;
use App\Models\BarangUsangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\UnitRektoratModel;
use Livewire\Component;
use Livewire\WithPagination;

class BarangUsangKhusus extends Component
{
    use WithPagination;
    public $id_ur, $ket_bu, $idKategori, $idSubkategori, $idItem, $barcode, $jumlah, $tgl_tentu, $nama, $tabel_id, $no, $nm_item;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kategori = [];
    public $databarangusang = [];
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
        return view('BarangUsang.Khusus.index', [
            'data' => BarangUsangModel::join('barang','barang_usang.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->where(function ($sub_query) {
                $sub_query->where('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('tgltentu_bu','desc')->paginate($page)
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
        $this->ket_bu = '';
        $this->idKategori = '';
        $this->idSubkategori = '';
        $this->idItem = '';
        $this->jumlah = '';        
        $this->barcode = '';
        $this->tgl_tentu = '';
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
            'ket_bu' => 'required',
            'jumlah' => 'required',
            'tgl_tentu' => 'required'
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
                /*$jumlah = LancarKategoriItemModel::where('barcode_brg', $this->barcode)->count();
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
                }*/
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
                    $databu = new BarangUsangModel();
                    $databu->ket_bu = $this->ket_bu;             
                    $databu->kd_brg = $this->idItem;
                    $databu->jmlh_bu = $this->jumlah;
                    $databu->tgltentu_bu = $this->tgl_tentu;
                    $databu->user_id = $user_id;
                    $databu->save();
                    $id_bu = $databu->id_bu;

                    $totalbu=0;
                    $proses=0;
                    $tnilai_baru=0;
                    $jumlah_usang = $this->jumlah;
                    $databarangmasuk = BarangMasukModel::where('kd_brg', $this->idItem)
                    ->where('sisa_bm',  '!=', 0)
                    ->orderBy('tglperolehan_bm','asc')
                    ->get();
                    foreach($databarangmasuk as $barisbm)
                    {
                        $sisabm = $barisbm->sisa_bm;
                        if($jumlah_usang <= $sisabm)
                        {
                            $sisa = $sisabm - $jumlah_usang;                            
                            if($proses==0)
                            {
                                $nilai_baru = $barisbm->hrg_bm * $jumlah_usang;
                                $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                $databmupdate->sisa_bm = $sisa;
                                $databmupdate->save();

                                $databkdetail = new BarangUsangDetailModel();
                                $databkdetail->id_bu = $id_bu;
                                $databkdetail->id_bm = $barisbm->id_bm;
                                $databkdetail->jmlh_bud = $jumlah_usang;
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
                                $sisa = $jumlah_usang - $sisabm;
                                if($sisa >= 0)
                                {          
                                    $nilai_baru = $barisbm->hrg_bm * $sisabm;
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = 0;                            
                                    $databmupdate->save();

                                    $databudetail = new BarangUsangDetailModel();
                                    $databudetail->id_bu = $id_bu;
                                    $databudetail->id_bm = $barisbm->id_bm;
                                    $databudetail->jmlh_bud = $sisabm;
                                    $databudetail->user_id = $user_id;
                                    $databudetail->save();
                                    $proses=0;
                                    $jumlah_usang = $sisa;

                                    $databarang = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                    $nilai_terakhir = $databarang->nilai_brg;
                                    
                                    $databmupdateitemnilai = BarangModel::where('kd_brg', $this->idItem)->first();                                
                                    //$tnilai_baru = $nilai_baru + $tnilai_baru;
                                    $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                    $databmupdateitemnilai->save();
                                }
                                else
                                {
                                    $sisa = $sisabm - $jumlah_usang;
                                    $nilai_baru = $barisbm->hrg_bm * $jumlah_usang;
                                    $databmupdate = BarangMasukModel::where('id_bm', $barisbm->id_bm)->first();                   
                                    $databmupdate->sisa_bm = $sisa;                            
                                    $databmupdate->save();

                                    $databkdetail = new BarangKeluarDetailModel();
                                    $databkdetail->id_bu = $id_bu;
                                    $databkdetail->id_bm = $barisbm->id_bm;
                                    $databkdetail->jmlh_bud = $sisabm;
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
        $data = BarangUsangModel::join('barang','barang_usang.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->where('id_bu', $id)->first();      
        $this->tabel_id = $id;        
        $this->nama = $data->nm_brg;       
        $this->jumlah = $data->jmlh_bu;  
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $tnilai_baru=0;
            $databud = BarangUsangDetailModel::where('id_bu', $this->tabel_id)->get();
            foreach($databud as $barisbud)
            {                
                $databm = BarangMasukModel::where('id_bm', $barisbud->id_bm)->first();
                $stok_skrg = $databm->sisa_bm + $barisbud->jmlh_bud;
                $nilai_baru = $barisbud->jmlh_bud * $databm->hrg_bm;

                $databmupdate = BarangMasukModel::where('id_bm', $barisbud->id_bm)->first();                   
                $databmupdate->sisa_bm = $stok_skrg;                            
                $databmupdate->save();

                $datadeletebud = BarangUsangDetailModel::where('id_bud', $barisbud->id_bud)->first();
                $datadeletebud->delete();                  
                $tnilai_baru = $nilai_baru + $tnilai_baru;
            }
            $data = BarangUsangModel::where('id_bu', $this->tabel_id)->first(); 
            $jmlh_bu = $data->jmlh_bu;
            
            $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
            $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bu;
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
        $this->databarangusang = BarangUsangDetailModel::join('barang_masuk','barang_usang_detail.id_bm','=','barang_masuk.id_bm')        
        ->where('barang_usang_detail.id_bu', $id)->get();      

        $databu = BarangUsangModel::
        join('barang','barang_usang.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('id_bu', $id)->first();
        $this->nm_item = $databu->nm_brg;
    }
}
