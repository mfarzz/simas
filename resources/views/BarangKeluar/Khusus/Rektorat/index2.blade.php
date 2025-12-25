<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Barang Keluar</li>
                            <li class="breadcrumb-item active" aria-current="page">Khusus</li>
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
                        <div class="ribbon ribbon-success">Daftar Barang Keluar Khusus</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">    
                        
                        <div class="row">
                            <div class="col-7 col-sm-7 col-md-8 col-lg-10">
                            <input type="text" class="form-control" id="specificSizeInputName" placeholder="Pencarian" wire:model="searchTerm">
                            </div>
                            <div class="col-5 col-sm-5 col-md-4 col-lg-2">
                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"><button type="button" class="waves-effect waves-light btn btn-primary mb-5" data-bs-toggle="modal" data-bs-target=".formInputModal"><i class="fa fa-plus-square"></i> Tambah Data </button></a>
                            </div>
                        </div>

                        @include('BarangKeluar.Khusus.Rektorat.create')
                        @include('BarangKeluar.Khusus.Rektorat.hapus')
                        @include('BarangKeluar.Khusus.Rektorat.detail')                        

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Nama Penerima" />
                                        <x-isi-judul-tabel namakolom="Kategori" />
                                        <x-isi-judul-tabel namakolom="Sub Kategori" />
                                        <x-isi-judul-tabel namakolom="Nama Barang" />
                                        <x-isi-judul-tabel namakolom="Jumlah" />
                                        <x-isi-judul-tabel namakolom="Tanggal" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $value)
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_penerima }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->kd_kl }} - {{ $value->nm_kl }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->kd_sskt }} - {{ $value->nm_sskt }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->kd_brg }} - {{ $value->nm_brg }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>{{ $value->jmlh_bk }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ \Carbon\Carbon::parse($value->tglambil_bk)->format('d M Y')}}</td>      
                                            <td style='border:1px solid #90AFC5; color:black'>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Detail Data"><button type="button" data-bs-toggle="modal" data-bs-target=".formDetailModal" wire:click="detail({{ $value->id_bk }})" class="btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-book fa-sm"></i></button></a>  
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_bk }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>           
                                            </td>
                                        </tr>                                    
                                    @endforeach
                                    @if($no=="0")                                    
                                    <tr>
                                        <td colspan="8" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
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