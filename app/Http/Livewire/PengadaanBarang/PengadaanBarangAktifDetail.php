<?php

namespace App\Http\Livewire\PengadaanBarang;

use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\PengadaanBarangDetailHistoryModel;
use App\Models\PengadaanBarangDetailModel;
use App\Models\PengadaanBarangModel;
use App\Models\RefStatusProsesDetailModel;
use App\Models\SubSubKategoriModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class PengadaanBarangAktifDetail extends Component
{
    use WithPagination;
    public $id_pb, $id2,$idKategori, $idSubkategori, $idItem, $namaKebutuhan, $tglPengadaan, $jumlah, $jumlah_awal, $estimasi_harga, $status_barang, $keterangan, $tabel_id, $no, $nomor;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kategori = [];
    public $tampil_status = [];
    public $tampil_histori = [];
    protected $paginationTheme = 'bootstrap';
    public $cariSubkategori = null;
    public $cariItemkategori = null;
    public $daftar_subkategori = [];
    public $daftar_barang = [];

    public function mount($id_pb)
    {
        $this->id_pb = $id_pb;
    }

    public function render()
    {   
        $role_id = auth()->user()->role_id;
        $des = Crypt::decryptString($this->id_pb);
        $this->id2 = $des;
        $data = PengadaanBarangModel::where('id_pb', $des)->first();
        $this->namaKebutuhan = $data->nm_pb;
        $this->tglPengadaan = substr($data->created_at,0,10);  

        $this->tampil_status = RefStatusProsesDetailModel::join('ref_status_proses_detail_untuk','ref_status_proses_detail.id_rspd','=','ref_status_proses_detail_untuk.id_rspd')->where('jns_rspd',1)->where('role_id',$role_id)->get();

        $this->daftar_kategori = KelompokModel::orderby('kd_kl')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('PengadaanBarang.Aktif.Detail.index', [
            'data' => PengadaanBarangDetailModel::join('ref_status_proses_detail','pengadaan_barang_detail.id_rspd','=','ref_status_proses_detail.id_rspd')
            ->join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where(function ($sub_query) {
                $sub_query->where('pengadaan_barang_detail.id_pb', 'like', '%' . $this->id2 . '%')
                ->where('barang.nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('pengadaan_barang_detail.kd_brg')->paginate($page)
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
        $this->estimasi_harga = '';
        $this->status_barang = '';
        $this->keterangan = '';
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_pb);
        $this->id2 = $des;
        $this->validate([
            'idItem' => 'required',
            'jumlah' => 'required',
            'estimasi_harga' => 'required'
        ]);
        $jumlah = PengadaanBarangDetailModel::where('kd_brg', $this->idItem)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'jenis barang tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {                    
            $data = new PengadaanBarangDetailModel();
            $data->id_pb = $this->id2;
            $data->id_rspd = 0;
            $data->kd_brg = $this->idItem;
            $data->jmlh_pbd_awal = $this->jumlah;                
            $data->hrg_estimasi_pbd = $this->estimasi_harga;
            $data->status_pbd = 0;
            $data->user_id = $user_id;
            $data->save();
            
            session()->flash('message', 'Data berhasil disimpan');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_tambah');   
        }    
    }

    public function edit($id)
    {        
        $data = PengadaanBarangDetailModel::join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('id_pbd', $id)->first();
        $this->tabel_id = $id;
        $this->daftar_subkategori = SubSubKategoriModel::where('kd_skt', $data->kd_skt)->orderby('kd_sskt')->get();
        $this->daftar_barang = BarangModel::where('kd_sskt', $data->kd_sskt)->orderby('kd_brg')->get();
        $this->idItem = $data->kd_brg;
        $this->idKategori = $data->kd_kl;
        $this->estimasi_harga = $data->hrg_estimasi_pbd;
        $this->jumlah = $data->jmlh_pbd;
        $this->jumlah_awal = $data->jmlh_pbd_awal;      
        $this->status_barang = $data->id_rspd;
        $this->keterangan = $data->ket_pdb;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        
        if(auth()->user()->role_id==2)
        {
            $this->validate([            
                'idItem' => 'required',
                'jumlah_awal' => 'required',
                'estimasi_harga' => 'required'
            ]);
            $cekData = PengadaanBarangDetailModel::where('id_pbd', $this->tabel_id)->first();
            if($cekData->kd_brg == $this->idItem and $cekData->jmlh_pbd_awal == $this->jumlah_awal and $cekData->hrg_estimasi_pbd == $this->estimasi_harga)
            {
                session()->flash('message', 'Tidak ada data yang diubah');
                session()->flash('class', 'info');
                $this->resetInputFields();
                $this->emit('tutup_ubah');
            }  
            else
            {
                if ($this->tabel_id) {
                    $jumlah=0;               
                    if($cekData->kd_brg != $this->idItem)
                    {
                        $jumlah = PengadaanBarangDetailModel::where('kd_brg', $this->idItem)->count();
                        if($jumlah>0)
                        {
                            session()->flash('message-gagal-update', 'jenis barang tidak boleh sama');
                            session()->flash('class', 'danger');                
                            $this->emit('gagal'); 
                        }
                    }                
                    if($jumlah==0)
                    {  
                        $data = PengadaanBarangDetailModel::where('id_pbd', $this->tabel_id)->first();                   
                        $data->kd_brg = $this->idItem;                        
                        $data->jmlh_pbd_awal = $this->jumlah_awal;                        
                        $data->hrg_estimasi_pbd = $this->estimasi_harga;
                        $data->id_rspd = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        
                        session()->flash('message', 'Data berhasil diubah');
                        session()->flash('class', 'success');
                        $this->resetInputFields();
                        $this->emit('tutup_ubah');
                    }            
                }
            }            
        }    
        else
        {
            $this->validate([
                'jumlah' => 'required',
                'status_barang' => 'required'
            ]);
            $data = PengadaanBarangDetailModel::where('id_pbd', $this->tabel_id)->first();
            $data->jmlh_pbd = $this->jumlah;
            $datahistori = PengadaanBarangDetailHistoryModel::where('id_pbd', $this->tabel_id)->orderby('id_pbdh','desc')->first();
            $data->jmlh_pbd_awal = $datahistori->jmlh_pbdh;
            $data->ket_pdb = $this->keterangan;
            $data->id_rspd = $this->status_barang;              
            $data->save();
            
            session()->flash('message', 'Data berhasil diubah');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }    
    }

    public function hapus($id)
    {        
        $data = PengadaanBarangDetailModel::join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')->
        where('id_pbd', $id)->first();      
        $this->tabel_id = $id;        
        $this->idItem = $data->nm_brg;        
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = PengadaanBarangDetailModel::where('id_pbd', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }

    public function histori($id)
    {        
        $data = PengadaanBarangDetailModel::join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')->
        where('id_pbd', $id)->first();      
        $this->tabel_id = $id;        
        $this->idItem = $data->nm_brg; 
        $this->nomor=0;
        $this->tampil_histori = PengadaanBarangDetailHistoryModel::
        select('barang.nm_brg','jenis_satuan.nm_js','pengadaan_barang_detail_history.created_at','ref_status_proses_detail.nm_rspd','pengadaan_barang_detail_history.ket_pdbh','pengadaan_barang_detail_history.jmlh_pbdh','pengadaan_barang_detail_history.jmlh_pbdh_awal', 'users.name', 'pengadaan_barang_detail_history.role_id', 'role_pengguna.nama_rp')
        ->join('ref_status_proses_detail','pengadaan_barang_detail_history.id_rspd','=','ref_status_proses_detail.id_rspd')
        ->join('pengadaan_barang_detail','pengadaan_barang_detail_history.id_pbd','=','pengadaan_barang_detail.id_pbd')
        ->join('barang','pengadaan_barang_detail.kd_brg','=','barang.kd_brg')
        ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
        ->join('users','pengadaan_barang_detail_history.user_id','=','users.id')
        ->join('role_pengguna','users.role_id','=','role_pengguna.id')
        ->where('pengadaan_barang_detail_history.id_pbd', $id)->get();
    }
}
