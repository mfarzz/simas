<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Pengadaan Barang</li>
                            <li class="breadcrumb-item" aria-current="page">Aktif</li>
                            <li class="breadcrumb-item" aria-current="page">Detail Barang</li>
                        </ol>
                    </nav>
                </div>
            </div>        
        </div>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-12">                
                <div class="box">
                    <div class="box-body ribbon-box">
                        <div class="ribbon ribbon-success">Daftar Barang yang Diminta</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="box bt-3 border-info">
                              <div class="box-header">
                                <h4 class="box-title">Kebutuhan Untuk <strong>{{ $namaKebutuhan }}</strong></h4>
                              </div>
                              <div class="box-body">
                                <p>Tanggal Pengadaan : {{ \Carbon\Carbon::parse($tglPengadaan)->format('d M Y')}}</p>                                
                              </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 col-sm-6 col-md-7 col-lg-9">
                            <input type="text" class="form-control" id="specificSizeInputName" placeholder="Pencarian Berdasarkan Nama Barang" wire:model="searchTerm">
                            </div>
                            <div class="col-6 col-sm-6 col-md-5 col-lg-3">
                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"><button type="button" class="waves-effect waves-light btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target=".formInputModal"><i class="fa fa-plus-square"></i> Tambah Data </button></a>
                            </div>
                        </div>
                        <br>
                        @include('PengadaanBarang.Aktif.Detail.create')
                        @include('PengadaanBarang.Aktif.Detail.update')
                        @include('PengadaanBarang.Aktif.Detail.hapus')
                        @include('PengadaanBarang.Aktif.Detail.histori')                        

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Kategori" />
                                        <x-isi-judul-tabel namakolom="Sub Kategori" />
                                        <x-isi-judul-tabel namakolom="Nama Barang" />
                                        <x-isi-judul-tabel namakolom="Satuan" />
                                        <x-isi-judul-tabel namakolom="Stok Sekarang" />
                                        <x-isi-judul-tabel namakolom="Jumlah Diminta" />
                                        <x-isi-judul-tabel namakolom="Jumlah Disetujui" />
                                        <x-isi-judul-tabel namakolom="Perkiraan Harga" />
                                        <x-isi-judul-tabel namakolom="Total Harga" />
                                        <x-isi-judul-tabel namakolom="Status" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @php ($total =0)
                                    @foreach ($data as $value)
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_kl }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_sskt }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_brg }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_js }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->stok_lki }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->jmlh_pbd_awal }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->jmlh_pbd }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>@currency($value->hrg_estimasi_pbd)</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>
                                                @if(auth()->user()->role_id==2)
                                                @currency($value->hrg_estimasi_pbd * $value->jmlh_pbd_awal)
                                                @elseif(auth()->user()->role_id==4)
                                                @currency($value->hrg_estimasi_pbd * $value->jmlh_pbd)
                                                @endif
                                            </td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rspd }}</td>  
                                            <td style='border:1px solid #90AFC5; color:black'>
                                                @if($value->id_rspd == 0 or $value->id_rspd == 3)
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_pbd }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_pbd }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>
                                                @elseif($value->id_rspd == 2)
                                                    @if(auth()->user()->role_id==4)
                                                    <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_pbd }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                    @endif
                                                @endif
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="History Data"><button type="button" data-bs-toggle="modal" data-bs-target=".historiModal" wire:click="histori({{ $value->id_pbd }})" class="btn btn-light btn-rounded btn-sm waves-effect"><i class="fa fa-history fa-sm"></i></button></a>
                                            </td>
                                        </tr>   
                                        @if(auth()->user()->role_id==2)  
                                            @php($jumlah = $value->hrg_estimasi_pbd * $value->jmlh_pbd_awal)
                                            @php($total = $total + $jumlah)
                                        @elseif(auth()->user()->role_id==4)
                                            @php($jumlah = $value->hrg_estimasi_pbd * $value->jmlh_pbd)
                                            @php($total = $total + $jumlah)
                                        @endif
                                    @endforeach
                                    
                                    @if($no=="0")                                    
                                    <tr>
                                        <td colspan="12" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
                                    </tr>     
                                    @else
                                    <tr>
                                        <td colspan="9" align='center' style='border:1px solid #90AFC5; color:black'>Total Harga</td>
                                        <td align='right' style='border:1px solid #90AFC5; color:black'>@currency($total)</td>
                                        <td colspan="2" align='center' style='border:1px solid #90AFC5; color:black'></td>
                                    </tr>                               
                                    @endif
                                    </tbody>
                                </table>
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                    <a href="/pengadaan-barang-aktif"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar pengadaan barang aktif"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Pengadaan Barang Aktif </button></a>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>