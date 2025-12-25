<?php

namespace App\Http\Livewire\MasterData;

use App\Models\FakultasJabatanModel;
use App\Models\FakultasModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class PenggunaFakultas extends Component
{
    use WithPagination;
    public $idFakultas, $idFakultasJabatan, $username, $nama, $jk, $nowa, $role,$tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;
    public $daftar_fakultas = [];
    public $daftar_fakultas_jabatan = [];
    protected $paginationTheme = 'bootstrap';
    public $cariFakultasJabatan = null;

    public function render()
    {        
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        $this->daftar_fakultas = FakultasModel::where('id_fk','!=',0)->orderby('nm_fk')->get();

        return view('MasterData.PenggunaFakultas.index', [
            'data' => User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->join('role_pengguna','fakultas_jabatan.role_id','=','role_pengguna.id_rp')
            ->where(function ($sub_query) {
                $sub_query->where('users.id_urj', '=', '0')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })->orderBy('fakultas.id_fk')->orderBy('users.id_fkj')->paginate($page)
        ]);
    }

    public function updatedIdFakultas($class_id)
    {
        $this->cariFakultasJabatan = FakultasJabatanModel::where('id_fk', $class_id)->get();
    }

    public function resetInputFields()
    {   
        $this->username = '';
        $this->nama = '';
        $this->jk = '';
        $this->nowa = '';
        $this->idFakultas = '';
        $this->idFakultasJabatan = '';
        $this->cariFakultasJabatan = null;
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $this->validate([
            'username' => 'required',
            'nama' => 'required',
            'jk' => 'required',           
            'nowa' => 'required',
            'idFakultasJabatan' => 'required'
        ]);
        $jumlah = User::where('id_fkj', $this->idFakultasJabatan)->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $baris = FakultasJabatanModel::where('id_fkj', $this->idFakultasJabatan)->first();
            $password = Hash::make('123456');
            $data = new User();
            $data->username = $this->username;
            $data->password = $password;
            $data->name = $this->nama;
            $data->jk = $this->jk;
            $data->nowa = $this->nowa;
            $data->role_id = $baris->role_id;
            $data->id_fkj = $this->idFakultasJabatan;
            $data->id_urj = 0;
            $data->save();
            session()->flash('message', 'Data berhasil disimpan');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_tambah');
        }              
    }

    public function edit($id)
    {        
        $data = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->where('id', $id)->first();
        $this->tabel_id = $id;
        $this->daftar_fakultas_jabatan = FakultasJabatanModel::where('id_fk','=',$data->id_fk)->orderby('role_id')->get();
        $this->username = $data->username;
        $this->nama = $data->name;
        $this->jk = $data->jk;
        $this->nowa = $data->nowa;
        $this->idFakultas = $data->id_fk;
        $this->idFakultasJabatan = $data->id_fkj;
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
            'idFakultasJabatan' => 'required'              
        ]);
        if ($this->tabel_id) {
        $cekData = User::where('id', $this->tabel_id)->first();
        if($cekData->username == $this->username and  $cekData->name == $this->nama and $cekData->jk == $this->jk and $cekData->nowa == $this->nowa and $cekData->id_fkj == $this->idFakultasJabatan)
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
            if($cekData->id_fkj != $this->idFakultasJabatan)
            {
                $jumlah = User::where('id_fkj', $this->idFakultasJabatan)->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'jabatan pada fakultas tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            {                    
                $baris = FakultasJabatanModel::where('id_fkj', $this->idFakultasJabatan)->first();
                $password = Hash::make('123456');
                $data = User::where('id', $this->tabel_id)->first();
                $data->username = $this->username;
                $data->password = $password;
                $data->name = $this->nama;
                $data->jk = $this->jk;
                $data->nowa = $this->nowa;
                $data->role_id = $baris->role_id;
                $data->id_fkj = $this->idFakultasJabatan;
                $data->id_urj = 0;
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
