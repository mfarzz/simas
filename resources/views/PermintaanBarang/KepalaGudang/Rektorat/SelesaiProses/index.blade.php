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
                        <li class="breadcrumb-item" aria-current="page">Permintaan Barang Selesai di Proses</li>
                        <li class="breadcrumb-item active" aria-current="page">Unit Rektorat</li>
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
                    <div class="ribbon ribbon-success">Daftar Permintaan Barang Selesai di Proses Unit Rektorat
                    </div>                    
                        <ul class="box-controls pull-right">
                        </ul>
                </div>
                
                <div class="box-body">   
                    <div class="table-responsive mb-0" data-pattern="priority-columns">               
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />                            
                            <x-isi-judul-tabel namakolom="Unit Rektorat" />
                            <x-isi-judul-tabel namakolom="Kebutuhan" />
                            <x-isi-judul-tabel namakolom="Keterangan" />
                            <x-isi-judul-tabel namakolom="Tanggal Permintaan" />
                            <x-isi-judul-tabel namakolom="Status" />
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
        ajax: "{{ url('permintaan-barang-kg-selesaiproses-pimr') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_ur', name: 'nm_ur' },
            { data: 'butuh_pbr', name: 'butuh_pbr' },
            { data: 'ket_pbr', name: 'ket_pbr' },
            {
                data: 'tgl_pbr',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },   
            { data: 'nm_pbs', name: 'nm_pbs' }, 
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons +=  
                    '<a href="permintaan-barang-kg-pimr-selesaiproses-daftar/' + row.id_pbr_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> '+
                    '<a href="permintaan-barang-kg-pimr-selesaiproses-log/' + row.id_pbr_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Riwayat Permintaan Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-history fa-sm"></i></a> ';
                    return buttons;
                }
            },     
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection