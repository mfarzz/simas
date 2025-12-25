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
                        <li class="breadcrumb-item active" aria-current="page">Pengguna Rumah Sakit</li>
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
                    <div class="ribbon ribbon-success">Daftar Pengguna Rumah Sakit
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="box-body">  
                    <div class="table-responsive mb-0" data-pattern="priority-columns">                
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Bidang/UPT" />
                            <x-isi-judul-tabel namakolom="Jabatan" />
                            <x-isi-judul-tabel namakolom="Username" />
                            <x-isi-judul-tabel namakolom="Password" />
                            <x-isi-judul-tabel namakolom="Nama" />
                            <x-isi-judul-tabel namakolom="Role Aplikasi" />
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
                <h5 class="modal-title">Form Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Username</label>
                        <div class="col-sm-8">
                          <input type="text" id="username" name="username" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Username" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Pengguna</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama" name="nama" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Pengguna" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jenis Kelamin</label>
                        <div class="col-sm-8">
                            <select id="jk" name="jk" class="form-select form-control" aria-label="Default select example" required>
                                <option value=''>Silahkan Pilih Jenis Kelamin</option>
                                <option value="L"> Laki-laki </option>
                                <option value="P"> Perempuan </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No Wa</label>
                        <div class="col-sm-8">
                          <input type="number" id="no_wa" name="no_wa" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan No Wa" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                          <input type="email" id="email" name="email" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Email" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Bagian Rumah Sakit</label>
                        <div class="col-sm-8">
                            <select id="idUnit" name="idUnit" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Bagian Rumah Sakit---</option>
                                @foreach ($daftar_unit as $baris )
                                    <option value={{ $baris->id_urs }}> {{ $baris->nm_urs }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jabatan Bagian Rumah Sakit</label>
                        <div class="col-sm-8">
                            <select id="idUnitjabatan" name="idUnitjabatan" class="form-select form-control" aria-label="Default select example" required>
                                <option value=''>---Silahkan Pilih Jabatan di Bagian Rektorat---</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Role Penggunaan Aplikasi</label>
                        <div class="col-sm-8">
                            <select id="pengguna" name="pengguna" class="form-select form-control" aria-label="Default select example" required>
                                <option value=''>Silahkan Pilih Role Penggunaan Aplikasi</option>
                                <option value="1"> Hanya Aplikasi Persediaan </option>
                                <option value="2"> Hanya Aplikasi Aset </option>
                                <option value="3"> Aplikasi Persediaan dan Aset </option>
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
$('#idUnit').change(function(){   
    var unit = $(this).val();
    if(unit){
        $.ajax({
           type:"GET",
           url:"/master-pengguna-rumahsakit-unitjabatan?unitjabatan="+unit,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idUnitjabatan").empty();
                $("#idUnitjabatan").append("<option value=''>---Silahkan Pilih Jabatan di Bagian Rektorat---</option>");
                $.each(res,function(nm_ursj,id_ursj){
                    $("#idUnitjabatan").append('<option value="'+id_ursj+'">'+nm_ursj+'</option>');
                });
            }else{
               $("#idUnitjabatan").empty();
            }
           }
        });
    }else{
        $("#idUnitjabatan").empty();
    }
});

$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#data-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('master-pengguna-rumahsakit') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_urs', name: 'nm_urs' },
            { data: 'nama_rp', name: 'nama_rp' },
            { data: 'username', name: 'username' },
            { data: 'password_text', name: 'password_text' },
            { data: 'name', name: 'name' },   
            {
                data: 'pengguna',
                name: 'pengguna',
                render: function (data, type, row) {
                    if (data === 1) {
                        return 'Hanya aplikasi persediaan';
                    } else if (data === 2) {
                        return 'Hanya aplikasi aset';
                    } else if (data === 3) {
                        return 'Aplikasi persediaan dan aset';
                    }
                }
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += 
                    '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="resetFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Reset Password" class="delete btn btn-info btn-rounded btn-sm waves-effect"><i class="fa fa-unlock fa-sm"></i></a>'
                    ;
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
    $('#id').val('');
}   
     
function editFunc(id){    
    $.ajax({
        type:"POST",
        url: "{{ url('master-pengguna-rumahsakit-edit') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id').val(res.id);
            $('#username').val(res.username);
            $('#username').prop('readonly', true);
            $('#nama').val(res.name);
            $('#jk').val(res.jk);
            $('#no_wa').val(res.nowa);
            $('#email').val(res.email);
            $('#idUnit').val(res.id_urs);
            $('#pengguna').val(res.pengguna);
            $.ajax({
                type: "GET",
                url: "/master-pengguna-rumahsakit-unitjabatan?unitjabatan=" + res.id_urs,
                dataType: 'JSON',
                success: function(item) {
                    $("#idUnitjabatan").empty();
                    $("#idUnitjabatan").append("<option value=''>---Silahkan Pilih Jabatan di Bagian Rektorat---</option>");
                    $.each(item, function(nm_ursj, id_ursj) {
                        $("#idUnitjabatan").append('<option value="' + id_ursj + '">' + nm_ursj + '</option>');
                    });
                    
                    $('#idUnitjabatan').val(res.id_ursj).trigger('change');
                }
            });
        }
    });
}  
 
function deleteFunc(id){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('master-pengguna-rumahsakit-delete') }}",
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

function resetFunc(id){
    var pesan = "Apakah anda yakin akan reset password akun ini?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('master-pengguna-rumahsakit-reset') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                showSaveMessage('Password berhasil direset, saat ini password sama dengan username');
            }
        });
    }
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('master-pengguna-rumahsakit-store')}}",
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
                showDangerMessage('Data gagal disimpan. Username tidak boleh sama');
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