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

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permintaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_pbf" id="id_pbf">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kebutuhan Untuk</label>
                        <div class="col-sm-8">
                          <input type="text" id="butuh" name="butuh" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kebutuhannya" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Permintaan</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl_permintaan" name="tgl_permintaan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Permintaan" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Alasan Penolakan</label>
                        <div class="col-sm-8">
                          <input type="text" id="alasan" name="alasan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Alasan Penolakan" required>
                        </div>
                    </div>
                    <div class="col-sm-offset-2 col-sm-10"><br/>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Kembali</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
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
        ajax: "{{ url('permintaan-barang-kasilogistik-pimf') }}",
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
                    if (row.level_pbs == 2) {
                    buttons +=                  
                    '<a href="javascript:void(0);" onClick="validasiFunc(' + row.id_pbf + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" title="Validasi Data" class="delete btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ' +
                    '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tolak Permintaan Barang" onClick="cekFunc(' + row.id_pbf + ')" data-original-title="Tolak Permintaan Barang" class="edit btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-window-close fa-sm"></i></a> ';
                    }
                    buttons +=  
                    '<a href="permintaan-barang-kasilogistik-pimf-daftar/' + row.id_pbf_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> '+
                    '<a href="permintaan-barang-kasilogistik-pimf-log/' + row.id_pbf_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Riwayat Permintaan Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-history fa-sm"></i></a> ';
                    return buttons;
                }
            },     
        ],
        order: [[0, 'desc']]
    });
});

function cekFunc(id_pbf){      
    $.ajax({
        type:"POST",
        url: "{{ url('permintaan-barang-kasilogistik-pimf-cektolak') }}",
        data: { id_pbf: id_pbf },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Cek Data");
            $('#data-modal').modal('show');
            $('#id_pbf').val(res.id_pbf);            
            $('#butuh').val(res.butuh_pbf);         
            $('#tgl_permintaan').val(res.tgl_pbf);           
        }
    });
}

$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('permintaan-barang-kasilogistik-pimf-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) {      
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            }
            else if (data.status === 4) {      
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil diubah');
            }
            else if (data.status === 3) { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showWarningMessage('Tidak ada data yang diubah');
            }
            else
            {
                showDangerMessage('Data gagal disimpan');
            }
        },
        error: function(data){
            console.log(data);
        }
    });
});

function validasiFunc(id_pbf){
    var pesan = "Apakah anda yakin akan menyetujui permintaan barang ini ?";
    if (confirm(pesan) == true) {
        var id_pbf = id_pbf;
        $.ajax({
            type:"POST",
            url: "{{ url('permintaan-barang-kasilogistik-pimf-validasi') }}",
            data: { id_pbf: id_pbf },
            dataType: 'json',
            success: (data) => {  
                if (data.status === 1) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showSaveMessage('Data berhasil diajukan');
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