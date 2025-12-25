@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Daftar Ruangan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                            <li class="breadcrumb-item active">Daftar Ruangan</li>
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
                                <h4 class="card-title">Daftar Ruangan</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <a onClick="add()" href="javascript:void(0)" title="Tambah Data" data-bs-toggle="modal" data-bs-target=".formInputModal">
                                    <button type="button" class="btn btn-info waves-effect btn-label waves-light">
                                        <i class="bx bx-add-to-queue label-icon"></i> Tambah Data
                                    </button>
                                </a>
                                <div class="mt-3"></div>
                            </div>
                        </div>
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped" id="data-datatable">
                                <x-judul-tabel-aset>
                                    <x-isi-judul-tabel-aset namakolom="No" />
                                    <x-isi-judul-tabel-aset namakolom="Kode" />
                                    <x-isi-judul-tabel-aset namakolom="Nama Ruangan" />
                                    <x-isi-judul-tabel-aset namakolom="NIP" />
                                    <x-isi-judul-tabel-aset namakolom="Nama Penanggung Jawab Ruangan" />
                                    <x-isi-judul-tabel-aset namakolom="" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="a_id_arr" id="a_id_arr">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kode Ruangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="kode" name="kode" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kode Ruangan" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Ruangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama_ruangan" name="nama_ruangan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Ruangan" required>
                        </div>
                    </div> 
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">NIP</label>
                        <div class="col-sm-8">
                          <input type="number" id="nip_pj" name="nip_pj" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Kategori" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Penanggung Jawab Ruangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama_pj" name="nama_pj" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Penganggung Jawab Ruangan" required>
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
<style>

</style>
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
        ajax: "{{ url('ruangan-rektorat-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_kd_arr', name: 'a_kd_arr' },
            { data: 'a_nm_arr', name: 'a_nm_arr' },
            { data: 'a_nip_pj_arr', name: 'a_nip_pj_arr' },
            { data: 'a_nm_pj_arr', name: 'a_nm_pj_arr' },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.a_id_arr + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-edit fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.a_id_arr + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a>';
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
});
 
function add(){
    $('#DataForm').trigger("reset");
    $('.modal-title').html("Form Tambah Data");
    $('#data-modal').modal('show');
    $('#a_id_arr').val('');
}   
     
function editFunc(a_id_arr){
    $.ajax({
        type:"POST",
        url: "{{ url('ruangan-rektorat-aset-edit') }}",
        data: { a_id_arr: a_id_arr },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#a_id_arr').val(res.a_id_arr);
            $('#kode').val(res.a_kd_arr);
            $('#nama_ruangan').val(res.a_nm_arr);
            $('#nama_pj').val(res.a_nm_pj_arr);
            $('#nip_pj').val(res.a_nip_pj_arr);
        }
    });
}  
 
function deleteFunc(a_id_arr){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var a_id_arr = a_id_arr;
        $.ajax({
            type:"POST",
            url: "{{ url('ruangan-rektorat-aset-delete') }}",
            data: { a_id_arr : a_id_arr },
            dataType: 'json',
            success: function(res){    
                if (res.status === 1) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showSaveMessage('Data berhasil dihapus');
                }  
                else if (res.status === 2) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showDeleteMessage('Data gagal dihapus, kode kategori sudah digunakan pada data sub kategori');
                }
            }
        });
    }
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('ruangan-rektorat-aset-store')}}",
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
            else if (data.status === 2) { 
                showDangerMessage('Data gagal disimpan. Kode tidak boleh sama');
            }
            else if (data.status === 3) { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showWarningMessage('Tidak ada data yang diubah. Kode atau nama tidak boleh sama');
            }
            else if (data.status === 11) { 
                showDangerMessage('kode tidak boleh sama');
            }
            else if (data.status === 12) { 
                showDangerMessage('nama tidak boleh sama');
            }
            else if (data.status === 13) { 
                showDeleteMessage('Data gagal diubah, kode kategori sudah digunakan pada data sub kategori, anda hanya bisa mengubah nama kategori');
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
</script>
@endsection