<?php

namespace App\Http\Livewire\MasterData;

use App\Models\LancarKategoriModel;
use Livewire\Component;
use Livewire\WithPagination;

class RefLancarKategori extends Component
{
    use WithPagination;
    public $kode, $nama, $tabel_id, $no;
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

        return view('MasterData.RefLancarKategori.index', ['judul'=>'tes',
            'data' => LancarKategoriModel::
            leftjoin('v_lab_nilai3_kategori','lancar_kategori.id_lk','=','v_lab_nilai3_kategori.v_id_lk')
            ->where(function ($sub_query) {
                $sub_query->where('nm_lk', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('kd_lk')->paginate($page)
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
        $this->validate([
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = LancarKategoriModel::where('kd_lk', $this->kode)->orwhere('nm_lk', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan. Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new LancarKategoriModel();
            $data->kd_lk = $this->kode;
            $data->nm_lk = $this->nama;
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
        $data = LancarKategoriModel::where('id_lk', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->kd_lk;
        $this->nama = $data->nm_lk;
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
        if ($this->tabel_id) {
        $cekData = LancarKategoriModel::where('id_lk', $this->tabel_id)->first();
        if($cekData->kd_lk == $this->kode and $cekData->nm_lk == $this->nama )
        {
            session()->flash('message', 'Tidak ada data yang diubah. Kode atau nama tidak boleh sama');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->kd_lk != $this->kode)
            {
                $jumlah = LancarKategoriModel::where('kd_lk', $this->kode)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'kode tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($cekData->nm_lk != $this->nama)
            {
                $jumlah = LancarKategoriModel::where('nm_lk', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = LancarKategoriModel::where('id_lk', $this->tabel_id)->first();                   
                $data->kd_lk = $this->kode;
                $data->nm_lk = $this->nama;
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
        $data = LancarKategoriModel::where('id_lk', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->kd_lk;
        $this->nama = $data->nm_lk;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            LancarKategoriModel::where('id_lk', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
