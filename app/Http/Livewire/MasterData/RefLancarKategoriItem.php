<?php

namespace App\Http\Livewire\MasterData;

use App\Models\JenisSatuanModel;
use App\Models\LancarKategoriItemModel;
use App\Models\LancarKategoriSubModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class RefLancarKategoriItem extends Component
{
    use WithPagination;
    public $id_lks, $id2, $idKategori, $kodeAset, $namaAset, $kodeSub, $namaSub, $kode, $nama, $barcode, $idSatuan, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_satuan = [];
    protected $paginationTheme = 'bootstrap';

    public function mount($id_lks)
    {
        $this->id_lks = $id_lks;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->id_lks);
        $this->id2 = $des;
        $data = LancarKategoriSubModel::
        join('lancar_kategori','lancar_kategori_sub.id_lk','=','lancar_kategori.id_lk')
        ->where('id_lks', $des)->first();
        $this->kodeAset = $data->kd_lk;
        $this->namaAset = $data->nm_lk;
        $this->kodeSub = $data->kd_lks;
        $this->namaSub = $data->kd_lks;
        $this->idKategori = Crypt::encryptString($data->id_lk);

        $this->daftar_satuan = JenisSatuanModel::get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.RefLancarKategori.Item.index', [
            'data' => LancarKategoriItemModel::
            join('jenis_satuan','lancar_kategori_item.id_js','=','jenis_satuan.id_js')
            ->join('lancar_kategori_sub','lancar_kategori_item.id_lks','=','lancar_kategori_sub.id_lks')
            ->join('lancar_kategori','lancar_kategori_sub.id_lk','=','lancar_kategori.id_lk')
            ->where(function ($sub_query) {
                $sub_query->where('lancar_kategori_sub.id_lks', 'like', '%' . $this->id2 . '%')->where('kd_lks', 'ilike', '%' . $this->searchTerm . '%')
                ->where('nm_lks', 'ilike', '%' . $this->searchTerm . '%')
                ->where('kd_lki', 'ilike', '%' . $this->searchTerm . '%')
                ->where('nm_lki', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('nm_lki')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {        
        $this->idSatuan = '';   
        $this->kode = '';
        $this->nama = '';
        $this->barcode = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_lks);
        $this->id2 = $des;
        $this->validate([            
            'kode' => 'required',
            'nama' => 'required',
            'idSatuan' => 'required'
        ]);
        $jumlah = LancarKategoriItemModel::where('kd_lki', $this->kode)->orwhere('nm_lki', $this->nama)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new LancarKategoriItemModel();
            $data->id_lks = $this->id2;
            $data->kd_lki = $this->kode;
            $data->nm_lki = $this->nama;
            $data->barcode_lki = $this->barcode;
            $data->id_js = $this->idSatuan;
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
        $data = LancarKategoriItemModel::where('id_lki', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->kd_lki;
        $this->nama = $data->nm_lki;
        $this->barcode = $data->barcode_lki;
        $this->idSatuan = $data->id_js;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $this->validate([            
            'kode' => 'required',
            'nama' => 'required',
            'idSatuan' => 'required'
        ]);
        $cekData = LancarKategoriItemModel::where('id_lki', $this->tabel_id)->first();
        if($cekData->kd_lki == $this->kode and $cekData->nm_lki == $this->nama)
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
                if($cekData->kd_lki != $this->kode)
                {
                    $jumlah = LancarKategoriItemModel::where('kd_lki', $this->kode)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'kode tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($cekData->nm_lki != $this->nama)
                {
                    $jumlah = LancarKategoriItemModel::where('nm_lki', $this->nama)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'nama tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = LancarKategoriItemModel::where('id_lki', $this->tabel_id)->first();                   
                    $data->kd_lki = $this->kode;
                    $data->nm_lki = $this->nama;
                    $data->barcode_lki = $this->barcode;
                    $data->id_js = $this->idSatuan;
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
        $data = LancarKategoriItemModel::
        where('id_lki', $id)->first();      
        $this->tabel_id = $id;        
        $this->idSatuan = $data->id_js;
        $this->kode = $data->kd_lki;
        $this->nama = $data->nm_lki;
        $this->barcode = $data->barcode_lki;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = LancarKategoriItemModel::where('id_lki', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
