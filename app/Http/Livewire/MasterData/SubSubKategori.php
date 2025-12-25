<?php

namespace App\Http\Livewire\MasterData;

use App\Models\KelompokModel;
use App\Models\SubKategoriModel;
use App\Models\SubSubKategoriModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class SubSubKategori extends Component
{
    use WithPagination;
    public $kd_skt, $kd_kt, $id2, $kodeKategori, $namaKategori, $kodeSubKategori, $namaSubKategori, $idKelompok, $kode, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    public $daftar_kelompok = [];
    protected $paginationTheme = 'bootstrap';

    public function mount($kd_skt)
    {
        $this->kd_skt = $kd_skt;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->kd_skt);
        $this->id2 = $des;
        $data = SubKategoriModel::
        join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
        ->where('subkategori.kd_skt', $des)->first();        
        $this->kodeKategori = $data->kd_kt;
        $this->namaKategori = $data->nm_kt;
        $this->kodeSubKategori = $data->kd_skt;
        $this->namaSubKategori = $data->nm_skt;
        $this->kd_kt = Crypt::encryptString($data->kd_kt);

        $this->daftar_kelompok = KelompokModel::orderby('kd_kl')->get();

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.Kategori.SubKategori.SubSubKategori.index', [
            'data' => SubSubKategoriModel::            
            join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->leftjoin('v_lab_nilai2_subsubkategori','subsubkategori.kd_sskt','=','v_lab_nilai2_subsubkategori.v_kd_sskt')
            ->where(function ($sub_query) {
                $sub_query->where('subkategori.kd_skt', 'like', '%' . $this->id2 . '%')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->orwhere('kd_sskt', 'ilike', '%' . $this->searchTerm . '%')->orwhere('nm_sskt', 'ilike', '%' . $this->searchTerm . '%');
                });
            })->orderBy('subsubkategori.kd_skt')->orderBy('kd_sskt')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {   
        $this->idKelompok = '';        
        $this->kode = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_skt);
        $this->id2 = $des;

        $data = SubKategoriModel::
        where('kd_skt', $des)->first();        
        $kodeSubKategori = $data->kd_skt;

        $this->validate([            
            'idKelompok' => 'required',
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = SubSubKategoriModel::where('kd_skt', $this->id2)->where('kd_kl', $this->idKelompok)->where('kd_sskt', $this->kode)->where('nm_sskt', $this->nama)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new SubSubKategoriModel();
            $data->kd_skt = $this->id2;
            $data->kd_sskt = "$kodeSubKategori$this->kode";
            $data->kd_kl = $this->idKelompok;
            $data->no_sskt = $this->kode;
            $data->nm_sskt = $this->nama;            
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
        $data = SubSubKategoriModel::where('id_sskt', $id)->first();
        $this->tabel_id = $id;
        $this->idKelompok = $data->kd_kl;
        $this->kode = "$data->no_sskt";
        $this->nama = $data->nm_sskt;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_skt);
        $this->id2 = $des;

        $this->validate([            
            'idKelompok' => 'required',
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $data = SubKategoriModel::
        where('kd_skt', $des)->first();        
        $kodeSubKategori = $data->kd_skt;

        $cekData = SubSubKategoriModel::where('id_sskt', $this->tabel_id)->first();
        if($cekData->kd_kl == $this->idKelompok and $cekData->kd_sskt == $this->kode and $cekData->nm_sskt == $this->nama)
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
                if($cekData->kd_sskt != $this->kode)
                {
                    $jumlah = SubSubKategoriModel::where('kd_skt', $this->id2)->where('kd_sskt', $this->kode)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'kode tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($cekData->nm_sskt != $this->nama)
                {
                    $jumlah = SubSubKategoriModel::where('kd_skt', $this->id2)->where('nm_sskt', $this->nama)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'nama tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = SubSubKategoriModel::where('id_sskt', $this->tabel_id)->first();                   
                    $data->kd_skt = $this->id2;
                    $data->kd_kl = $this->idKelompok;
                    $data->kd_sskt = "$kodeSubKategori$this->kode";
                    $data->no_sskt = $this->kode;
                    $data->nm_sskt = $this->nama;
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
        $data = SubSubKategoriModel::
        where('id_skt', $id)->first();      
        $this->tabel_id = $id;        
        $this->kode = $data->kd_sskt;
        $this->nama = $data->nm_sskt;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = SubSubKategoriModel::where('id_sskt', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
