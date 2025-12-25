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
                        <li class="breadcrumb-item" aria-current="page">Reklasifikasi</li>
                        <li class="breadcrumb-item" aria-current="page">Jenis Barang</li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
                    <div class="ribbon ribbon-success">Daftar Reklasifikasi Jenis Barang Detail
                    </div>                    
                        <ul class="box-controls pull-right">
                            @if($cek_rf->status_rf==0)
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                            @endif
                        </ul>
                </div>
                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                      <div class="box-header">
                        <h4 class="box-title">Rincian</strong></h4>
                      </div>
                      <div class="box-body">
                        <p>Nomor Berita Acara : {{ $cek_rf->no_rf }}</p>
                        <p>Tanggal Berita Acara : {{ $tgl = \Carbon\Carbon::parse($cek_rf->tgl_rf)->formatLocalized('%d %B %Y'); }}</p>
                        <p>Status : @if($cek_rf->status_rf==0) Draf @elseif($cek_rf->status_rf == 1)Kirim @endif</p>
                      </div>
                    </div>
                </div>
                <div class="box-body">    
                    <div class="table-responsive mb-0" data-pattern="priority-columns">              
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Barang Lama" />
                            <x-isi-judul-tabel namakolom="Barang Baru" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/reklasifikasi-jenisbrg-opf-khusus"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar reklasifikasi"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Reklasifikasi </button></a>
            </div>
        </div>
    </div>
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reklasifikasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_bkf" id="id_bkf">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barang Lama</label>
                        <div class="col-sm-8">
                            <select id="barang_lama" name="barang_lama" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Barang---</option>
                                @foreach ($daftar_bm as $baris_bm )
                                    <option value={{ $baris_bm->kd_brg }}> {{ $baris_bm->kd_brg }} - {{ $baris_bm->nm_brg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
    $('#idItem').val('').trigger('change');
    var kat = $(this).val();    
    if(kat){
        $.ajax({
           type:"GET",
           url:"/reklasifikasi-jenisbrg-opf-khusus-detail-subkategori?subKat="+kat,
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
    if(isTambahData){  
        var subKat = $(this).val();    
        if(subKat){
            $.ajax({
            type:"GET",
            url:"/reklasifikasi-jenisbrg-opf-khusus-detail-item?item="+subKat,
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
        ajax: "{{ url('reklasifikasi-jenisbrg-opf-khusus-detail', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kd_brg_lama', name: 'kd_brg_lama' },
            { data: 'kd_brg_baru', name: 'kd_brg_baru' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var buttons = '';
                    if (row.status_rf == 0) {
                        buttons += 
                            '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_rfd + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
                    }
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
});
 
var isTambahData = false;

function add(){
    isTambahData = true;
    $('#idUr').val('').trigger('change');
    $('#kategori').val('').trigger('change');
    $('#idSubkategori').val('').trigger('change');
    $('#idItem').val('').trigger('change');
    $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
    $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
    $('#DataForm').trigger("reset");
    $('#DataModal').html("Add Data");
    $('#data-modal').modal('show');
    $('#id_bkf').val('');
}   

function editFunc(id_bkf){  
    isTambahData = false;  
    $.ajax({
        type:"POST",
        url: "{{ url('barang-keluar-opf-khusus-edit') }}",
        data: { id_bkf: id_bkf },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_bkf').val(res.id_bkf);
            $('#kategori').val(res.kd_kl).trigger('change');
            $('#id_bkf').val(res.id_bkf);
            $('#jumlah').val(res.jmlh_bkf);            
            
            $.ajax({
                type: "GET",
                url: "/barang-keluar-opf-khusus-subkategori?subKat=" + res.kd_kl,
                dataType: 'JSON',
                success: function(subKat) {
                    $("#idSubkategori").empty();
                    $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
                    $.each(subKat, function(nm_sskt,kd_sskt) {
                        $("#idSubkategori").append('<option value="'+kd_sskt+'">'+kd_sskt+' - '+nm_sskt+'</option>');
                    });
                    $('#idSubkategori').val(res.kd_sskt).trigger('change');
                }
            });

            $.ajax({
                type: "GET",
                url: "/barang-keluar-opf-khusus-item?item=" + res.kd_sskt,
                dataType: 'JSON',
                success: function(item) {
                    $("#idItem").empty();
                    $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                    $.each(item, function(ket, kd_brg) {
                        $("#idItem").append('<option value="' + kd_brg + '">' + ket + '</option>');
                    });                    
                    $('#idItem').val(res.kd_brg).trigger('change');
                    isTambahData = true;
                }
            });
        }
    });    
}  
 
function deleteFunc(id_bkf){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_bkf = id_bkf;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-keluar-opf-khusus-delete') }}",
            data: { id_bkf : id_bkf },
            dataType: 'json',
            success: function(res){ 
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                if (res.status === 1) {
                    showSaveMessage('Data berhasil dihapus');
                }
                else if (res.status === 2) {
                    showDeleteMessage('Maaf, tanggal dikeluarkan yang anda hapus lebih kecil dari data tanggal dikeluarkan yang sudah ada pada barang keluar');
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
        url: "{{ url('barang-keluar-opf-khusus-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) {      
                $('#idUr').val('').trigger('change');
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
            else if (data.status === 2)
            {
                showDangerMessage('Maaf, jumlah yang anda keluarkan melebihi jumlah stok yang tersedia');
            }
            else if (data.status === 3)
            {
                showDangerMessage('Maaf, tanggal dikeluarkan yang anda entrikan lebih kecil dari data tanggal dikeluarkan yang sudah ada pada barang keluar');
            }
            else if (data.status === 4)
            {
                showDangerMessage('Maaf, item barang sudah anda entrikan dalam nomor nota yang sama');
            }
            else
            {
                showWarningMessage('Data gagal disimpan');
            }
        },
        error: function(data){
            console.log(data);
        }
    });
});
</script>
@endsection