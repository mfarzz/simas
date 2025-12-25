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
                    <div class="ribbon ribbon-success">Daftar Status Proses Untuk
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="box-body">                  
                    <table class="table table-bordered" id="data-datatable">
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
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jenis Satuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select id="idStatus" name="idStatus" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Status---</option>
                                @foreach ($daftar_status as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nm_rsp }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <select id="nama" name="nama" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Namak---</option>
                                <option value='Oleh'> Oleh </option>
                                <option value='Ke'> Ke </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Level Akses Proses</label>
                        <div class="col-sm-8">
                            <select id="role_proses" name="role_proses" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Level Akses Proses---</option>
                                @foreach ($daftar_level as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nama_rp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Level Akses Pilihan</label>
                        <div class="col-sm-8">
                            <select id="role_pilihan" name="role_pilihan" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kelompok---</option>
                                @foreach ($daftar_level as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nama_rp }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kondisi Data</label>
                        <div class="col-sm-8">
                            <select id="kondisi" name="kondisi" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kondisi Data---</option>
                                <option value='0'> Tidak Boleh Edit </option>
                                <option value='1'> Boleh Edit </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Posisi Proses</label>
                        <div class="col-sm-8">
                          <input type="number" id="posisi_proses" name="posisi_proses" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Posisi Proses" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Posisi Pilihan</label>
                        <div class="col-sm-8">
                          <input type="number" id="posisi_pilihan" name="posisi_pilihan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Posisi Pilihan" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Silahkan Pilih Kegiatan</label>
                        <div class="col-sm-8">
                            <select id="kegiatan" name="kegiatan" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kegiatan---</option>
                                @foreach ($daftar_kegiatan as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nm_rk }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status Data</label>
                        <div class="col-sm-8">
                            <select id="status_data" name="status_data" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Status Data---</option>
                                <option value='0'> Belum Selesai </option>
                                <option value='1'> Sudah Selesai </option>
                            </select>
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
        ajax: "{{ url('master-status-untuk') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_rsp', name: 'nm_rsp' },
            { data: 'nm_rspu', name: 'nm_rspu' },
            { data: 'role_id_proses', name: 'role_id_proses' },
            { data: 'role_id_pilihan', name: 'role_id_pilihan' },
            { 
                data: 'kondisi_rspu',
                name: 'kondisi_rspu',
                render: function(data, type, full, meta) {
                    if (data === 1) {
                        return 'Boleh Edit';
                    } else if (data === 0) {
                        return 'Tidak Boleh Edit';
                    } 
                }
            },
            { data: 'posisi_pb_proses', name: 'posisi_pb_proses' },
            { data: 'posisi_pb_pilihan', name: 'posisi_pb_pilihan' },
            { data: 'nm_rk', name: 'nm_rk' },
            { 
                data: 'sts_rspu',
                name: 'sts_rspu',
                render: function(data, type, full, meta) {
                    if (data === 1) {
                        return 'Belum Selesai';
                    } else if (data === 0) {
                        return 'Selesai';
                    } 
                }
            },
            { data: 'action', name: 'action', orderable: false},
        ],
        order: [[0, 'desc']]
    });
});
 
function add(){
    $('#DataForm').trigger("reset");
    $('.modal-title').html("Form Tambah Data");
    $('#data-modal').modal('show');
    $('#id').val('');
}   
     
function editFunc(id){
    $.ajax({
        type:"POST",
        url: "{{ url('master-status-untuk-edit') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id').val(res.id);
            $('#nama').val(res.nm_js);
        }
    });
}  
 
function deleteFunc(id){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('master-status-untuk-delete') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                showDeleteMessage('Data berhasil dihapus');
            }
        });
    }
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('master-status-untuk-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            console.log(data);
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
                showDangerMessage('Data gagal disimpan. Data nama jenis satuan tidak boleh sama');
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
</script>
@endsection