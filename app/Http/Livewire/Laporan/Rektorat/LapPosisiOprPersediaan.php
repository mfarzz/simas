<?php

namespace App\Http\Livewire\Laporan\Rektorat;

use App\Models\LokasiModel;
use App\Models\TempBarangMasukModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class LapPosisiOprPersediaan extends Component
{
    use WithPagination;
    public $user_id, $filter, $nama, $carilokasi, $lokasi, $cariproses, $caritglawal, $caritglakhir, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_lokasi = [];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $this->user_id = auth()->user()->id;

        $this->daftar_lokasi = LokasiModel::where('kd_lks','023170800677513009KD')->orderby('kd_lks')->get();

        if($this->caritglakhir == ""){ $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;}

        if($this->caritglakhir == ""){ $tgl_akhir = date("Y-m-d"); $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;$tgl_akhir = $this->caritglakhir;}
        $this->filter = Crypt::encryptString("$tgl_akhir");
        $this->lokasi = Crypt::encryptString("$this->carilokasi");

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;

        return view('Laporan.Rektorat.Unit.PosisiPersediaan.index');
    }

    public function resetInputFields()
    {        
        $this->nama = '';        
    }

    public function proses()
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $this->user_id = auth()->user()->id;

        $this->daftar_lokasi = LokasiModel::orderby('kd_lks')->get();

        if($this->caritglakhir == ""){ $tgl_awal = date("Y-m-d"); $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;$tgl_akhir = $this->caritglakhir;}

        $this->filter = Crypt::encryptString("$tgl_akhir");  
        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->first();
        $datadeletetbm->delete();  
        }
        return redirect()->to('/lap-posisi-persediaan');
    }
}
