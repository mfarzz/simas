<?php

namespace App\Http\Livewire\MasterData;

use App\Models\BarangModel;
use App\Models\JenisSatuanModel;
use App\Models\SubSubKategoriModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class Barang extends Component
{
    use WithPagination;
    public $kd_sskt, $kd_skt, $id2, $kodeKategori, $namaKategori, $kodeSubKategori, $namaSubKategori,$kodeSubSubKategori, $namaSubSubKategori, $kodeKelompok, $namaKelompok, $idJenis, $kode, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_jenis = [];
    protected $paginationTheme = 'bootstrap';

    public function mount($kd_sskt)
    {
        $this->kd_sskt = $kd_sskt;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->kd_sskt);
        $this->id2 = $des;
        $data = SubSubKategoriModel::
        join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
        ->join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
        ->where('subsubkategori.kd_sskt', $des)->first();        
        $this->kodeKategori = $data->kd_kt;
        $this->namaKategori = $data->nm_kt;
        $this->kodeSubKategori = $data->kd_skt;
        $this->namaSubKategori = $data->nm_skt;
        $this->kodeSubSubKategori = $data->kd_sskt;
        $this->namaSubSubKategori = $data->nm_sskt;
        $this->kodeKelompok = $data->kd_kl;
        $this->namaKelompok = $data->nm_kl;
        $this->kd_skt = Crypt::encryptString($data->kd_skt);

        $this->daftar_jenis = JenisSatuanModel::orderby('nm_js')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.Kategori.SubKategori.Barang.index', [
            'data' => BarangModel::            
            join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->leftjoin('v_lab_nilai1_item','barang.kd_brg','=','v_lab_nilai1_item.v_kd_brg')
            ->where(function ($sub_query) {
                $sub_query->where('subsubkategori.kd_sskt', 'like', '%' . $this->id2 . '%')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->orwhere('kd_brg', 'ilike', '%' . $this->searchTerm . '%')->orwhere('nm_brg', 'ilike', '%' . $this->searchTerm . '%');
                });
            })->orderBy('barang.kd_sskt')->orderBy('kd_brg')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {   
        $this->idJenis = '';        
        $this->kode = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_sskt);
        $this->id2 = $des;

        $data = SubSubKategoriModel::
        where('kd_sskt', $des)->first();        
        $kodeSubSubKategori = $data->kd_sskt;

        $this->validate([            
            'idJenis' => 'required',
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = BarangModel::where('kd_sskt', $this->id2)->where('kd_brg', $this->kode)->where('nm_brg', $this->nama)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new BarangModel();
            $data->kd_sskt = $this->id2;
            $data->kd_brg = "$kodeSubSubKategori$this->kode";
            $data->id_js = $this->idJenis;
            $data->no_brg = $this->kode;
            $data->nm_brg = $this->nama;            
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
        $data = BarangModel::where('id_brg', $id)->first();
        $this->tabel_id = $id;
        $this->idJenis = $data->id_js;
        $this->kode = $data->no_brg;
        $this->nama = $data->nm_brg;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_sskt);
        $this->id2 = $des;

        $this->validate([            
            'idJenis' => 'required',
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $data = SubSubKategoriModel::
        where('kd_sskt', $des)->first();        
        $kodeSubSubKategori = $data->kd_sskt;

        $cekData = BarangModel::where('id_brg', $this->tabel_id)->first();
        if($cekData->id_js == $this->idJenis and $cekData->kd_brg == $this->kode and $cekData->nm_brg == $this->nama and $cekData->barcode_brg == $this->barcode)
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
                if($cekData->kd_brg != $this->kode)
                {
                    $jumlah = BarangModel::where('kd_sskt', $this->id2)->where('kd_brg', $this->kode)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'kode tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($cekData->nm_brg != $this->nama)
                {
                    $jumlah = BarangModel::where('kd_sskt', $this->id2)->where('nm_brg', $this->nama)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'nama tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = BarangModel::where('id_brg', $this->tabel_id)->first();                   
                    $data->kd_sskt = $this->id2;
                    $data->id_js = $this->idJenis;
                    $data->kd_brg = "$kodeSubSubKategori$this->kode";
                    $data->no_brg = $this->kode;
                    $data->nm_brg = $this->nama;
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

    public function hapus($id)
    {        
        $data = BarangModel::
        where('id_brg', $id)->first();      
        $this->tabel_id = $id;        
        $this->kode = $data->kd_brg;
        $this->nama = $data->nm_brg;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = BarangModel::where('id_brg', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
