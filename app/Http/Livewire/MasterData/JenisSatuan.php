<?php

namespace App\Http\Livewire\MasterData;

use App\Models\JenisSatuanModel;
use Livewire\Component;
use Livewire\WithPagination;

class JenisSatuan extends Component
{
    use WithPagination;
    public $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        return view('MasterData.JenisSatuan.index', [
            'data' => JenisSatuanModel::
            where(function ($sub_query) {
                $sub_query->where('nm_js', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('nm_js')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {        
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $this->validate([
            'nama' => 'required'                
        ]);
        $jumlah = JenisSatuanModel::where('nm_js', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new JenisSatuanModel();
            $data->nm_js = $this->nama;
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
        $data = JenisSatuanModel::where('id_js', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_js;        
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
            'nama' => 'required'                
        ]);
        if ($this->tabel_id) {
        $cekData = JenisSatuanModel::where('id_js', $this->tabel_id)->first();
        if($cekData->nm_js == $this->nama )
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->nm_js != $this->nama)
            {
                $jumlah = JenisSatuanModel::where('nm_js', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = JenisSatuanModel::where('id_js', $this->tabel_id)->first();                   
                $data->nm_js = $this->nama;  
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
        $data = JenisSatuanModel::where('id_js', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_js;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            JenisSatuanModel::where('id_js', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
