<?php

namespace App\Http\Livewire\Laporan\Fakultas;

use App\Models\LokasiModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class LapOpfPersediaan extends Component
{
    use WithPagination;
    public $user_id, $filter, $nama, $carilokasi, $lokasi, $cariproses, $caritglawal, $caritglakhir, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    protected $paginationTheme = 'bootstrap';

    public function render()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $this->user_id = auth()->user()->id;

        if($this->caritglakhir == ""){ $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;}

        if($this->caritglakhir == ""){ $tgl_akhir = date("Y-m-d"); $this->caritglakhir = date("Y-m-d");}
        else{$this->caritglakhir = $this->caritglakhir;$tgl_akhir = $this->caritglakhir;}
        $this->filter = Crypt::encryptString("$tgl_akhir");

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;
        return view('Laporan.Fakultas.Persediaan.index');
    }

    public function resetInputFields()
    {        
        $this->nama = '';        
    }
}
