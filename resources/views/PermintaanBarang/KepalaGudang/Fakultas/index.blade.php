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
                        <li class="breadcrumb-item active" aria-current="page">Fakultas</li>
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
                    <div class="ribbon ribbon-success">Daftar Permintaan Barang Fakultas
                    </div>                    
                        <ul class="box-controls pull-right">
                        </ul>
                </div>
                
                <div class="box-body">   
                    <div class="table-responsive mb-0" data-pattern="priority-columns">               
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />                            
                            <x-isi-judul-tabel namakolom="Fakultas" />
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
        ajax: "{{ url('permintaan-barang-kg-pimf') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_fk', name: 'nm_fk' },
            { data: 'butuh_pbf', name: 'butuh_pbf' },
            { data: 'ket_pbf', name: 'ket_pbf' },
            {
                data: 'tgl_pbf',
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
                    if (row.level_pbs == 3) {
                    buttons +=                  
                    '<a href="javascript:void(0);" onClick="validasiFunc(' + row.id_pbf + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" title="Validasi Data" class="delete btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ';
                    }
                    buttons +=  
                    '<a href="permintaan-barang-kg-pimf-daftar/' + row.id_pbf_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> '+
                    '<a href="permintaan-barang-kg-pimf-log/' + row.id_pbf_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Riwayat Permintaan Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-history fa-sm"></i></a> ';
                    return buttons;
                }
            },     
        ],
        order: [[0, 'desc']]
    });
});

function validasiFunc(id_pbf){
    var pesan = "Apakah anda yakin akan bahwa permintaan barang ini sudah diserahkan?";
    if (confirm(pesan) == true) {
        var id_pbf = id_pbf;
        $.ajax({
            type:"POST",
            url: "{{ url('permintaan-barang-kg-pimf-validasi') }}",
            data: { id_pbf: id_pbf },
            dataType: 'json',
            success: (data) => {  
                if (data.status === 1) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showSaveMessage('Data berhasil disimpan');
                }
                else if (data.status === 2) {
                    showDangerMessage('Data gagal diajukan. Anda tidak bisa melakukan pengajuan barang jika masih ada item barang yang belum di proses');
                }
            },
        });
    }
}
</script>
@endsection