<?php

namespace App\Http\Livewire\PermintaanBarang\Rektorat;

use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\PermintaanBarangRektoratDetailHistoryModel;
use App\Models\PermintaanBarangRektoratDetailModel;
use App\Models\PermintaanBarangRektoratModel;
use App\Models\RefStatusProsesDetailModel;
use App\Models\SubSubKategoriModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class PermintaanBarangRektoratAktifDetail extends Component
{
    use WithPagination;
    public $id_pbr, $id2,$idKategori, $idSubkategori, $idItem, $namaKebutuhan, $tglPermintaan, $jumlah, $jumlah_awal, $status_barang, $keterangan, $tabel_id, $no, $nomor;
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

    public function mount($id_pbr)
    {
        $this->id_pbr = $id_pbr;
    }

    public function render()
    {   
        $role_id = auth()->user()->role_id;
        $des = Crypt::decryptString($this->id_pbr);
        $this->id2 = $des;
        $data = PermintaanBarangRektoratModel::where('id_pbr', $des)->first();
        $this->namaKebutuhan = $data->nm_pbr;
        $this->tglPermintaan = substr($data->created_at,0,10);  

        $this->tampil_status = RefStatusProsesDetailModel::join('ref_status_proses_detail_untuk','ref_status_proses_detail.id_rspd','=','ref_status_proses_detail_untuk.id_rspd')->where('jns_rspd',2)->where('role_id',$role_id)->get();

        $this->daftar_kategori = KelompokModel::orderby('kd_kl')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('PermintaanBarang.Rektorat.Aktif.Detail.index', [
            'data' => PermintaanBarangRektoratDetailModel::join('ref_status_proses_detail','permintaan_barang_rektorat_detail.id_rspd','=','ref_status_proses_detail.id_rspd')
            ->join('barang','permintaan_barang_rektorat_detail.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where(function ($sub_query) {
                $sub_query->where('permintaan_barang_rektorat_detail.id_pbr', 'like', '%' . $this->id2 . '%')
                ->where('barang.nm_brg', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('permintaan_barang_rektorat_detail.kd_brg')->paginate($page)
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
        $this->status_barang = '';
        $this->keterangan = '';
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_pbr);
        $this->id2 = $des;
        $this->validate([
            'idItem' => 'required',
            'jumlah' => 'required'
        ]);
        $jumlah = PermintaanBarangRektoratDetailModel::where('kd_brg', $this->idItem)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'jenis barang tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {                    
            $data = new PermintaanBarangRektoratDetailModel();
            $data->id_pbr = $this->id2;
            $data->id_rspd = 0;
            $data->kd_brg = $this->idItem;
            $data->jmlh_pbrd_awal = $this->jumlah;                
            $data->status_pbrd = 0;
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
        $data = PermintaanBarangRektoratDetailModel::join('barang','permintaan_barang_rektorat_detail.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
        ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('id_pbrd', $id)->first();
        $this->tabel_id = $id;
        $this->daftar_subkategori = SubSubKategoriModel::where('kd_skt', $data->kd_skt)->orderby('kd_sskt')->get();
        $this->daftar_barang = BarangModel::where('kd_sskt', $data->kd_sskt)->orderby('kd_brg')->get();
        $this->idItem = $data->kd_brg;
        $this->idKategori = $data->kd_kl;
        $this->idSubkategori = $data->kd_sskt;
        $this->jumlah = $data->jmlh_pbrd;
        $this->jumlah_awal = $data->jmlh_pbrd_awal;      
        $this->status_barang = $data->id_rspd;
        $this->keterangan = $data->ket_pdrb;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        
        if(auth()->user()->role_id==5)
        {
            $this->validate([            
                'idItem' => 'required',
                'jumlah_awal' => 'required'
            ]);
            $cekData = PermintaanBarangRektoratDetailModel::where('id_pbrd', $this->tabel_id)->first();
            if($cekData->kd_brg == $this->idItem and $cekData->jmlh_pbrd_awal == $this->jumlah_awal)
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
                        $jumlah = PermintaanBarangRektoratDetailModel::where('kd_brg', $this->idItem)->count();
                        if($jumlah>0)
                        {
                            session()->flash('message-gagal-update', 'jenis barang tidak boleh sama');
                            session()->flash('class', 'danger');                
                            $this->emit('gagal'); 
                        }
                    }                
                    if($jumlah==0)
                    {  
                        $data = PermintaanBarangRektoratDetailModel::where('id_pbrd', $this->tabel_id)->first();                   
                        $data->kd_brg = $this->idItem;                        
                        $data->jmlh_pbrd_awal = $this->jumlah_awal;                        
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
            $data = PermintaanBarangRektoratDetailModel::where('id_pbrd', $this->tabel_id)->first();
            $data->jmlh_pbd = $this->jumlah;
            $datahistori = PermintaanBarangRektoratDetailHistoryModel::where('id_pbrd', $this->tabel_id)->orderby('id_pbrdh','desc')->first();
            $data->jmlh_pbrd_awal = $datahistori->jmlh_pbrdh;
            $data->ket_pdrb = $this->keterangan;
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
        $data = PermintaanBarangRektoratDetailModel::join('barang','permintaan_barang_rektorat_detail.kd_brg','=','barang.kd_brg')->
        where('id_pbrd', $id)->first();      
        $this->tabel_id = $id;        
        $this->idItem = $data->nm_brg;        
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = PermintaanBarangRektoratDetailModel::where('id_pbrd', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }

    public function histori($id)
    {        
        $data = PermintaanBarangRektoratDetailModel::join('barang','permintaan_barang_detail.kd_brg','=','barang.kd_brg')->
        where('id_pbrd', $id)->first();      
        $this->tabel_id = $id;        
        $this->idItem = $data->nm_brg; 
        $this->nomor=0;
        $this->tampil_histori = PermintaanBarangRektoratDetailHistoryModel::
        select('barang.nm_brg','jenis_satuan.nm_js','permintaan_barang_detail_history.created_at','ref_status_proses_detail.nm_rspd','permintaan_barang_detail_history.ket_pdrdh','permintaan_barang_detail_history.jmlh_pbrdh','permintaan_barang_detail_history.jmlh_pbrdh_awal', 'users.name', 'permintaan_barang_detail_history.role_id', 'role_pengguna.nama_rp')
        ->join('ref_status_proses_detail','permintaan_barang_detail_history.id_rspd','=','ref_status_proses_detail.id_rspd')
        ->join('permintaan_barang_detail','permintaan_barang_detail_history.id_pbrd','=','permintaan_barang_detail.id_pbrd')
        ->join('barang','permintaan_barang_detail.kd_brg','=','barang.kd_brg')
        ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
        ->join('users','permintaan_barang_detail_history.user_id','=','users.id')
        ->join('role_pengguna','users.role_id','=','role_pengguna.id')
        ->where('permintaan_barang_detail_history.id_pbrd', $id)->get();
    }
}
