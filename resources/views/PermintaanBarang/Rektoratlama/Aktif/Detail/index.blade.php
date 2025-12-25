<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Permintaan Barang</li>
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
                                <p>Tanggal Permintaan : {{ \Carbon\Carbon::parse($tglPermintaan)->format('d M Y')}}</p>                                
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
                        @include('PermintaanBarang.Rektorat.Aktif.Detail.create')
                        @include('PermintaanBarang.Rektorat.Aktif.Detail.update')
                        @include('PermintaanBarang.Rektorat.Aktif.Detail.hapus')
                        @include('PermintaanBarang.Rektorat.Aktif.Detail.histori')                        

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Nama Barang" />
                                        <x-isi-judul-tabel namakolom="Satuan" />
                                        <x-isi-judul-tabel namakolom="Stok Sekarang" />
                                        <x-isi-judul-tabel namakolom="Jumlah Diminta" />
                                        <x-isi-judul-tabel namakolom="Jumlah Disetujui" />
                                        <x-isi-judul-tabel namakolom="Status" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @php ($total =0)
                                    @foreach ($data as $value)
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_brg }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_js }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->stok_lki }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->jmlh_pbrd_awal }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->jmlh_pbrd }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rspd }}</td>  
                                            <td style='border:1px solid #90AFC5; color:black'>
                                                @if($value->id_rspd == 0 or $value->id_rspd == 3)
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_pbrd }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_pbrd }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>
                                                @elseif($value->id_rspd == 2)
                                                    @if(auth()->user()->role_id==6)
                                                    <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_pbrd }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                    @endif
                                                @endif
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="History Data"><button type="button" data-bs-toggle="modal" data-bs-target=".historiModal" wire:click="histori({{ $value->id_pbrd }})" class="btn btn-light btn-rounded btn-sm waves-effect"><i class="fa fa-history fa-sm"></i></button></a>
                                            </td>
                                        </tr>   
                                    @endforeach
                                    
                                    @if($no=="0")                                    
                                    <tr>
                                        <td colspan="10" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
                                    </tr>     
                                    @else                              
                                    @endif
                                    </tbody>
                                </table>
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                    <a href="/permintaan-barang-rektorat-aktif"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar permintaan barang aktif"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Permintaan Barang Aktif </button></a>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>