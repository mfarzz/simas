<?php

namespace App\Http\Livewire\BarangMasuk;

use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use Livewire\Component;
use Livewire\WithPagination;

class BarangMasukKhusus extends Component
{
    use WithPagination;
    public $idKategori, $idSubkategori, $idItem, $barcode, $jumlah, $harga, $tgl_perolehan, $tgl_buku, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kategori = [];
    protected $paginationTheme = 'bootstrap';
    public $cariSubkategori = null;
    public $cariItemkategori = null;
    public $selectedValue = '';
    public $options = [
        'Option 1',
        'Option 2',
        'Option 3',
        // Tambahkan opsi lain sesuai kebutuhan Anda
    ];

    public function render()
    {   
        $this->daftar_kategori = KelompokModel::orderby('kd_kl')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;
        return view('BarangMasuk.Khusus.index', [
            'data' => BarangMasukModel::join('barang','barang_masuk.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->where(function ($sub_query) {
                $sub_query->where('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('tglperolehan_bm','desc')->paginate($page)
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
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $this->validate([
            'jumlah' => 'required',
            'harga' => 'required',
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
                $jumlah = BarangModel::where('barcode_brg', $this->barcode)->count();
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
                    $databarang->nilai_brg = $databarang->nilai_brg + $nilai;
                    $databarang->save();
                    $data = new BarangMasukModel();
                    $data->kd_brg = $databarang->kd_brg;
                    $data->jmlh_awal_bm = $this->jumlah;
                    $data->sisa_bm = $this->jumlah;
                    $data->hrg_bm = $this->harga;
                    $data->tglperolehan_bm = $this->tgl_perolehan;
                    $data->tglbuku_bm = $this->tgl_buku;
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
                $databarang = BarangModel::where('kd_brg', $this->idItem)->first();
                $databarang->stok_brg = $databarang->stok_brg + $this->jumlah;
                $databarang->nilai_brg = $databarang->nilai_brg + $nilai;
                $databarang->save();
                $data = new BarangMasukModel();
                $data->kd_brg = $this->idItem;
                $data->jmlh_awal_bm = $this->jumlah;
                $data->sisa_bm = $this->jumlah;
                $data->hrg_bm = $this->harga;
                $data->tglperolehan_bm = $this->tgl_perolehan;
                $data->tglbuku_bm = $this->tgl_buku;
                $data->user_id = $user_id;
                $data->save();
                session()->flash('message', 'Data berhasil disimpan');
                session()->flash('class', 'success');
                $this->resetInputFields();
                $this->emit('tutup_tambah');
            }
        }
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }    

    public function hapus($id)
    {        
        $data = BarangMasukModel::join('barang','barang_masuk.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->where('id_bm', $id)->first();      
        $this->tabel_id = $id;        
        $this->nama = $data->nm_brg;       
        $this->jumlah = $data->jmlh_awal_bm;  
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = BarangMasukModel::where('id_bm', $this->tabel_id)->first();   
            $nilai = $data->jmlh_awal_bm * $data->hrg_bm;
            $databarang = BarangModel::where('kd_brg', $data->kd_brg)->first();
            $databarang->stok_brg = $databarang->stok_brg - $data->jmlh_awal_bm;
            $databarang->nilai_brg = $databarang->nilai_brg - $nilai;
            $databarang->save();
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
