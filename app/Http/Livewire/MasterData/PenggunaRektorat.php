<?php

namespace App\Http\Livewire\MasterData;

use App\Models\FakultasModel;
use App\Models\RolePenggunaModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use App\Models\User;
use App\Models\VPenggunaRektoratModel;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class PenggunaRektorat extends Component
{
    use WithPagination;
    public $idUnit, $idUnitJabatan, $username, $nama, $jk, $nowa, $role,$tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;
    public $daftar_unit = [];
    public $daftar_unit_jabatan = [];
    protected $paginationTheme = 'bootstrap';
    public $cariUnitJabatan = null;

    public function render()
    {        
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        $this->daftar_unit = UnitRektoratModel::orderby('nm_ur')->get();

        return view('MasterData.PenggunaRektorat.index', [
            'data' => User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->join('role_pengguna','unit_rektorat_jabatan.role_id','=','role_pengguna.id_rp')
            ->where(function ($sub_query) {
                $sub_query->where('users.id_fkj', '=', '0')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })->orderBy('unit_rektorat.id_ur')->orderBy('users.id_urj')->paginate($page)
        ]);
    }

    public function updatedIdUnit($class_id)
    {
        $this->cariUnitJabatan = UnitRektoratJabatanModel::where('id_ur', $class_id)->get();
    }

    public function resetInputFields()
    {   
        $this->username = '';
        $this->nama = '';
        $this->jk = '';
        $this->nowa = '';
        $this->idUnit = '';
        $this->idUnitJabatan = '';
        $this->cariUnitJabatan = null;
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $this->validate([
            'username' => 'required',
            'nama' => 'required',
            'jk' => 'required',           
            'nowa' => 'required',
            'idUnitJabatan' => 'required'
        ]);
        $jumlah = User::where('id_urj', $this->idUnitJabatan)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $baris = UnitRektoratJabatanModel::where('id_urj', $this->idUnitJabatan)->first();
            $password = Hash::make('123456');
            $data = new User();
            $data->username = $this->username;
            $data->password = $password;
            $data->name = $this->nama;
            $data->jk = $this->jk;
            $data->nowa = $this->nowa;
            $data->role_id = $baris->role_id;
            $data->id_fkj = 0;
            $data->id_urj = $this->idUnitJabatan;
            $data->save();
            session()->flash('message', 'Data berhasil disimpan');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_tambah');
        }              
    }

    public function edit($id)
    {        
        $data = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->where('id', $id)->first();
        $this->tabel_id = $id;
        $this->daftar_unit_jabatan = UnitRektoratJabatanModel::where('id_ur','=',$data->id_ur)->orderby('role_id')->get();
        $this->username = $data->username;
        $this->nama = $data->name;
        $this->jk = $data->jk;
        $this->nowa = $data->nowa;
        $this->idUnit = $data->id_ur;
        $this->idUnitJabatan = $data->id_urj;
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
            'username' => 'required',
            'nama' => 'required',
            'jk' => 'required',           
            'nowa' => 'required',
            'idUnitJabatan' => 'required'              
        ]);
        if ($this->tabel_id) {
            /*$data = User::where('id', $this->tabel_id)->first();                   
            $data->username = $this->username;
            $data->name = $this->nama;
            $data->jk = $this->jk;
            $data->nowa = $this->nowa;
            $data->role_id = $this->role;
            $data->id_fk = $this->fakultas;
            $data->id_urj = $this->unit_kerja;
            $data->user_id = $user_id;             
            $data->save();
            session()->flash('message', 'Data berhasil diubah');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_ubah');*/
        $cekData = User::where('id', $this->tabel_id)->first();
        if($cekData->username == $this->username and  $cekData->name == $this->nama and $cekData->jk == $this->jk and $cekData->nowa == $this->nowa and $cekData->id_urj == $this->idUnitJabatan)
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->username != $this->username)
            {
                $jumlah = User::where('name', $this->nama)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'nama tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($cekData->id_urj != $this->idUnitJabatan)
            {
                $jumlah = User::where('id_urj', $this->idUnitJabatan)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'jabatan pada bagian rektorat tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            {                    
                $baris = UnitRektoratJabatanModel::where('id_urj', $this->idUnitJabatan)->first();
                $password = Hash::make('123456');
                $data = User::where('id', $this->tabel_id)->first();
                $data->username = $this->username;
                $data->password = $password;
                $data->name = $this->nama;
                $data->jk = $this->jk;
                $data->nowa = $this->nowa;
                $data->role_id = $baris->role_id;
                $data->id_fkj = 0;
                $data->id_urj = $this->idUnitJabatan;
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
        $data = User::where('id', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->name;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            User::where('id', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
