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
                        <li class="breadcrumb-item" aria-current="page">Pengadaan Barang</li>
                        <li class="breadcrumb-item" aria-current="page">Aktif</li>
                        <li class="breadcrumb-item" aria-current="page">Detail Barang</li>
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
                    <div class="ribbon ribbon-success">Daftar Barang yang Diminta
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                      <div class="box-header">
                        <h4 class="box-title">Kebutuhan Untuk <strong>{{ $data_pengadaan->nm_pb }}</strong></h4>
                      </div>
                      <div class="box-body">
                        <p>Tanggal Pengadaan : {{ \Carbon\Carbon::parse($data_pengadaan->tgl_pb)->format('d M Y')}}</p> 
                      </div>
                    </div>
                </div>
                <div class="box-body">                  
                    <table class="table table-bordered responsive" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Kategori" />
                            <x-isi-judul-tabel namakolom="Sub Kategori" />
                            <x-isi-judul-tabel namakolom="Nama Barang" />
                            <x-isi-judul-tabel namakolom="Satuan" />
                            <x-isi-judul-tabel namakolom="Stok Sekarang" />
                            <x-isi-judul-tabel namakolom="Jumlah Diminta" />
                            <x-isi-judul-tabel namakolom="Jumlah Disetujui" />
                            <x-isi-judul-tabel namakolom="Perkiraan Harga" />
                            <x-isi-judul-tabel namakolom="Total Harga" />
                            <x-isi-judul-tabel namakolom="Status" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                </div>
                <a href="/pengadaan-barang-aktif"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar kategori"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Pengadaan Barang Aktif </button></a>
            </div>
            
        </div>
        
    </div>    
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">
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
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah yang Diminta</label>
                        <div class="col-sm-8">
                          <input type="text" id="jumlah" name="jumlah" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Perkiraan Harga</label>
                        <div class="col-sm-8">
                          <input type="number" id="estimasi_harga" name="estimasi_harga" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Harga" required>
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


<div  class="modal fade" id="data-modal-validasi" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title-validasi">Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataFormValidasi" name="DataFormValidasi" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_validasi" id="id_validasi">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Kategori</label>
                        <div class="col-sm-8">
                            <input type="text" id="nmKategori" name="nmKategori" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kategori" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Kategori</label>
                        <div class="col-sm-8">
                            <input type="text" id="nmSubkategori" name="nmSubkategori" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan SubKategori" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Item</label>
                        <div class="col-sm-8">
                            <input type="text" id="nmItem" name="nmItem" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Item Barang" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Stok Terakhir</label>
                        <div class="col-sm-8">
                            <input type="text" id="stokItem" name="stokItem" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Stok Barang" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Perkiraan Harga</label>
                        <div class="col-sm-8">
                          <input type="number" id="estimasi_harga_awal" name="estimasi_harga_awal" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Harga" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah yang Diminta</label>
                        <div class="col-sm-8">
                          <input type="text" id="jumlah_awal" name="jumlah_awal" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah yang Disetujui</label>
                        <div class="col-sm-8">
                          <input type="text" id="jumlah_disetujui" name="jumlah_disetujui" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <select id="status_barang" name="status_barang" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Status---</option>
                                @foreach ($tampil_status as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nm_rspd }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="ket_validasi" name="ket_validasi" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Keterangan">
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
           url:"/pengadaan-barang-aktif-detail-subkategori?subKat="+kat,
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
           url:"/pengadaan-barang-aktif-detail-item?item="+subKat,
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

var userRoleId = {{ auth()->user()->role_id }};    
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#data-datatable').DataTable({
        processing: true,
        serverSide: true,    
        responsive: true,    
        ajax: "{{ url('pengadaan-barang-aktif-detail', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_kl', name: 'nm_kl' },
            { data: 'nm_sskt', name: 'nm_sskt' },
            { data: 'nm_brg', name: 'nm_brg' },
            { data: 'nm_js', name: 'nm_js' },
            { data: 'stok_brg', name: 'stok_brg' },
            { data: 'jmlh_pbd_awal', name: 'jmlh_pbd_awal' },
            { data: 'jmlh_pbd', name: 'jmlh_pbd' },
            { 
                data: 'hrg_estimasi_pbd',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            { 
                data: 'total_hrg',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            { data: 'nm_rspd', name: 'nm_rspd' },
             
            { data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (row.id_rspd === 0 || row.id_rspd === 3){
                        var buttons = '';
                        buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
                        return buttons;
                    } 
                    else if(row.id_rspd === 2 && userRoleId === 4){
                        var buttons = '';
                        buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="showFunc(' + row.id + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a>';
                        return buttons;
                    }else {
                        return '';
                    }
                }
            },
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
     
function editFunc(id){
    $.ajax({
        type: "POST", 
        url: "{{ url('pengadaan-barang-aktif-detail-edit') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res){            
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id').val(res.id);
            $('#kategori').val(res.kd_kl).trigger('change'); 
            
            $.ajax({
                type: "GET",
                url: "/pengadaan-barang-aktif-detail-subkategori?subKat=" + res.kd_kl,
                dataType: 'JSON',
                success: function(subkategori) {
                    $("#idSubkategori").empty();
                    $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
                    $.each(subkategori, function(nm_sskt, kd_sskt) {
                        $("#idSubkategori").append('<option value="' + kd_sskt + '">' + kd_sskt + ' - ' + nm_sskt + '</option>');
                    });
                    
                    $('#idSubkategori').val(res.kd_sskt).trigger('change');
                    
                    $.ajax({
                        type: "GET",
                        url: "/pengadaan-barang-aktif-detail-item?item=" + res.kd_sskt,
                        dataType: 'JSON',
                        success: function(item) {
                            $("#idItem").empty();
                            $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                            $.each(item, function(ket, kd_brg) {
                                $("#idItem").append('<option value="' + kd_brg + '">' + ket + '</option>');
                            });
                            
                            $('#idItem').val(res.kd_brg).trigger('change');
                        }
                    });
                }
            });

            $('#jumlah').val(res.jmlh_pbd_awal);
            $('#estimasi_harga').val(res.hrg_estimasi_pbd);
        }
    });
}

function showFunc(id){
    $.ajax({
        type: "POST", 
        url: "{{ url('pengadaan-barang-aktif-detail-edit') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res){            
            $('.modal-title-validasi').html("Form Validasi Data");
            $('#data-modal-validasi').modal('show');
            $('#id_validasi').val(res.id);
            $('#nmKategori').val(res.nm_kl);
            $('#nmSubkategori').val(res.nm_sskt);
            $('#stokItem').val(res.stok_brg);
            $('#nmItem').val(res.nm_brg);
            $('#estimasi_harga_awal').val(res.hrg_estimasi_pbd);
            $('#jumlah_awal').val(res.jmlh_pbd_awal);
            $('#jumlah_disetujui').val('');
            $('#ket_validasi').val('');
            $('#status_barang').val('');
        }
    });
}
 
function deleteFunc(id){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('pengadaan-barang-aktif-detail-delete') }}",
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
        url: "{{ url('pengadaan-barang-aktif-detail-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            //showSaveMessage(data.status);
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
                showDangerMessage('Data gagal disimpan. Jenis barang tidak boleh sama');
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

$('#DataFormValidasi').submit(function(e) {    
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ url('pengadaan-barang-aktif-detail-validasi')}}",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {     
            if (data.status === 1) {
                $("#data-modal-validasi").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save").attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            } else {
                showDangerMessage('Data gagal disimpan');
            }
        },
        error: function(data) {
            console.log(data);
        }
    });
});
</script>
@endsection