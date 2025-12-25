<div>
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title"></h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Laporan</li>
                            <li class="breadcrumb-item active" aria-current="page">Persediaan</li>
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
                        <div class="ribbon ribbon-success">Laporan Persediaan</div>
                    </div> <!-- end box-body-->                    
                    <div class="box-body">
                        <div class="row">
                            <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                                <select class="form-control" wire:model="carilokasi">
                                    <option value="">SILAHKAN PILIH</option>
                                    @foreach ($daftar_lokasi as $baris_lokasi)
                                        <option value="{{ $baris_lokasi->kd_lks }}">{{ $baris_lokasi->nm_lks }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                                <input type="date" class="form-control" id="specificSizeInputName" placeholder="Pencarian" wire:model="caritglakhir">
                            </div>

                            <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                                <a href="/lap-persediaan-print/{{ $filter }}/{{ $lokasi }}"  target="_blank" data-bs-placement="left" title="Cetak Data"><button type="button" class="waves-effect waves-light btn btn-primary mb-5"><i class="fa fa-print"></i> Cetak </button></a>
                            </div>
                        </div>
                        <br>               
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
</div>