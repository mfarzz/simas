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
                        <li class="breadcrumb-item" aria-current="page">Barang Keluar</li>
                        <li class="breadcrumb-item active" aria-current="page">Nota</li>
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
                    <div class="ribbon ribbon-success">Daftar Nota Barang Keluar
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="box-body">
                    <form action="javascript:void(0)" id="DataFormPencarian" name="DataFormPencarian" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Dari Tanggal</label>
                            <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                                <input type="date" class="form-control" placeholder="Pencarian" id='tgl_awal' name='tgl_awal' required>
                            </div>
                            <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Sampai Tanggal</label>
                            <div class="col-3 col-sm-3 col-md-3 col-lg-3">
                                <input type="date" class="form-control" placeholder="Pencarian" id='tgl_akhir' name='tgl_akhir' required>
                            </div>
    
                            <div class="col-4 col-sm-4 col-md-4 col-lg-2">
                                <button type="submit" class="waves-effect waves-light btn btn-primary mb-5" id="cetakBtn">
                                    <i class="fa fa-print"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-responsive mb-0" data-pattern="priority-columns">               
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Nama Unit Penerima" />
                            <x-isi-judul-tabel namakolom="No Nota" />
                            <x-isi-judul-tabel namakolom="Tanggal Nota" />
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
                <h5 class="modal-title">Barang Keluar Nota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_bkrsn" id="id_bkrsn">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Unit Penerima</label>
                        <div class="col-sm-8">
                            <select id="penerima" name="penerima" class="form-select form-control" required>
                                <option value=''>---Silahkan Pilih Unit Penerima---</option>
                                @foreach ($daftar_penerima as $baris )
                                    <option value={{ $baris->id_bkprs }}> {{ $baris->nm_bkprs }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No Nota</label>
                        <div class="col-sm-8">
                          <input type="text" id="no_nota" name="no_nota" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nomor Nota" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Nota</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl_nota" name="tgl_nota" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Nota" required>
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
    document.addEventListener("DOMContentLoaded", function () {
        // Mendapatkan elemen input tanggal berdasarkan ID
        var tanggalInput = document.getElementById("tgl_akhir");

        // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
        var today = new Date().toISOString().slice(0, 10);

        // Mengatur nilai input tanggal ke tanggal hari ini
        tanggalInput.value = today;
    });
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#DataFormPencarian').submit(function(e) {
    e.preventDefault();
    var formDataPencarian = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('barang-keluar-nota-oprs-khusus-lap')}}",
        data: formDataPencarian,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            var tgl_awal = data.tgl_awal;
            var tgl_akhir = data.tgl_akhir;
            var link = "/barang-keluar-nota-oprs-khusus-lap-print/" + encodeURIComponent(tgl_awal) + "/" + encodeURIComponent(tgl_akhir);
            var newTab = window.open(link, '_blank');

             if (!newTab || newTab.closed || typeof newTab.closed == 'undefined') {
                alert("Pemblokir pop-up mencegah pembukaan tab baru. Harap izinkan pop-up untuk situs ini.");
            }
            
        },
        error: function(data){
            console.log(data);
        }
    });
});
</script>
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
        ajax: "{{ url('barang-keluar-nota-oprs-khusus') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_bkprs', name: 'nm_bkprs' },
            { data: 'no_bkrsn', name: 'no_bkrsn' },
            {
                data: 'tgl_bkrsn',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },   
            {
                data: 'status_bkrsn',
                name: 'status_bkrsn',
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
                    if (row.status_bkrsn == 0) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_bkrsn + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_bkrsn + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ' +                    
                    '<a href="javascript:void(0);" onClick="validasiFunc(' + row.id_bkrsn + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" title="Validasi Data" class="delete btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ';
                    }
                    buttons += 
                                
                    '<a href="barang-keluar-oprs-khusus/' + row.id_bkrsn_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> ';
                    
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
    $('#id_bkrsn').val('');
}   
     
function editFunc(id_bkrsn){    
    $.ajax({
        type:"POST",
        url: "{{ url('barang-keluar-nota-oprs-khusus-edit') }}",
        data: { id_bkrsn: id_bkrsn },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_bkrsn').val(res.id_bkrsn);
            $('#penerima').val(res.id_bkprs);
            $('#no_nota').val(res.no_bkrsn);
            $('#tgl_nota').val(res.tgl_bkrsn);
        }
    });
}  
 
function deleteFunc(id_bkrsn){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_bkrsn = id_bkrsn;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-keluar-nota-oprs-khusus-delete') }}",
            data: { id_bkrsn: id_bkrsn },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                showSaveMessage('Data berhasil dihapus');
            }
        });
    }
}

function validasiFunc(id_bkrsn){
    var pesan = "Apakah anda yakin akan validasi data ini ?";
    if (confirm(pesan) == true) {
        var id_bkrsn = id_bkrsn;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-keluar-nota-oprs-khusus-validasi') }}",
            data: { id_bkrsn: id_bkrsn },
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
            },
        });
    }
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('barang-keluar-nota-oprs-khusus-store')}}",
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
                showDangerMessage('Data gagal disimpan. Data nama penerima tidak boleh sama');
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
            else if (data.status === 6)
            {
                showDangerMessage('Maaf, tahun yang anda entrikan di luar tahun anggaran yang anda pilih');
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