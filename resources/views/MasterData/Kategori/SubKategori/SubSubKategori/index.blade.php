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
                        <li class="breadcrumb-item active" aria-current="page">Sub Sub Kategori</li>
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
                    <div class="ribbon ribbon-success">Daftar Sub Sub Kategori
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
                        <p>Kategori : {{ $data_subkategori->kd_kt }} - {{ $data_subkategori->nm_kt }}</p>
                        <p>Sub Kategori : {{ $data_subkategori->kd_skt }} - {{ $data_subkategori->nm_skt }}</p>
                      </div>
                    </div>
                </div>
                <div class="box-body">        
                    <div class="table-responsive mb-0" data-pattern="priority-columns">          
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Kelompok" />
                            <x-isi-judul-tabel namakolom="Kode Lengkap" />
                            <x-isi-judul-tabel namakolom="Kode" />
                            <x-isi-judul-tabel namakolom="Nama" />
                            <x-isi-judul-tabel namakolom="Nilai" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/master-subkategori/{{ $kd_kt }}"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar sub kategori"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Sub Kategori </button></a>
            </div>
            
        </div>
        
    </div>    
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sub Sub Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_sskt" id="id_sskt">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kelompok</label>
                        <div class="col-sm-8">
                            <select id="idKelompok" name="idKelompok" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kelompok---</option>
                                @foreach ($daftar_kelompok as $baris )
                                    <option value={{ $baris->kd_kl }}> {{ $baris->kd_kl }} - {{ $baris->nm_kl }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Sub Sub Kategori</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama" name="nama" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Kategori" required>
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
        ajax: "{{ url('master-subsubkategori', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_kelompok', name: 'nm_kelompok' },
            { data: 'kd_sskt', name: 'kd_sskt' },
            { data: 'no_sskt', name: 'no_sskt' },
            { data: 'nm_sskt', name: 'nm_sskt' },
            {
                data: 'total_nilai',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_sskt + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.id_sskt + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> '  +
                    '<a href="/master-kategoribarang/' + row.id_sskt_en + '" id="delete-compnay" data-bs-toggle="tooltip" data-bs-placement="left" title="Sub Sub Kategori" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> ';
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
    $('#idKelompok').val('').trigger('change');
    $('#id_sskt').val('');
}   
     
function editFunc(id_sskt){    
    $.ajax({
        type:"POST", 
        url: "{{ url('master-subsubkategori-edit') }}",
        data: { id_sskt : id_sskt },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_sskt').val(res.id_sskt);
            $('#idKelompok').val(res.kd_kl).trigger('change'); 
            $('#nama').val(res.nm_sskt);
        }
    });
}  
 
function deleteFunc(id_sskt){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_sskt = id_sskt;
        $.ajax({
            type:"POST",
            url: "{{ url('master-subsubkategori-delete') }}",
            data: { id_sskt : id_sskt },
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
                    showDeleteMessage('Data gagal dihapus, kode sub sub kategori sudah digunakan pada data barang');
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
        url: "{{ url('master-subsubkategori-store')}}",
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
                showWarningMessage('Tidak ada data yang diubah. Kode atau nama tidak boleh sama');
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