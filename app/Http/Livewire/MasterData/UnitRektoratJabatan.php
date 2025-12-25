<?php

namespace App\Http\Livewire\MasterData;

use App\Models\RolePenggunaModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class UnitRektoratJabatan extends Component
{
    use WithPagination;
    public $id_ur, $id2, $level, $namaUnitRektorat, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_level = [];
    protected $paginationTheme = 'bootstrap';

    public function mount($id_ur)
    {
        $this->id_ur = $id_ur;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->id_ur);
        $this->id2 = $des;
        $data = UnitRektoratModel::
        where('id_ur', $des)->first();
        $this->namaUnitRektorat = $data->nm_ur;

        $this->daftar_level = RolePenggunaModel::where('id_rp','=','5')->orwhere('id_rp','=','6')->orderby('id_rp')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.UnitRektorat.Jabatan.index', [
            'data' => UnitRektoratJabatanModel::            
            join('role_pengguna','unit_rektorat_jabatan.role_id','=','role_pengguna.id_rp')
            ->where(function ($sub_query) {
                $sub_query->where('unit_rektorat_jabatan.id_ur', 'like', '%' . $this->id2 . '%')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->orwhere('nm_urj', 'ilike', '%' . $this->searchTerm . '%')->orwhere('nm_urj', 'ilike', '%' . $this->searchTerm . '%');
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
        $des = Crypt::decryptString($this->id_ur);
        $this->id2 = $des;

        $this->validate([            
            'level' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = UnitRektoratJabatanModel::where('id_ur', $this->id2)->where('role_id', $this->level)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Level akses tidak boleh sama pada unit rektorat yang sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new UnitRektoratJabatanModel();
            $data->id_ur = $this->id2;
            $data->role_id = $this->level;
            $data->nm_urj = $this->nama;            
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
        $data = UnitRektoratJabatanModel::where('id_urj', $id)->first();
        $this->tabel_id = $id;
        $this->level = $data->role_id;
        $this->nama = $data->nm_urj;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->id_ur);
        $this->id2 = $des;

        $this->validate([            
            'level' => 'required',
            'nama' => 'required'
        ]);

        $cekData = UnitRektoratJabatanModel::where('id_urj', $this->tabel_id)->first();
        if($cekData->role_id == $this->level and $cekData->nm_urj == $this->nama)
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
                    $jumlah = UnitRektoratJabatanModel::where('id_ur', $this->id2)->where('role_id', $this->level)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'level akses tidak boleh sama pada unit rektorat yang sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = UnitRektoratJabatanModel::where('id_urj', $this->tabel_id)->first();                   
                    $data->id_ur = $this->id2;                    
                    $data->role_id = $this->level;
                    $data->nm_urj = $this->nama;
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
        $data = UnitRektoratJabatanModel::
        join('role_pengguna','unit_rektorat_jabatan.role_id','=','role_pengguna.id')
        ->where('id_urj', $id)->first();      
        $this->tabel_id = $id;        
        $this->level = $data->nama_rp;
        $this->nama = $data->nm_urj;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = UnitRektoratJabatanModel::where('id_urj', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
