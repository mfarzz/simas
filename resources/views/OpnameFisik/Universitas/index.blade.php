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
                        <li class="breadcrumb-item" aria-current="page">Opname Fisik</li>
                        <li class="breadcrumb-item active" aria-current="page">Pelaksanaan</li>
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
                    <div class="ribbon ribbon-success">Daftar Pelaksanaan Opname Fisik
                    </div>                    
                        <ul class="box-controls pull-right">
                        </ul>
                </div>
                
                <div class="box-body">   
                    <div class="table-responsive mb-0" data-pattern="priority-columns">               
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Fakultas/Unit Rektorat" />
                            <x-isi-judul-tabel namakolom="No OPSIK" />
                            <x-isi-judul-tabel namakolom="Tanggal OPSIK" />
                            <x-isi-judul-tabel namakolom="Semester OPSIK" />
                            <x-isi-judul-tabel namakolom="Tahun Anggaran" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#data-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('opsik-opu') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_fk', name: 'nm_fk' },
            { data: 'no_opfk', name: 'no_opfk' },
            {
                data: 'tgl_opfk',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },   
            { data: 'sem_opfk', name: 'sem_opfk' },
            { data: 'thn_opfk', name: 'thn_opfk' },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += 
                    '<a href="opsik-opu-keseluruhan/' + row.id_opfk_en + '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left" title="Cetak Keseluruhan" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-print fa-sm"></i></a> ' +
                    '<a href="opsik-opu-selisih/' + row.id_opfk_en + '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left" title="Cetak Selisih" class="btn btn-rounded btn-sm btn-primary"><i class="fa fa-print fa-sm"></i></a> ' +
                    '<a href="opsik-opu-persediaan/' + row.id_opfk_en + '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left" title="Cetak Laporan Persediaan" class="btn btn-rounded btn-sm btn-warning"><i class="fa fa-print fa-sm"></i></a> ';
                    return buttons;
                }
            },     
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection