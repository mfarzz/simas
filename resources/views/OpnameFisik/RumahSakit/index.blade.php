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
                        <li class="breadcrumb-item" aria-current="page">Opname Fisik</li>
                        <li class="breadcrumb-item active" aria-current="page">Pelaksanaan</li>
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
                    <div class="ribbon ribbon-success">Daftar Pelaksanaan Opname Fisik
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
                            <x-isi-judul-tabel namakolom="No OPSIK" />
                            <x-isi-judul-tabel namakolom="Tanggal OPSIK" />
                            <x-isi-judul-tabel namakolom="Semester OPSIK" />
                            <x-isi-judul-tabel namakolom="Tahun Anggaran" />
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
                <h5 class="modal-title">Barang Masuk SP2D</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_opurs" id="id_opurs">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No OPSIK</label>
                        <div class="col-sm-8">
                          <input type="text" id="no_opurs" name="no_opurs" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nomor OPSIK" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal OPSIK</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl_opurs" name="tgl_opurs" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal OPSIK" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Semester</label>
                        <div class="col-sm-8">
                          <input type="number" id="semester" name="semester" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Semester" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tahun Anggaran</label>
                        <div class="col-sm-8">
                          <input type="number" id="tahun" name="tahun" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tahun Anggaran" required>
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

<div  class="modal fade" id="data-modal-upload" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title-upload">Upload Berita Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataFormUpload" name="DataFormUpload" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_opurs_cek" id="id_opurs_cek">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Upload File</label>
                        <div class="col-sm-8">
                          <input type="file" id="dokumen" name="dokumen" class="form-control" id="horizontal-firstname-input" placeholder="Upload Berita Acara" required>
                        </div>
                    </div>                   
                    <div class="form-group" id="dokumen-preview">
                        <div class="col-md-12">
                          (Dokumen Tidak Ditemukan)
                        <span class="help-block"></span>
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
        ajax: "{{ url('opsik-oprs') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_opurs', name: 'no_opurs' },
            {
                data: 'tgl_opurs',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },   
            { data: 'sem_opurs', name: 'sem_opurs' },
            { data: 'thn_opurs', name: 'thn_opurs' },
            {
                data: 'status_opurs',
                name: 'status_opurs',
                render: function (data, type, row) {
                    if (data === 1) {
                        return 'Kirim';
                    } else if (data === 0) {
                        return 'Draft';
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
                    if (row.status_opurs == 0) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_opurs + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_opurs + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ' +                    
                    '<a href="javascript:void(0);" onClick="validasiFunc(' + row.id_opurs + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" title="Validasi Data" class="delete btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ';
                    }
                    else
                    {
                        buttons +=                         
                        '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Upload Data" onClick="uploadFunc(' + row.id_opurs + ')" data-original-title="Upload" class="edit btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-upload fa-sm"></i></a> ' +
                        '<a href="opsik-oprs-lampiran/' + row.id_opurs_en + '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left" title="Cetak Lampiran" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-print fa-sm"></i></a> ' +
                        '<a href="opsik-oprs-persediaan/' + row.id_opurs_en + '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left" title="Cetak Laporan Persediaan" class="btn btn-rounded btn-sm btn-warning"><i class="fa fa-print fa-sm"></i></a> ';
                    }
                    buttons += 
                    '<a href="opsik-oprs-detail/' + row.id_opurs_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> ';
                    
                    
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
    $('#id_opurs').val('');
}   
     
function editFunc(id_opurs){    
    $.ajax({
        type:"POST",
        url: "{{ url('opsik-oprs-edit') }}",
        data: { id_opurs: id_opurs },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_opurs').val(res.id_opurs);            
            $('#no_opurs').val(res.no_opurs);
            $('#tgl_opurs').val(res.tgl_opurs);
            $('#sem_opurs').val(res.sem_opurs);
            $('#thn_opurs').val(res.thn_opurs);
        }
    });
}  
 
function deleteFunc(id_bmrss){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_bmrss = id_bmrss;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-masuk-sp2d-oprs-khusus-delete') }}",
            data: { id_bmrss: id_bmrss },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                showSaveMessage('Data berhasil dihapus');
            }
        });
    }
}

function validasiFunc(id_opurs){
    var pesan = "Apakah anda yakin akan validasi data ini ?";
    if (confirm(pesan) == true) {
        var id_opurs = id_opurs;
        $.ajax({
            type:"POST",
            url: "{{ url('opsik-oprs-validasi') }}",
            data: { id_opurs: id_opurs },
            dataType: 'json',
            success: (data) => {  
                if (data.status === 1) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showSaveMessage('Data berhasil divalidasi');
                }
                else if (data.status === 2) {
                    showDangerMessage('Data gagal divalidasi. Anda tidak bisa melakukan validasi jika belum ada item barang yang anda entrikan');
                }
                else if (data.status === 3) {
                    showDangerMessage('Data gagal divalidasi. Nilai SP2D tidak sama dengan total nilai barang masuk');
                }
            },
        });
    }
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('opsik-oprs-store')}}",
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
                showDangerMessage('Data gagal disimpan. Data semester dan tahun anggaran tidak boleh sama');
            }
            else if (data.status === 5) { 
                showDangerMessage('Data gagal disimpan. Maaf untuk simpan data baru tidak boleh ada yang masih berstatus draf');
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

function uploadFunc(id_opurs){
    $.ajax({
        type:"POST",
        url: "{{ url('opsik-oprs-store-upload-cek') }}",
        data: { id_opurs: id_opurs },
        dataType: 'json',
        success: function(res){
            $('#DataFormUpload').trigger("reset");
            $('.modal-title-upload').html("Form Upload Berita Acara");
            $('#data-modal-upload').modal('show');
            $('#id_opurs_cek').val(res.id_opurs);
            if (res.file_opurs) {           
                $('#label-dokumen').text('Change PDF'); // Ganti teks label
                var pdfPath = window.location.origin + "/storage/berita_acara/" + res.file_opurs;
                loadPDF(pdfPath);
            } else {
                $('#label-dokumen').text('Upload Photo'); // label photo upload
                dokumenPreview.text('(No photo)');
            }
        }
    });
}   
function loadPDF(pdfPath) {
    $('#dokumen-preview div').html('<object data="' + pdfPath + '" type="application/pdf" width="100%" height="400"></object>');
}

$('#DataFormUpload').submit(function(e) {
    e.preventDefault();
    var formDataUpload = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('opsik-oprs-store-upload')}}",
        data: formDataUpload,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) {      
                $("#data-modal-upload").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil diupload');
            }
            else if (data.status === 2) { 
                showDangerMessage('Data gagal diupload. File yang diperbolehkan upload hanya pdf');
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