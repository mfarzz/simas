<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Master Data</li>
                            <li class="breadcrumb-item" aria-current="page">Kategori</li>
                            <li class="breadcrumb-item" aria-current="page">Sub Kategori</li>
                            <li class="breadcrumb-item active" aria-current="page">Barang</li>
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
                        <div class="ribbon ribbon-success">Daftar Barang</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="box bt-3 border-info">
                              <div class="box-header">
                                <h4 class="box-title">Rincian</strong></h4>
                              </div>
                              <div class="box-body">
                                <p>Kategori : {{ $kodeKategori }} - {{ $namaKategori }}</p>
                                <p>Sub Kategori : {{ $kodeSubKategori }} - {{ $namaSubKategori }}</p>
                                <p>Sub Sub Kategori : {{ $kodeSubSubKategori }} - {{ $namaSubSubKategori }}</p>
                                <p>Kelompok : {{ $kodeKelompok }} - {{ $namaKelompok }}</p>
                              </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2 col-sm-2 col-md-2 col-lg-1">
                                <input type="hidden" class="form-control" wire:model="page">
                                <select class="form-control form-select" data-placeholder="Choose one" wire:model="halaman">
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-6 col-md-6 col-lg-9">
                                <input type="text" class="form-control" id="specificSizeInputName" placeholder="Pencarian" wire:model="searchTerm">
                            </div>
                            <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"><button type="button" class="waves-effect waves-light btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target=".formInputModal"><i class="fa fa-plus-square"></i> Tambah Data </button></a>
                            </div>
                        </div>
                        <br>
                        @include('MasterData.Kategori.SubKategori.Barang.create')
                        @include('MasterData.Kategori.SubKategori.Barang.update')
                        @include('MasterData.Kategori.SubKategori.Barang.hapus')

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Kode Lengkap" />
                                        <x-isi-judul-tabel namakolom="Kode" />
                                        <x-isi-judul-tabel namakolom="Nama" />
                                        <x-isi-judul-tabel namakolom="Satuan" />
                                        <x-isi-judul-tabel namakolom="Stok" />
                                        <x-isi-judul-tabel namakolom="Nilai" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $value)
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->kd_brg }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->no_brg }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_brg }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_js }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>{{ $value->stok_brg }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>@currency($value->total_nilai)</td>
                                            <td style='border:1px solid #90AFC5; color:black'>                                            
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_brg }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_brg }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>          
                                            </td>
                                        </tr>                                    
                                    @endforeach
                                    @if($no=="0")                                    
                                    <tr>
                                        <td colspan="6" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
                                    </tr>                                    
                                    @endif
                                    </tbody>
                                </table>
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                    <a href="/master-subsubkategori/{{ $kd_skt }}"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar sub sub kategori"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Sub Sub Kategori </button></a>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>