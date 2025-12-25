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
                            <li class="breadcrumb-item active" aria-current="page">Status Proses Untuk</li>
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
                        <div class="ribbon ribbon-success">Daftar Status Proses Untuk</div>
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
                        <br>
                        @include('MasterData.StatusProsesUntuk.create')
                        @include('MasterData.StatusProsesUntuk.update')
                        @include('MasterData.StatusProsesUntuk.hapus')                        

                        @if(session()->has('message'))
                        <x-alert/>
                        @endif

                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                    <x-judul-tabel>
                                        <x-isi-judul-tabel namakolom="No" />
                                        <x-isi-judul-tabel namakolom="Status" />
                                        <x-isi-judul-tabel namakolom="Nama" />
                                        <x-isi-judul-tabel namakolom="Level Akses Proses" />
                                        <x-isi-judul-tabel namakolom="Level Akses Pilihan" />
                                        <x-isi-judul-tabel namakolom="Kondisi Data" />
                                        <x-isi-judul-tabel namakolom="Posisi Proses" />
                                        <x-isi-judul-tabel namakolom="Posisi Pilihan" />
                                        <x-isi-judul-tabel namakolom="Kegiatan" />
                                        <x-isi-judul-tabel namakolom="Status Data" />
                                        <x-isi-judul-tabel namakolom="" />
                                    </x-judul-tabel>
                                    </thead>
                                    <tbody>
                                    @foreach ($data as $value)                                    
                                        <tr>
                                            <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rsp }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rspu }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->role_id_proses }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->role_id_pilihan }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>
                                                @if($value->kondisi_rspu == "1")
                                                Boleh Edit
                                                @elseif($value->kondisi_rspu == "0")
                                                Tidak Boleh Edit
                                                @endif
                                            </td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->posisi_pb_proses }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->posisi_pb_pilihan }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rk }}</td>
                                            <td style=' border:1px solid #90AFC5 ; color:black'>
                                                @if($value->sts_rspu == "0")
                                                Belum Selesai
                                                @elseif($value->sts_rspu == "1")
                                                Selesai
                                                @endif
                                            </td>
                                            <td style='border:1px solid #90AFC5; color:black'>                                            
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data"><button type="button" data-bs-toggle="modal" data-bs-target=".ubahModal" wire:click="edit({{ $value->id_rspu }})" class="btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></button></a>
                                                <a href="#"  data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data"><button type="button" data-bs-toggle="modal" data-bs-target=".hapusModal" wire:click="hapus({{ $value->id_rspu }})" class="btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></button></a>
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