<?php

namespace App\Http\Livewire\PermintaanBarang\Rektorat;

use App\Models\PermintaanBarangRektoratDetailHistoryModel;
use App\Models\PermintaanBarangRektoratDetailModel;
use App\Models\PermintaanBarangRektoratHistoryModel;
use App\Models\PermintaanBarangRektoratModel;
use App\Models\RefPosisiKegiatanModel;
use App\Models\RefStatusProsesModel;
use App\Models\RefStatusProsesUntukModel;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PermintaanBarangRektoratAktif extends Component
{
    use WithPagination;
    public $id_ur, $nama, $tgl, $role_id, $tabel_id, $no, $status_ajuan, $posisi, $kondisi, $keterangan, $nomor;
    public $searchTerm, $halaman;
    public $currentPage = 1;  
    public $daftar_status = [];
    public $tampil_histori = [];  
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        $this->role_id = auth()->user()->role_id;
        $this->id = auth()->user()->id;
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        $dataposisi = RefPosisiKegiatanModel::where('role_id', $this->role_id)->where('id_rk',2)->first();
        $this->posisi = $dataposisi->posisi_rpk;
        $this->kondisi = $dataposisi->kondisi_rpk;

        $this->daftar_status = RefStatusProsesModel::
        join('ref_status_proses_untuk','ref_status_proses.id_rsp','=','ref_status_proses_untuk.id_rsp')
        ->join('role_pengguna','ref_status_proses_untuk.role_id_pilihan','=','role_pengguna.id')
        ->where('ref_status_proses_untuk.posisi_pb_pilihan', '=', $this->posisi)        
        ->where('ref_status_proses_untuk.id_rk', '=',2)
        ->orderBy('nm_rsp')->orderBy('ref_status_proses.id_rsp')->get();    
        
        if($this->role_id == 5 or $this->role_id == 6)
        {
            $dataunitkerja = User::join('unit_rektorat_jabatan','unit_rektorat_jabatan.id_urj','=','users.id_urj')
            ->where('id', $this->id)->first();
            $this->id_ur = $dataunitkerja->id_ur;
            
            return view('PermintaanBarang.Rektorat.Aktif.index', [
                'data' => PermintaanBarangRektoratModel::select('permintaan_barang_rektorat.id_pbr', 'permintaan_barang_rektorat.nm_pbr', 'permintaan_barang_rektorat.tgl_pbr', 'unit_rektorat.nm_ur', 'ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'ref_status_proses_untuk.role_id_proses', 'role_pengguna.nama_rp', 'v_permintaan_barang_rektorat_detail_belum_diproses.jumlah_belum_diproses')
                ->join('ref_status_proses_untuk','permintaan_barang_rektorat.id_rspu','=','ref_status_proses_untuk.id_rspu')
                ->join('role_pengguna','ref_status_proses_untuk.role_id_proses','=','role_pengguna.id')
                ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
                ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
                ->leftjoin('v_permintaan_barang_rektorat_detail_belum_diproses','permintaan_barang_rektorat.id_pbr','=','v_permintaan_barang_rektorat_detail_belum_diproses.id_pbr')
                ->where(function ($sub_query) {
                    $sub_query->where('permintaan_barang_rektorat.id_ur', $this->id_ur)
                    ->Where(function ($sub_sub_query) {
                        $sub_sub_query->where('nm_pbr', 'ilike', '%' . $this->searchTerm . '%')
                        ->where('permintaan_barang_rektorat.status_pbr', '=', 1)->where('permintaan_barang_rektorat.role_id', '=', $this->role_id);
                    });
                })->orderBy('nm_pbr')->paginate($page)
            ]);
        }
        else
        {
            return view('PermintaanBarang.Rektorat.Aktif.index', [
                'data' => PermintaanBarangRektoratModel::select('permintaan_barang_rektorat.id_pbr', 'permintaan_barang_rektorat.nm_pbr', 'permintaan_barang_rektorat.tgl_pbr', 'unit_rektorat.nm_ur', 'ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'ref_status_proses_untuk.role_id_proses', 'role_pengguna.nama_rp', 'v_permintaan_barang_rektorat_detail_belum_diproses.jumlah_belum_diproses')
                ->join('ref_status_proses_untuk','permintaan_barang_rektorat.id_rspu','=','ref_status_proses_untuk.id_rspu')
                ->join('role_pengguna','ref_status_proses_untuk.role_id_proses','=','role_pengguna.id')
                ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
                ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
                ->leftjoin('v_permintaan_barang_rektorat_detail_belum_diproses','permintaan_barang_rektorat.id_pbr','=','v_permintaan_barang_rektorat_detail_belum_diproses.id_pbr')
                ->where(function ($sub_query) {
                    $sub_query->where('nm_pbr', 'ilike', '%' . $this->searchTerm . '%')
                    ->where('permintaan_barang_rektorat.status_pbr', '=', 1)->where('permintaan_barang_rektorat.role_id', '=', $this->role_id);
                })->orderBy('nm_pbr')->paginate($page)
            ]);
        }
        
        
    }

    public function resetInputFields()
    {        
        $this->nama = '';
        $this->kondisi = '';
        $this->tgl = '';
        $this->status_ajuan = '';
        $this->keterangan = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $this->validate([
            'nama' => 'required',
            'tgl' => 'required'
        ]);

        $dataunitkerja = User::join('unit_rektorat_jabatan','unit_rektorat_jabatan.id_urj','=','users.id_urj')
        ->where('id', $user_id)->first();

        $datastatus = RefStatusProsesUntukModel::where('id_rsp', 0)->where('id_rk', 2)->first();

        $data = new PermintaanBarangRektoratModel();
        $data->id_ur = $dataunitkerja->id_ur;
        $data->nm_pbr = $this->nama;
        $data->tgl_pbr = $this->tgl;
        $data->id_rspu = $datastatus->id_rspu;
        $data->role_id = $role_id;
        $data->status_pbr = 1;
        $data->posisi_pbr = 1;
        $data->user_id = $user_id;
        $data->save();
        $id_pbr = $data->id_pbr;

        $datahistory = new PermintaanBarangRektoratHistoryModel();
        $datahistory->id_pbr = $id_pbr;
        $datahistory->id_rspu = $datastatus->id_rspu;
        $datahistory->ket_pbrh = '';
        $datahistory->user_id = $user_id;
        $datahistory->role_id = $role_id;
        $datahistory->save();

        session()->flash('message', 'Data berhasil disimpan');
        session()->flash('class', 'success');
        $this->resetInputFields();
        $this->emit('tutup_tambah');         
    }

    public function edit($id)
    {        
        $data = PermintaanBarangRektoratModel::where('id_pbr', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pbr;
        $this->tgl = $data->tgl_pbr;
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
        if ($this->tabel_id) 
        {
        $data = PermintaanBarangRektoratModel::where('id_pbr', $this->tabel_id)->first();
        $data->nm_pbr = $this->nama;
        $data->tgl_pbr = $this->tgl;               
        $data->user_id = $user_id;             
        $data->save();
        session()->flash('message', 'Data berhasil diubah');
        session()->flash('class', 'success');
        $this->resetInputFields();
        $this->emit('tutup_ubah');
        }
    }

    public function hapus($id)
    {
        $data = PermintaanBarangRektoratModel::where('id_pbr', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pbr;
        $this->tgl = $data->tgl_pbr;
    }

    public function delete()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id = $this->tabel_id;
        if ($id) {            
            PermintaanBarangRektoratModel::where('id_pbr', $id)->delete();  
            PermintaanBarangRektoratDetailModel::where('id_pbr', $id)->delete();          
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }

    public function ajukan($id)
    {        
        $data = PermintaanBarangRektoratModel::where('id_pbr', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pbr;
        $this->status_ajuan = $data->id_rspu;
    }

    public function prosesajukan()
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        $this->validate([
            'status_ajuan' => 'required'                
        ]);
        $this->role_id = auth()->user()->role_id;

        $dataposisi = RefPosisiKegiatanModel::where('role_id', $this->role_id)->where('id_rk',2)->first();
        $posisi = $dataposisi->posisi_rpk;

        $datadisposisi = RefStatusProsesUntukModel::where('id_rspu', $this->status_ajuan)->first();
        $disposisi = $datadisposisi->role_id_proses;
        $posisibrg = $datadisposisi->posisi_pb_proses;
        $sts_rspu = $datadisposisi->sts_rspu;

        $id = $this->tabel_id;
        if ($id) {            
            if($posisi==1)
            {
                $data = PermintaanBarangRektoratDetailModel::where('id_pbr', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PermintaanBarangRektoratDetailHistoryModel();
                    $data->id_pbrd = $baris->id_pbrd;
                    $data->jmlh_pbrdh = $baris->jmlh_pbrd_awal;
                    $data->jmlh_pbrdh_awal = $baris->jmlh_pbrd_awal;
                    $data->id_rspd = 2;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();

                    $datadetail = PermintaanBarangRektoratDetailModel::where('id_pbrd', $baris->id_pbrd)->first();
                    $datadetail->id_rspd = 2;
                    $datadetail->save();
                }
            }
            else
            {
                $data = PermintaanBarangRektoratDetailModel::where('id_pbr', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PermintaanBarangRektoratDetailHistoryModel();
                    $data->id_pbrd = $baris->id_pbrd;
                    $data->jmlh_pbrdh = $baris->jmlh_pbrd;
                    $data->jmlh_pbrdh_awal = $baris->jmlh_pbrd_awal;
                    $data->ket_pbrdh = $baris->ket_pdrd;
                    $data->id_rspd = $baris->id_rspd;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();
                }
            }

            $datahistory = new PermintaanBarangRektoratHistoryModel();
            $datahistory->id_pbr = $id;
            $datahistory->id_rspu = $this->status_ajuan;
            $datahistory->ket_pbrh = $this->keterangan;
            $datahistory->user_id = $user_id;
            $datahistory->role_id = $role_id;
            $datahistory->save();

            $datapermintaan = PermintaanBarangRektoratModel::where('id_pbr', $this->tabel_id)->first();
            $datapermintaan->id_rspu = $this->status_ajuan;
            $datapermintaan->role_id = $disposisi;
            $datapermintaan->posisi_pbr = $posisibrg;
            if($sts_rspu=="1")
            {
                $datapermintaan->status_pbr = 2;
            }
            $datapermintaan->save();                    
            session()->flash('message', 'Data berhasil diajukan');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_proses');
        }
    }

    public function histori($id)
    {        
        $data = PermintaanBarangRektoratModel::where('id_pbr', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pbr;
        $this->status_ajuan = $data->id_rspu;
        $this->nomor=0;
        $this->tampil_histori = PermintaanBarangRektoratHistoryModel::
        select('permintaan_barang_rektorat_history.role_id', 'permintaan_barang_rektorat_history.ket_pbrh', 'permintaan_barang_rektorat_history.created_at', 'unit_rektorat.nm_ur', 'users.name',  'ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'role_pengguna.nama_rp')
        ->join('permintaan_barang_rektorat','permintaan_barang_rektorat_history.id_pbr','=','permintaan_barang_rektorat.id_pbr')
        ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
        ->join('ref_status_proses_untuk','permintaan_barang_rektorat_history.id_rspu','=','ref_status_proses_untuk.id_rspu')
        ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
        ->join('users','permintaan_barang_rektorat_history.user_id','=','users.id')
        ->join('role_pengguna','permintaan_barang_rektorat_history.role_id','=','role_pengguna.id')
        ->where('permintaan_barang_rektorat_history.id_pbr', $id)->get();
    }
}
