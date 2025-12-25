<?php

namespace App\Http\Livewire\MasterData;

use App\Models\KategoriModel;
use Livewire\Component;
use Livewire\WithPagination;

class Kategori extends Component
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

        return view('MasterData.Kategori.index', ['judul'=>'tes',
            'data' => KategoriModel::
            join('bidang', 'kategori.kd_bd','=','bidang.kd_bd')
            ->join('golongan', 'bidang.kd_gl','=','golongan.kd_gl')
            ->leftjoin('v_lab_nilai4_kategori', 'kategori.kd_kt','=','v_lab_nilai4_kategori.v_kd_kt')
            ->where(function ($sub_query) {
                $sub_query->where('nm_kt', 'ilike', '%' . $this->searchTerm . '%');
            })->orderBy('kd_kt')->paginate($page)
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
        $jumlah = KategoriModel::where('kd_kt', $this->kode)->orwhere('nm_kt', $this->nama)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan. Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new KategoriModel();            
            $data->kd_kt = "101$this->kode";
            $data->no_kt = $this->kode;
            $data->kd_bd = "101";
            $data->nm_kt = $this->nama;
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
        $data = KategoriModel::where('id_kt', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->no_kt;
        $this->nama = $data->nm_kt;
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
        $cekData = KategoriModel::where('id_kt', $this->tabel_id)->first();
        if($cekData->kd_kt == $this->kode and $cekData->nm_kt == $this->nama )
        {
            session()->flash('message', 'Tidak ada data yang diubah. Kode atau nama tidak boleh sama');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->kd_kt != $this->kode)
            {
                $jumlah = KategoriModel::where('kd_kt', $this->kode)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'kode tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($cekData->nm_kt != $this->nama)
            {
                $jumlah = KategoriModel::where('nm_kt', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = KategoriModel::where('id_kt', $this->tabel_id)->first(); 
                $data->kd_kt = "101$this->kode";
                $data->no_kt = $this->kode;
                $data->kd_bd = "101";
                $data->nm_kt = $this->nama;
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
        $data = KategoriModel::where('id_kt', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->kd_kt;
        $this->nama = $data->nm_kt;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            KategoriModel::where('id_kt', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
