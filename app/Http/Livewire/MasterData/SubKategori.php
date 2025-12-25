<?php

namespace App\Http\Livewire\MasterData;

use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class SubKategori extends Component
{
    use WithPagination;
    public $kd_kt, $id2, $kodeKategori, $namaKategori, $kode, $nama, $tabel_id, $no;
    public $searchTerm, $halaman;
    public $currentPage = 1;    
    protected $paginationTheme = 'bootstrap';

    public function mount($kd_kt)
    {
        $this->kd_kt = $kd_kt;
    }

    public function render()
    {   
        $des = Crypt::decryptString($this->kd_kt);
        $this->id2 = $des;
        $data = KategoriModel::
        where('kd_kt', $des)->first();        
        $this->kodeKategori = $data->kd_kt;
        $this->namaKategori = $data->nm_kt;

        if($this->halaman == ""){$page = 10;}
        else{$page = $this->halaman;}
        $a = $this->page - 1;
        $b = $a * $page;        
        $this->no = $b + 0;        
        return view('MasterData.Kategori.SubKategori.index', [
            'data' => SubKategoriModel::            
            join('kategori','subkategori.kd_kt','=','kategori.kd_kt')
            ->leftjoin('v_lab_nilai3_subkategori','subkategori.kd_skt','=','v_lab_nilai3_subkategori.v_kd_skt')
            ->where(function ($sub_query) {
                $sub_query->where('subkategori.kd_kt', 'like', '%' . $this->id2 . '%')
                ->Where(function ($sub_sub_query) {
                    $sub_sub_query->orwhere('kd_skt', 'ilike', '%' . $this->searchTerm . '%')->orwhere('nm_skt', 'ilike', '%' . $this->searchTerm . '%');
                });
            })->orderBy('subkategori.kd_kt')->orderBy('kd_skt')->paginate($page)
        ]);
    }

    public function resetInputFields()
    {           
        $this->kode = '';
        $this->nama = '';        
    }

    public function store()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_kt);
        $this->id2 = $des;

        $data = KategoriModel::
        where('kd_kt', $des)->first();        
        $kodeKategori = $data->kd_kt;

        $this->validate([            
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $jumlah = SubKategoriModel::where('kd_kt', $this->id2)->where('kd_skt', $this->kode)->where('nm_skt', $this->nama)->count();
        if($jumlah>0)
        {            
            session()->flash('message-gagal-insert', 'Kode atau nama tidak boleh sama');
            session()->flash('class', 'danger');                
            $this->emit('gagal'); 
        }
        else
        {            
            $data = new SubKategoriModel();
            $data->kd_kt = $this->id2;
            $data->kd_skt = "$kodeKategori$this->kode";
            $data->no_skt = $this->kode;
            $data->nm_skt = $this->nama;            
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
        $data = SubKategoriModel::where('id_skt', $id)->first();
        $this->tabel_id = $id;
        $this->kode = $data->no_skt;
        $this->nama = $data->nm_skt;
    }

    public function cancel()
    {        
        $this->resetInputFields();
    }

    public function update()
    {        
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        $des = Crypt::decryptString($this->kd_kt);
        $this->id2 = $des;

        $this->validate([            
            'kode' => 'required',
            'nama' => 'required'
        ]);
        $data = KategoriModel::
        where('kd_kt', $des)->first();        
        $kodeKategori = $data->kd_kt;

        $cekData = SubKategoriModel::where('id_skt', $this->tabel_id)->first();
        if($cekData->kd_skt == $this->kode and $cekData->nm_skt == $this->nama)
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
                if($cekData->kd_skt != $this->kode)
                {
                    $jumlah = SubKategoriModel::where('kd_kt', $this->id2)->where('kd_skt', $this->kode)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'kode tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($cekData->nm_skt != $this->nama)
                {
                    $jumlah = SubKategoriModel::where('kd_kt', $this->id2)->where('nm_skt', $this->nama)->count();
                    if($jumlah>0)
                    {
                        session()->flash('message-gagal-update', 'nama tidak boleh sama');
                        session()->flash('class', 'danger');                
                        $this->emit('gagal'); 
                    }
                }
                if($jumlah==0)
                {                    
                    $data = SubKategoriModel::where('id_skt', $this->tabel_id)->first();                   
                    $data->kd_kt = $this->id2;
                    $data->kd_skt = "$kodeKategori$this->kode";
                    $data->no_skt = $this->kode;
                    $data->nm_skt = $this->nama;
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
        $data = SubKategoriModel::
        where('id_skt', $id)->first();      
        $this->tabel_id = $id;        
        $this->kode = $data->kd_skt;
        $this->nama = $data->nm_skt;
    }

    public function delete()
    {        
        if ($this->tabel_id) {   
            $data = SubKategoriModel::where('id_skt', $this->tabel_id)->first();      
            $data->delete();                   
            session()->flash('message', 'Data berhasil dihapus');
            session()->flash('class', 'success');
            $this->resetInputFields();
            $this->emit('tutup_hapus');
        }        
    }
}
