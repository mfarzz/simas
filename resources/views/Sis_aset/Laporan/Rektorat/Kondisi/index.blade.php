@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Laporan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Laporan</a></li>
                            <li class="breadcrumb-item active">Kondisi</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Daftar BMN Menurut Kondisi</h4>
                            </div>
                            <div class="col-md-6 text-end">

                                <div class="mt-3"></div>
                            </div>
                        </div>
                        <div class="mb-0" data-pattern="priority-columns">
                            <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                
                                <div class="row">
                                    <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Kondisi Barang</label>
                                    <div class="col-sm-4">
                                        <select id="kondisi" name="kondisi" class="form-select form-control" required>
                                            <option value=''>---Silahkan Pilih---</option>
                                            <option value='0'> Semua Kondisi </option>
                                            <option value='1'> Baik </option>
                                            <option value='2'> Rusak </option>
                                            <option value='3'> Rusak Berat </option>
                                        </select>
                                    </div>

                                    <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">SD Tanggal</label>
                                    <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                                        <input type="date" class="form-control" placeholder="Pencarian" id='tgl_akhir' name='tgl_akhir' required>
                                    </div>
            
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                                        <button type="submit" class="waves-effect waves-light btn btn-primary mb-5" id="cetakBtn">
                                            <i class="fa fa-print"></i> Cetak
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
        var today = new Date().toISOString().slice(0, 10);
        var tanggalInputAkhir = document.getElementById("tgl_akhir");
        tanggalInputAkhir.value = today;
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
        url: "{{ url('lap-kondisibrg-opr-aset')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            var kondisi = data.kondisi;
            var tgl_akhir = data.tgl_akhir;            
            var link = "/lap-kondisibrg-opr-aset-print/"+ encodeURIComponent(tgl_akhir) + "/" + encodeURIComponent(kondisi);
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