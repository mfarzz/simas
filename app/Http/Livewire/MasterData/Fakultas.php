<?php

namespace App\Http\Livewire\MasterData;

use App\Models\FakultasModel;
use Livewire\Component;
use Livewire\WithPagination;

class Fakultas extends Component
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

        return view('MasterData.Fakultas.index', [
            'data' => FakultasModel::
            where(function ($sub_query) {
                $sub_query->where('id_fk', '!=', 0)->where('nm_fk', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('nm_fk')->paginate($page)
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
        $jumlah = FakultasModel::where('nm_fk', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new FakultasModel();
            $data->nm_fk = $this->nama;
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
        $data = FakultasModel::where('id_fk', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_fk;
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
        $cekData = FakultasModel::where('id_fk', $this->tabel_id)->first();
        if($cekData->nm_fk == $this->nama )
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->nm_fk != $this->nama)
            {
                $jumlah = FakultasModel::where('nm_fk', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = FakultasModel::where('id_fk', $this->tabel_id)->first();                   
                $data->nm_fk = $this->nama;  
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
        $data = FakultasModel::where('id_fk', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_fk;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            FakultasModel::where('id_fk', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
