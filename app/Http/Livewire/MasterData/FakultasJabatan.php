<?php

namespace App\Http\Livewire\MasterData;

use App\Models\FakultasJabatanModel;
use App\Models\FakultasModel;
use App\Models\RolePenggunaModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class FakultasJabatan extends Component
{
    use WithPagination;
    public $id_fk, $id2, $level, $namaFakultas, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_level = [];
    protected $paginationTheme = 'bootstrap';

    public function mount($id_fk)
    {
        $this->id_fk = $id_fk;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->id_fk);
        $this->id2 = $des;
        $data = FakultasModel::
        where('id_fk', $des)->first();
        $this->namaFakultas = $data->nm_fk;

        $this->daftar_level = RolePenggunaModel::where('id_rp','=','7')->orwhere('id_rp','=','8')->orderby('id_rp')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.Fakultas.Jabatan.index', [
            'data' => FakultasJabatanModel::            
            join('role_pengguna','fakultas_jabatan.role_id','=','role_pengguna.id_rp')
            ->where(function ($sub_query) {
                $sub_query->where('fakultas_jabatan.id_fk', 'like', '%' . $this->id2 . '%')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->orwhere('nm_fkj', 'ilike', '%' . $this->searchTerm . '%')->orwhere('nama_rp', 'ilike', '%' . $this->searchTerm . '%');
                });
            })->orderBy('role_pengguna.nama_rp')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {           
        $this->level = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_fk);
        $this->id2 = $des;

        $this->validate([            
            'level' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = FakultasJabatanModel::where('id_fk', $this->id2)->where('role_id', $this->level)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Level akses tidak boleh sama pada fakultas yang sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new FakultasJabatanModel();
            $data->id_fk = $this->id2;
            $data->role_id = $this->level;
            $data->nm_fkj = $this->nama;            
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
        $data = FakultasJabatanModel::where('id_fkj', $id)->first();
        $this->tabel_id = $id;
        $this->level = $data->role_id;
        $this->nama = $data->nm_fkj;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_fk);
        $this->id2 = $des;

        $this->validate([            
            'level' => 'required',
            'nama' => 'required'
        ]);

        $cekData = FakultasJabatanModel::where('id_fkj', $this->tabel_id)->first();
        if($cekData->role_id == $this->level and $cekData->nm_fkj == $this->nama)
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
                if($cekData->role_id != $this->level)
                {
                    $jumlah = FakultasJabatanModel::where('id_fk', $this->id2)->where('role_id', $this->level)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'level akses tidak boleh sama pada fakultas yang sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = FakultasJabatanModel::where('id_fkj', $this->tabel_id)->first();                   
                    $data->id_fk = $this->id2;                    
                    $data->role_id = $this->level;
                    $data->nm_fkj = $this->nama;
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
        $data = FakultasJabatanModel::
        join('role_pengguna','fakultas_jabatan.role_id','=','role_pengguna.id')
        ->where('id_fkj', $id)->first();      
        $this->tabel_id = $id;        
        $this->level = $data->nama_rp;
        $this->nama = $data->nm_fkj;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = FakultasJabatanModel::where('id_fkj', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
