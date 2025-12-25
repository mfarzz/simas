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
                        <li class="breadcrumb-item" aria-current="page">Permintaan Barang</li>
                        <li class="breadcrumb-item active" aria-current="page">Proses Permintaan Barang</li>
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
                    <div class="ribbon ribbon-success">Daftar Proses Permintaan Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                        </ul>
                </div>

                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                      <div class="box-header">
                        <h4 class="box-title">Rincian</strong></h4>
                      </div>
                      <div class="box-body">
                        <p>Fakultas : {{ $cek_pbf->nm_fk }}</p>
                        <p>Kebutuhan Untuk : {{ $cek_pbf->butuh_pbf }}</p>
                        <p>Tanggal Permintaan : {{ $tgl = \Carbon\Carbon::parse($cek_pbf->tgl_pbf)->locale('id')->isoFormat('D MMMM Y'); }}</p>
                        <p>Status : {{ $cek_pbf->nm_pbs }}</p>
                      </div>
                    </div>
                </div>
                
                <div class="box-body">                  
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Status" />
                            <x-isi-judul-tabel namakolom="Tanggal Proses" />
                            <x-isi-judul-tabel namakolom="Keterangan" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/permintaan-barang-kg-pimf"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar permintaan barang"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Permintaan Barang </button></a>
            </div>
        </div>
    </div>
</section>


<script type="text/javascript">
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#data-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        
        ajax: "{{ url('permintaan-barang-kg-pimf-log', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_pbs', name: 'nm_pbs' },
            {
                data: 'tgl_pbfl',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            { data: 'ket_pbf', name: 'ket_pbf' },
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection