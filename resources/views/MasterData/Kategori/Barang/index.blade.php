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
                        <li class="breadcrumb-item" aria-current="page">Kategori</li>
                        <li class="breadcrumb-item" aria-current="page">Sub Kategori</li>
                        <li class="breadcrumb-item active" aria-current="page">Barang</li>
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
                    <div class="ribbon ribbon-success">Daftar Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                      <div class="box-header">
                        <h4 class="box-title">Rincian</strong></h4>
                      </div>
                      <div class="box-body">
                        <p>Kategori : {{ $data_kategori->kd_kt }} - {{ $data_kategori->nm_kt }}</p>
                      </div>
                    </div>
                </div>
                <div class="box-body">         
                    <div class="table-responsive mb-0" data-pattern="priority-columns">         
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Kode Lengkap" />
                            <x-isi-judul-tabel namakolom="Kode" />
                            <x-isi-judul-tabel namakolom="Nama" />
                            <x-isi-judul-tabel namakolom="Satuan" />
                            <x-isi-judul-tabel namakolom="Stok" />
                            <x-isi-judul-tabel namakolom="Nilai" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/master-kategori"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar sub sub kategori"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Kategori </button></a>
            </div>
            
        </div>
        
    </div>    
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_brg" id="id_brg">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jenis Satuan</label>
                        <div class="col-sm-8">
                            <select id="idJenis" name="idJenis" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Satuan---</option>
                                @foreach ($daftar_jenis as $baris )
                                    <option value={{ $baris->id_js }}> {{ $baris->nm_js }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Barang</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama" name="nama" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Barang" required>
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
        ajax: "{{ url('master-kategoribarang', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kd_brg', name: 'kd_brg' },
            { data: 'no_brg', name: 'no_brg' },
            { data: 'nm_brg', name: 'nm_brg' },
            { data: 'nm_js', name: 'nm_js' },
            { data: 'stok_brg', name: 'stok_brg' },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_brg + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.id_brg + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a>  ';
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
    $('#idJenis').val('').trigger('change');
    $('#id_brg').val('');
}   
     
function editFunc(id_brg){    
    $.ajax({
        type:"POST", 
        url: "{{ url('master-kategoribarang-edit') }}",
        data: { id_brg : id_brg },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_brg').val(res.id_brg);
            $('#idJenis').val(res.id_js).trigger('change'); 
            $('#nama').val(res.nm_brg);
        }
    });
}  
 
function deleteFunc(id_brg){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_brg = id_brg;
        $.ajax({
            type:"POST",
            url: "{{ url('master-kategoribarang-delete') }}",
            data: { id_brg : id_brg },
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
                    showDeleteMessage('Data gagal dihapus, kode barang sudah digunakan pada data barang masuk fakultas');
                }
                else if (res.status === 3) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showDeleteMessage('Data gagal dihapus, kode barang sudah digunakan pada data barang keluar fakultas');
                }
                else if (res.status === 4) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showDeleteMessage('Data gagal dihapus, kode barang sudah digunakan pada data barang masuk unit rektorat');
                }
                else if (res.status === 5) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showDeleteMessage('Data gagal dihapus, kode barang sudah digunakan pada data barang keluar unit rektorat');
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
        url: "{{ url('master-kategoribarang-store')}}",
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
                showDangerMessage('Data gagal disimpan. Kode atau nama tidak boleh sama');
            }
            else if (data.status === 3) { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showWarningMessage('Tidak ada data yang diubah.');
            }
            else if (data.status === 11) { 
                showDangerMessage('kode tidak boleh sama');
            }
            else if (data.status === 12) { 
                showDangerMessage('nama tidak boleh sama');
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