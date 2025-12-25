<?php

namespace App\Http\Livewire\MasterData;

use App\Models\KelompokModel;
use Livewire\Component;
use Livewire\WithPagination;

class Kelompok extends Component
{
    use WithPagination;
    public $kode_lama, $kode_baru, $nama, $tabel_id, $no;
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

        return view('MasterData.Kelompok.index', [
            'data' => KelompokModel::
            where(function ($sub_query) {
                $sub_query->where('nm_kl', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('nm_kl')->paginate(10)
        ]);
    }

    public function resetInputFields()
    {        
        $this->kode_lama = '';
        $this->kode_baru = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $this->validate([
            'kode_lama' => 'required',
            'kode_baru' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = KelompokModel::where('kd_kl2', $this->kode_lama)->where('kd_kl', $this->kode_baru)->where('nm_kl', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan, data nama, kode baru, dan kode lama tidak boleh sama');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new KelompokModel();
            $data->kd_kl = $this->kode_baru;
            $data->kd_kl2 = $this->kode_lama;
            $data->nm_kl = $this->nama;
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
        $data = KelompokModel::where('id_kl', $id)->first();
        $this->tabel_id = $id;
        $this->kode_baru = $data->kd_kl;
        $this->kode_lama = $data->kd_kl2;
        $this->nama = $data->nm_kl;
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
            'kode_lama' => 'required',
            'kode_baru' => 'required',
            'nama' => 'required'                
        ]);
        if ($this->tabel_id) {
        $cekData = KelompokModel::where('id_kl', $this->tabel_id)->first();
        if($cekData->kd_kl == $this->kode_baru and $cekData->kd_kl2 == $this->kode_lama and $cekData->nm_kl == $this->nama)
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->kd_kl != $this->kode_baru)
            {
                $jumlah = KelompokModel::where('kd_kl', $this->kode_baru)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'Kode baru tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($cekData->kd_kl2 != $this->kode_lama)
            {
                $jumlah = KelompokModel::where('kd_kl2', $this->kode_lama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'Kode lama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($cekData->nm_kl != $this->nama)
            {
                $jumlah = KelompokModel::where('nm_kl', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'Nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = KelompokModel::where('id_kl', $this->tabel_id)->first();                   
                $data->kd_kl = $this->kode_baru;
                $data->kd_kl2 = $this->kode_lama;
                $data->nm_kl = $this->nama;
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
        $data = KelompokModel::where('id_kl', $id)->first();
        $this->tabel_id = $id;
        $this->kode_baru = $data->kd_kl;
        $this->kode_lama = $data->kd_kl2;
        $this->nama = $data->nm_kl;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            KelompokModel::where('id_kl', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
