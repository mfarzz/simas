<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Barang Masuk</li>
                            <li class="breadcrumb-item" aria-current="page">Aset Lancar</li>
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
                        <div class="ribbon ribbon-success">Daftar Barang yang Diterima</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="box bt-3 border-info">
                              <div class="box-header">
                                <h4 class="box-title">Kebutuhan Untuk <strong>{{ $namaKebutuhan }}</strong></h4>
                              </div>
                              <div class="box-body">
                                <p>Tanggal Disetujui : {{ \Carbon\Carbon::parse($tglDisetujui)->format('d M Y')}}</p>                                
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
                        @include('BarangMasuk.Lancar.Detail.create')

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
                                        <x-isi-judul-tabel namakolom="Jumlah" />
                                        <x-isi-judul-tabel namakolom="Status" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $value)
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_masaselan }}</td> 
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_js }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->jmlh_bml }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black' align='right'>
                                                @if($value->status_bml==0)
                                                Draft
                                                @elseif($value->status_bml==1)
                                                Valid
                                                @endif
                                            </td> 
                                            <td style='border:1px solid #90AFC5; color:black'>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_bml }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_bml }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>
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
                    <a href="/barang-masuk-lancar"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar barang masuk"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Barang Masuk </button></a>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>