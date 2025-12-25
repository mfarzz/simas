@extends('layouts.app')   
@section('konten') 
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
                    <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                            <select class="form-control" id="lokasi" name="lokasi">
                                <option value="">SILAHKAN PILIH</option>
                                @foreach ($daftar_lokasi as $baris_lokasi)
                                    <option value="{{ $baris_lokasi->kd_lks }}">{{ $baris_lokasi->nm_lks }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                            <input type="date" class="form-control" placeholder="Pencarian" id='tgl' name='tgl' required>
                        </div>

                        <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                            <button type="submit" class="waves-effect waves-light btn btn-primary mb-5" id="cetakBtn">
                                <i class="fa fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                    </form>
                    <br>               
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Mendapatkan elemen input tanggal berdasarkan ID
        var tanggalInput = document.getElementById("tgl");

        // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
        var today = new Date().toISOString().slice(0, 10);

        // Mengatur nilai input tanggal ke tanggal hari ini
        tanggalInput.value = today;
    });
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#DataForm').submit(function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('lap-persediaan')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            
            var tgl = data.tgl;
            var lokasi = data.lokasi;
            var link = "/lap-persediaan-print/" + encodeURIComponent(tgl) + "/" + encodeURIComponent(lokasi);
            var newTab = window.open(link, '_blank');

             if (!newTab || newTab.closed || typeof newTab.closed == 'undefined') {
                alert("Pemblokir pop-up mencegah pembukaan tab baru. Harap izinkan pop-up untuk situs ini.");
            }
            
        },
        error: function(data){
            console.log(data);
        }
    });
});
</script>
@endsection