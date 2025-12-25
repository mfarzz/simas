<?php

namespace App\Http\Livewire\PengadaanBarang;

use App\Models\PengadaanBarangDetailHistoryModel;
use App\Models\PengadaanBarangDetailModel;
use App\Models\PengadaanBarangHistoryModel;
use App\Models\PengadaanBarangModel;
use App\Models\RefPosisiKegiatanModel;
use App\Models\RefStatusProsesModel;
use App\Models\RefStatusProsesUntukModel;
use Livewire\Component;
use Livewire\WithPagination;

class PengadaanBarangAktif extends Component
{
    use WithPagination;
    public $nama, $tgl, $role_id, $tabel_id, $no, $status_ajuan, $posisi, $kondisi, $keterangan, $nomor;
    public $searchTerm, $halaman;
    public $currentPage = 1;  
    public $daftar_status = [];
    public $tampil_histori = [];  
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        $this->role_id = auth()->user()->role_id;
        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        $dataposisi = RefPosisiKegiatanModel::where('role_id', $this->role_id)->where('id_rk',1)->first();
        $this->posisi = $dataposisi->posisi_rpk;
        $this->kondisi = $dataposisi->kondisi_rpk;

        $this->daftar_status = RefStatusProsesModel::
        join('ref_status_proses_untuk','ref_status_proses.id_rsp','=','ref_status_proses_untuk.id_rsp')
        ->join('role_pengguna','ref_status_proses_untuk.role_id_pilihan','=','role_pengguna.id')
        ->where('ref_status_proses_untuk.posisi_pb_pilihan', '=', $this->posisi)        
        ->where('ref_status_proses_untuk.id_rk', '=',1)
        ->orderBy('nm_rsp')->orderBy('ref_status_proses.id_rsp')->get();

        
        /*return view('PengadaanBarang.Aktif.index', [
            'data' => PengadaanBarangModel::select('pengadaan_barang.id_pb', 'pengadaan_barang.nm_pb', 'pengadaan_barang.tgl_pb','ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'ref_status_proses_untuk.role_id', 'role_pengguna.nama_rp', 'v_pengadaan_barang_detail_belum_diproses.jumlah_belum_diproses')
            ->join('ref_status_proses_untuk','pengadaan_barang.id_rspu','=','ref_status_proses_untuk.id_rspu')
            ->join('role_pengguna','ref_status_proses_untuk.role_id','=','role_pengguna.id')
            ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
            ->leftjoin('v_pengadaan_barang_detail_belum_diproses','pengadaan_barang.id_pb','=','v_pengadaan_barang_detail_belum_diproses.id_pb')
            ->where(function ($sub_query) {
                $sub_query->where('nm_pb', 'ilike', '%' . $this->searchTerm . '%')
                ->where('status_pb', '=', 1)->where('role_id', '=', $this->role_id);
            })->orderBy('nm_pb')->paginate($page)
        ]);*/
        return view('PengadaanBarang.Aktif.index', [
            'data' => PengadaanBarangModel::select('pengadaan_barang.id_pb', 'pengadaan_barang.nm_pb', 'pengadaan_barang.tgl_pb','ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'ref_status_proses_untuk.role_id_proses', 'role_pengguna.nama_rp', 'v_pengadaan_barang_detail_belum_diproses.jumlah_belum_diproses')
            ->join('ref_status_proses_untuk','pengadaan_barang.id_rspu','=','ref_status_proses_untuk.id_rspu')
            ->join('role_pengguna','ref_status_proses_untuk.role_id_proses','=','role_pengguna.id')
            ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
            ->leftjoin('v_pengadaan_barang_detail_belum_diproses','pengadaan_barang.id_pb','=','v_pengadaan_barang_detail_belum_diproses.id_pb')
            ->where(function ($sub_query) {
                $sub_query->where('nm_pb', 'ilike', '%' . $this->searchTerm . '%')
                ->where('pengadaan_barang.status_pb', '=', 1)->where('pengadaan_barang.role_id', '=', $this->role_id);
            })->orderBy('nm_pb')->paginate($page)
        ]);
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

        $datastatus = RefStatusProsesUntukModel::where('id_rsp', 0)->where('id_rk', 1)->first();

        $data = new PengadaanBarangModel();
        $data->nm_pb = $this->nama;
        $data->tgl_pb = $this->tgl;
        $data->id_rspu = $datastatus->id_rspu;
        $data->role_id = $role_id;
        $data->status_pb = 1;
        $data->posisi_pb = 1;
        $data->user_id = $user_id;
        $data->save();
        $id_pb = $data->id_pb;

        $datahistory = new PengadaanBarangHistoryModel();
        $datahistory->id_pb = $id_pb;
        $datahistory->id_rspu = $datastatus->id_rspu;
        $datahistory->ket_pbh = '';
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
        $data = PengadaanBarangModel::where('id_pb', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pb;
        $this->tgl = $data->tgl_pb;
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
        $data = PengadaanBarangModel::where('id_pb', $this->tabel_id)->first();
        $data->nm_pb = $this->nama;
        $data->tgl_pb = $this->tgl;               
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
        $data = PengadaanBarangModel::where('id_pb', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pb;
        $this->tgl = $data->tgl_pb;
    }

    public function delete()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id = $this->tabel_id;
        if ($id) {            
            PengadaanBarangModel::where('id_pb', $id)->delete();  
            PengadaanBarangDetailModel::where('id_pb', $id)->delete();          
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }
    }

    public function ajukan($id)
    {        
        $data = PengadaanBarangModel::where('id_pb', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pb;
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

        $dataposisi = RefPosisiKegiatanModel::where('role_id', $this->role_id)->where('id_rk',1)->first();
        $posisi = $dataposisi->posisi_rpk;

        $datadisposisi = RefStatusProsesUntukModel::where('id_rspu', $this->status_ajuan)->first();
        $disposisi = $datadisposisi->role_id_proses;
        $posisibrg = $datadisposisi->posisi_pb_proses;
        $sts_rspu = $datadisposisi->sts_rspu;

        $id = $this->tabel_id;
        if ($id) {            
            if($posisi==1)
            {
                $data = PengadaanBarangDetailModel::where('id_pb', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PengadaanBarangDetailHistoryModel();
                    $data->id_pbd = $baris->id_pbd;
                    $data->jmlh_pbdh_awal = $baris->jmlh_pbd_awal;
                    $data->jmlh_pbdh = $baris->jmlh_pbd_awal;
                    $data->id_rspd = 2;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();

                    $datadetail = PengadaanBarangDetailModel::where('id_pbd', $baris->id_pbd)->first();
                    $datadetail->id_rspd = 2;
                    $datadetail->save();
                }
            }
            else
            {
                $data = PengadaanBarangDetailModel::where('id_pb', $id)->get();
                foreach($data as $baris)
                {
                    $data = new PengadaanBarangDetailHistoryModel();
                    $data->id_pbd = $baris->id_pbd;
                    $data->jmlh_pbdh = $baris->jmlh_pbd;
                    $data->jmlh_pbdh_awal = $baris->jmlh_pbd_awal;
                    $data->ket_pdbh = $baris->ket_pdb;
                    $data->id_rspd = $baris->id_rspd;
                    $data->role_id = $role_id;
                    $data->user_id = $user_id;
                    $data->save();
                }
            }

            $datahistory = new PengadaanBarangHistoryModel();
            $datahistory->id_pb = $id;
            $datahistory->id_rspu = $this->status_ajuan;
            $datahistory->ket_pbh = $this->keterangan;
            $datahistory->user_id = $user_id;
            $datahistory->role_id = $role_id;
            $datahistory->save();

            $datapermintaan = PengadaanBarangModel::where('id_pb', $this->tabel_id)->first();
            $datapermintaan->id_rspu = $this->status_ajuan;
            $datapermintaan->role_id = $disposisi;
            $datapermintaan->posisi_pb = $posisibrg;
            if($sts_rspu=="1")
            {
                $datapermintaan->status_pb = 2;
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
        $data = PengadaanBarangModel::where('id_pb', $id)->first();
        $this->tabel_id = $id;
        $this->nama = $data->nm_pb;
        $this->status_ajuan = $data->id_rspu;
        $this->nomor=0;
        $this->tampil_histori = PengadaanBarangHistoryModel::
        select('pengadaan_barang_history.role_id', 'pengadaan_barang_history.ket_pbh', 'pengadaan_barang_history.created_at', 'users.name',  'ref_status_proses.nm_rsp', 'ref_status_proses_untuk.kondisi_rspu', 'ref_status_proses_untuk.nm_rspu', 'role_pengguna.nama_rp')
        ->join('ref_status_proses_untuk','pengadaan_barang_history.id_rspu','=','ref_status_proses_untuk.id_rspu')
        ->join('ref_status_proses','ref_status_proses_untuk.id_rsp','=','ref_status_proses.id_rsp')
        ->join('users','pengadaan_barang_history.user_id','=','users.id')
        ->join('role_pengguna','pengadaan_barang_history.role_id','=','role_pengguna.id')
        ->where('id_pb', $id)->get();
    }
}
