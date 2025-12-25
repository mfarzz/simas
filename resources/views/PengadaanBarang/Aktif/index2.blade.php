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
                            <li class="breadcrumb-item active" aria-current="page">Aktif</li>
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
                        <div class="ribbon ribbon-success">Daftar Pengadaan Barang</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">
                        <div class="row">
                            <div class="col-2 col-sm-3 col-md-2 col-lg-1">
                                <input type="hidden" class="form-control" wire:model="page">
                                <select class="form-control form-select" data-placeholder="Choose one" wire:model="halaman">
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <div class="col-6 col-sm-9 col-md-6 col-lg-9">
                            <input type="text" class="form-control" id="specificSizeInputName" placeholder="Pencarian" wire:model="searchTerm">
                            </div>
                            <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                                @if($kondisi==1)
                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"><button type="button" class="waves-effect waves-light btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target=".formInputModal"><i class="fa fa-plus-square"></i> Tambah Data </button></a>
                                @endif
                            </div>
                        </div>
                        <br>
                        @include('PengadaanBarang.Aktif.create')
                        @include('PengadaanBarang.Aktif.update')
                        @include('PengadaanBarang.Aktif.hapus')   
                        @include('PengadaanBarang.Aktif.ajukan')
                        @include('PengadaanBarang.Aktif.histori')                      

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Nama" />
                                        <x-isi-judul-tabel namakolom="Tanggal" />
                                        <x-isi-judul-tabel namakolom="Status" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>

                                    @foreach ($data as $value)  
                                    @php $encript = Crypt::encryptString($value->id_pb); @endphp                                  
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_pb }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ \Carbon\Carbon::parse($value->tgl_pb)->format('d M Y')}}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rsp }} {{ $value->nm_rspu }} {{ $value->nama_rp }}</td>      
                                            <td style='border:1px solid #90AFC5; color:black'> 
                                                @if(auth()->user()->role_id==2)
                                                    @if($value->kondisi_rspu==1)
                                                    <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_pb }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                    <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_pb }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>
                                                    @endif
                                                @endif
                                                <a href="/pengadaan-barang-aktif-detail/{{ $encript }}"  data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang Diminta"><button type="button" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book"></i></button></a>
                                                @if($value->role_id_proses == auth()->user()->role_id)
                                                    @if(auth()->user()->role_id==2)
                                                        <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ajukan Permintaan"><button type="button" data-bs-toggle="modal" data-bs-target=".prosesdataModal" wire:click="ajukan({{ $value->id_pb }})" class="btn btn-dark btn-rounded btn-sm waves-effect"><i class="fa fa-handshake-o fa-sm"></i></button></a>       
                                                    @elseif(auth()->user()->role_id==3 or auth()->user()->role_id==4)
                                                        @if($value->jumlah_belum_diproses == 0 or $value->jumlah_belum_diproses == "")
                                                            <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ajukan Permintaan"><button type="button" data-bs-toggle="modal" data-bs-target=".prosesdataModal" wire:click="ajukan({{ $value->id_pb }})" class="btn btn-dark btn-rounded btn-sm waves-effect"><i class="fa fa-handshake-o fa-sm"></i></button></a>
                                                        @else
                                                        (Belum diproses semuanya)
                                                        @endif
                                                    @endif
                                                @endif
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="History Data"><button type="button" data-bs-toggle="modal" data-bs-target=".historiModal" wire:click="histori({{ $value->id_pb }})" class="btn btn-light btn-rounded btn-sm waves-effect"><i class="fa fa-history fa-sm"></i></button></a> 
                                            </td>
                                        </tr>                                    
                                    @endforeach
                                    @if($no=="0")                                    
                                    <tr>
                                        <td colspan="5" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
                                    </tr>                                    
                                    @endif
                                    </tbody>
                                </table>
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>