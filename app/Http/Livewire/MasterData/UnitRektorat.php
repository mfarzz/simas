<?php

namespace App\Http\Livewire\MasterData;

use App\Models\UnitRektoratModel;
use Livewire\Component;
use Livewire\WithPagination;

class UnitRektorat extends Component
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

        return view('MasterData.UnitRektorat.index', [
            'data' => UnitRektoratModel::
            where(function ($sub_query) {
                $sub_query->where('id_ur', '!=', 0)->where('nm_ur', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('nm_ur')->paginate($page)
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
        $jumlah = UnitRektoratModel::where('nm_ur', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new UnitRektoratModel();
            $data->nm_ur = $this->nama;
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
        $data = UnitRektoratModel::where('id_ur', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_ur;
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
        $cekData = UnitRektoratModel::where('id_ur', $this->tabel_id)->first();
        if($cekData->nm_ur == $this->nama )
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->nm_ur != $this->nama)
            {
                $jumlah = UnitRektoratModel::where('nm_ur', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = UnitRektoratModel::where('id_ur', $this->tabel_id)->first();                   
                $data->nm_ur = $this->nama;  
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
        $data = UnitRektoratModel::where('id_ur', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_ur;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            UnitRektoratModel::where('id_ur', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
