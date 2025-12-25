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
                        <li class="breadcrumb-item" aria-current="page">Barang Usang</li>
                        <li class="breadcrumb-item active" aria-current="page">Khusus</li>
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
                    <div class="ribbon ribbon-success">Daftar Barang Usang Khusus
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="box-body">                  
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Keterangan" />
                            <x-isi-judul-tabel namakolom="Kategori" />
                            <x-isi-judul-tabel namakolom="Sub Kategori" />
                            <x-isi-judul-tabel namakolom="Nama Barang" />
                            <x-isi-judul-tabel namakolom="Jumlah" />
                            <x-isi-judul-tabel namakolom="Tanggal" />
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
                <h5 class="modal-title">Barang Masuk Khusus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Kategori</label>
                        <div class="col-sm-8">
                            <select id="kategori" name="idKategori" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kategori---</option>
                                @foreach ($daftar_kategori as $baris )
                                    <option value={{ $baris->kd_kl }}> {{ $baris->kd_kl }} - {{ $baris->nm_kl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Kategori</label>
                        <div class="col-sm-8">
                            <select id="idSubkategori" name="idSubkategori" class="form-select form-control" aria-label="Default select example" required>
                                <option value=''>---Silahkan Pilih Sub Kategori---</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Item</label>
                        <div class="col-sm-8">
                            <select id="idItem" name="idItem" class="form-select form-control" required>
                                <option value=''>---Silahkan Pilih Item---</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah</label>
                        <div class="col-sm-8">
                          <input type="number" name="jumlah" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Keluar</label>
                        <div class="col-sm-8">
                          <input type="date" name="tgl_tentu" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Keluar" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                        <div class="col-sm-8">
                          <input type="text" name="ket_bu" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Keterangan" required>
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

<script type="text/javascript">
$('#kategori').change(function(){    
    var kat = $(this).val();    
    if(kat){
        $.ajax({
           type:"GET",
           url:"/barang-usang-khusus-subkategori?subKat="+kat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idSubkategori").empty();
                $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
                $.each(res,function(nm_sskt,kd_sskt){
                    $("#idSubkategori").append('<option value="'+kd_sskt+'">'+kd_sskt+' - '+nm_sskt+'</option>');
                });
            }else{
               $("#idSubkategori").empty();
            }
           }
        });
    }else{
        $("#idSubkategori").empty();
    }
});

$('#idSubkategori').change(function(){    
    var subKat = $(this).val();    
    if(subKat){
        $.ajax({
           type:"GET",
           url:"/barang-usang-khusus-item?item="+subKat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idItem").empty();
                $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                $.each(res,function(ket,kd_brg){
                    $("#idItem").append('<option value="'+kd_brg+'">'+ket+'</option>');
                });
            }else{
               $("#idItem").empty();
            }
           }
        });
    }else{
        $("#idItem").empty();
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
        ajax: "{{ url('barang-usang-khusus') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'ket_bu', name: 'ket_bu' },
            { data: 'nm_kelompok', name: 'nm_kelompok' },
            { data: 'nm_subsubkategori', name: 'nm_subsubkategori' },
            { data: 'nm_brg', name: 'nm_brg' },
            { 
                data: 'jmlh_bu', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },          
            {
                data: 'tgltentu_bu',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            { data: 'action', name: 'action', orderable: false},
        ],
        order: [[0, 'desc']]
    });
});
 
function add(){
    $('#kategori').val('').trigger('change');
    $('#idSubkategori').val('').trigger('change');
    $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
    $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
    $('#DataForm').trigger("reset");
    $('#DataModal').html("Add Data");
    $('#data-modal').modal('show');
    $('#id').val('');
}   

function deleteFunc(id){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-usang-khusus-delete') }}",
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
        url: "{{ url('barang-usang-khusus-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {     
            if (data.status === 1) {
                $('#kategori').val('').trigger('change');
                $('#idSubkategori').val('').trigger('change');
                $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
                $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            }
            else if (data.status === 0) { 
                showDangerMessage('Maaf, jumlah yang anda keluarkan melebihi jumlah stok yang tersedia');
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