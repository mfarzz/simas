<?php

namespace App\Http\Livewire\MasterData;

use App\Models\RefKegiatanMOdel;
use App\Models\RefStatusProsesModel;
use App\Models\RefStatusProsesUntukModel;
use App\Models\RolePenggunaModel;
use Livewire\Component;
use Livewire\WithPagination;

class RefStatusProsesUntuk extends Component
{
    use WithPagination;
    public $idStatus, $nama, $role_proses, $role_pilihan, $kondisi, $posisi_proses, $posisi_pilihan, $kegiatan, $status_data, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;  
    public $daftar_status = [];
    public $daftar_level = [];
    public $daftar_kegiatan = [];  
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        $this->daftar_status = RefStatusProsesModel::get();
        $this->daftar_level = RolePenggunaModel::get();
        $this->daftar_kegiatan = RefKegiatanMOdel::get();

        return view('MasterData.StatusProsesUntuk.index', [
            'data' => RefStatusProsesUntukModel::
            join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
            ->join('ref_kegiatan','ref_status_proses_untuk.id_rk','=','ref_kegiatan.id_rk')
            ->where(function ($sub_query) {
                $sub_query->where('nm_rspu', 'like', '%' . $this->searchTerm . '%');
            })->orderBy('ref_status_proses_untuk.id_rk')->orderBy('posisi_pb_proses')->paginate(10)
        ]);
    }

    public function resetInputFields()
    {   
        $this->idStatus = '';     
        $this->nama = '';
        $this->role_proses = '';
        $this->role_pilihan = '';
        $this->kondisi = '';
        $this->posisi_proses = '';
        $this->posisi_pilihan = '';
        $this->kegiatan = '';
        $this->status_data = '';
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $this->validate([
            'idStatus' => 'required',
            'nama' => 'required',
            'role_proses' => 'required',
            'role_pilihan' => 'required',
            'kondisi' => 'required',
            'posisi_proses' => 'required',
            'posisi_pilihan' => 'required',
            'kegiatan' => 'required',
            'status_data' => 'required'
        ]);
        $jumlah = RefStatusProsesUntukModel::
        where('id_rsp', $this->idStatus)
        ->where('nm_rspu', $this->nama)
        ->where('role_id_proses', $this->role_proses)
        ->where('role_id_pilihan', $this->role_pilihan)
        ->where('id_rk', $this->kegiatan)
        ->where('posisi_pb_proses', $this->posisi_proses)
        ->where('posisi_pb_pilihan', $this->posisi_pilihan)
        ->where('sts_rspu', $this->status_data)
        ->count();
        if($jumlah>0)
        {
            session()->flash('message-gagal-insert', 'Data gagal disimpan');
            session()->flash('class', 'danger');
            $this->emit('gagal');
        }
        else
        {
            $data = new RefStatusProsesUntukModel();
            $data->id_rsp = $this->idStatus;
            $data->nm_rspu = $this->nama;
            $data->role_id_proses = $this->role_proses;
            $data->role_id_pilihan = $this->role_pilihan;
            $data->kondisi_rspu = $this->kondisi;
            $data->posisi_pb_proses = $this->posisi_proses;
            $data->posisi_pb_pilihan = $this->posisi_pilihan; 
            $data->id_rk = $this->kegiatan;
            $data->sts_rspu = $this->status_data;
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
        $data = RefStatusProsesUntukModel::where('id_rspu', $id)->first();
        $this->tabel_id = $id;
        $this->idStatus = $data->id_rsp;
        $this->nama = $data->nm_rspu;
        $this->role_proses = $data->role_id_proses;
        $this->role_pilihan = $data->role_id_pilihan;
        $this->kondisi = $data->kondisi_rspu;
        $this->posisi_proses = $data->posisi_pb_proses;
        $this->posisi_pilihan = $data->posisi_pb_pilihan;
        $this->kegiatan = $data->id_rk;
        $this->status_data = $data->sts_rspu;
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
            'idStatus' => 'required',
            'nama' => 'required',
            'role_proses' => 'required',
            'role_pilihan' => 'required',
            'kondisi' => 'required',
            'posisi_proses' => 'required',
            'posisi_pilihan' => 'required',
            'kegiatan' => 'required',
            'status_data' => 'required'
        ]);
        if ($this->tabel_id) {
        $cekData = RefStatusProsesUntukModel::where('id_rspu', $this->tabel_id)->first();
        if($cekData->id_rsp == $this->idStatus and $cekData->nm_rspu == $this->nama and $cekData->role_id_proses == $this->role_proses and $cekData->role_id_pilihan == $this->role_pilihan and $cekData->kondisi_id == $this->kondisi and $cekData->posisi_pb_proses == $this->posisi_proses and $cekData->posisi_pb_pilihan == $this->posisi_pilihan and $cekData->id_rk == $this->kegiatan and $cekData->status_data == $this->status_data)
        {
            session()->flash('message', 'Tidak ada data yang diubah');
            session()->flash('class', 'info');
            $this->resetInputFields();
            $this->emit('tutup_ubah');
        }  
        else
        {
            $jumlah=0;               
            if($cekData->nm_rspu == $this->nama or $cekData->id_rsp == $this->idStatus or $cekData->role_id_proses == $this->role_proses or $cekData->role_id_pilihan == $this->pilihan  or $cekData->posisi_pb_proses == $this->posisi_proses or $cekData->posisi_pb_pilihan == $this->posisi_pilihan or $cekData->id_rk == $this->kegiatan)
            {
                $jumlah = RefStatusProsesUntukModel::
                where('id_rsp', $this->idStatus)
                ->where('nm_rspu', $this->nama)
                ->where('role_id_proses', $this->role_proses)
                ->where('role_id_pilihan', $this->role_pilihan)
                ->where('id_rk', $this->kegiatan)
                ->where('posisi_pb_proses', $this->posisi_proses)
                ->where('posisi_pb_pilihan', $this->posisi_pilihan)
                ->where('kondisi_rspu', $this->kondisi)
                ->count();
                if($jumlah>0)
                {
                    session()->flash('message-gagal-update', 'status, nama, level, dan penggunaan tidak boleh sama');
                    session()->flash('class', 'danger');                
                    $this->emit('gagal'); 
                }
            }
            if($jumlah==0)
            { 
                $data = RefStatusProsesUntukModel::where('id_rspu', $this->tabel_id)->first();                   
                $data->id_rsp = $this->idStatus;
                $data->nm_rspu = $this->nama;
                $data->role_id_proses = $this->role_proses;
                $data->role_id_pilihan = $this->role_pilihan;
                $data->kondisi_rspu = $this->kondisi;
                $data->posisi_pb_proses = $this->posisi_proses;
                $data->posisi_pb_pilihan = $this->posisi_pilihan;
                $data->id_rk = $this->kegiatan;
                $data->sts_rspu = $this->status_data;
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
        $data = RefStatusProsesUntukModel::where('id_rspu', $id)->first();
        $this->tabel_id = $id;
        $this->idStatus = $data->id_rsp;
        $this->nama = $data->nm_rspu;
        $this->role_proses = $data->role_id_proses;
        $this->role_pilihan = $data->role_id_pilihan;
        $this->kondisi = $data->kondisi_rspu;
        $this->posisi_proses = $data->posisi_pb_proses;
        $this->posisi_pilihan = $data->posisi_pb_pilihan;
        $this->kegiatan = $data->id_rk;
    }

    public function delete()
    {
        $id = $this->tabel_id;
        if ($id) {            
            RefStatusProsesUntukModel::where('id_rspu', $id)->delete();            
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }
}
