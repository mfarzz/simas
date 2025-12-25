<?php

namespace App\Http\Livewire\MasterData;

use App\Models\LancarKategoriModel;
use App\Models\LancarKategoriSubModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class RefLancarKategoriSub extends Component
{
    use WithPagination;
    public $id_lk, $id2, $kodeAset, $namaAset, $kode, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    protected $paginationTheme = 'bootstrap';

    public function mount($id_lk)
    {
        $this->id_lk = $id_lk;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->id_lk);
        $this->id2 = $des;
        $data = LancarKategoriModel::where('id_lk', $des)->first();
        $this->kodeAset = $data->kd_lk;
        $this->namaAset = $data->nm_lk;

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.RefLancarKategori.SubKategori.index', [
            'data' => LancarKategoriSubModel::
            leftjoin('v_lab_nilai2_subkategori','lancar_kategori_sub.id_lks','=','v_lab_nilai2_subkategori.v_id_lks')
            ->join('lancar_kategori','lancar_kategori_sub.id_lk','=','lancar_kategori.id_lk')
            ->where(function ($sub_query) {
                $sub_query->where('lancar_kategori_sub.id_lk', 'like', '%' . $this->id2 . '%')->where('kd_lks', 'ilike', '%' . $this->searchTerm . '%')->where('nm_lks', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('nm_lks')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {           
        $this->kode = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_lk);
        $this->id2 = $des;
        $this->validate([            
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = LancarKategoriSubModel::where('kd_lks', $this->kode)->orwhere('nm_lks', $this->nama)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new LancarKategoriSubModel();
            $data->id_lk = $this->id2;
            $data->kd_lks = $this->kode;
            $data->nm_lks = $this->nama;            
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
        $data = LancarKategoriSubModel::where('id_lks', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->kd_lks;
        $this->nama = $data->nm_lks;
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
            'nama' => 'required'
        ]);
        $cekData = LancarKategoriSubModel::where('id_lks', $this->tabel_id)->first();
        if($cekData->kd_lks == $this->kode and $cekData->nm_lks == $this->nama)
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
                if($cekData->kd_lks != $this->kode)
                {
                    $jumlah = LancarKategoriSubModel::where('kd_lks', $this->kode)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'kode tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($cekData->nm_lks != $this->nama)
                {
                    $jumlah = LancarKategoriSubModel::where('nm_lks', $this->nama)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'nama tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = LancarKategoriSubModel::where('id_lks', $this->tabel_id)->first();                   
                    $data->kd_lks = $this->kode;
                    $data->nm_lks = $this->nama;
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
        $data = LancarKategoriSubModel::
        where('id_lks', $id)->first();      
        $this->tabel_id = $id;        
        $this->kode = $data->kd_lks;
        $this->nama = $data->nm_lks;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = LancarKategoriSubModel::where('id_lks', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
