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
                        <li class="breadcrumb-item" aria-current="page">Barang Masuk</li>
                        <li class="breadcrumb-item active" aria-current="page">Pesanan</li>
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
                    <div class="ribbon ribbon-success">Daftar Pesanan Barang Masuk
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
                            <x-isi-judul-tabel namakolom="No SP2D" />
                            <x-isi-judul-tabel namakolom="MAK" />
                            <x-isi-judul-tabel namakolom="No Kontrak/Kuitansi" />
                            <x-isi-judul-tabel namakolom="Tanggal Kontrak" />
                            <x-isi-judul-tabel namakolom="Nilai Pesanan" />
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
                <h5 class="modal-title">Barang Masuk Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_bmrp" id="id_bmrp">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No SP2D</label>
                        <div class="col-sm-8">
                          <input type="text" id="no_sp2d" name="no_sp2d" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nomor SP2D" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">MAK</label>
                        <div class="col-sm-8">
                            <select id="kd_mak" name="kd_mak" class="form-select form-control" aria-label="Default select example" required>
                                <option value=''>---Silahkan Pilih MAK---</option>
                                @foreach ($daftar_mata_anggaran as $baris )
                                    <option value={{ $baris->kd_mak }}> {{ $baris->kd_mak }} - {{ $baris->nm_mak }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No Kontrak/Kuitansi</label>
                        <div class="col-sm-8">
                          <input type="text" id="no_pesanan" name="no_pesanan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nomor Pesanan" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Kontrak</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl_pesanan" name="tgl_pesanan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal pesanan" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nilai Pesanan</label>
                        <div class="col-sm-8">
                            <input type="text" id="nilai_pesanan2" name="nilai_pesanan2" class="form-control" placeholder="Entrikan Nilai Pesanan" required>
                            <input type="hidden" id="nilai_pesanan" name="nilai_pesanan" class="form-control" placeholder="Entrikan Nilai Pesanan">
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

    function formatRupiah2(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $('#nilai_pesanan2').keyup(function(){        
        var value = $(this).val();
        value = value.replace(/\./g, '');
        var formattedValue = formatRupiah2(value);
        $(this).val(formattedValue);
        $('#nilai_pesanan').val(value);
    });

    $('#DataFormPencarian').submit(function(e) {
    e.preventDefault();
    var formDataPencarian = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('barang-masuk-pesanan-opr-khusus-lap')}}",
        data: formDataPencarian,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            var tgl_awal = data.tgl_awal;
            var tgl_akhir = data.tgl_akhir;
            var link = "/barang-masuk-pesanan-opr-khusus-lap-print/" + encodeURIComponent(tgl_awal) + "/" + encodeURIComponent(tgl_akhir);
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
        ajax: "{{ url('barang-masuk-pesanan-opr-khusus') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_sp2d', name: 'no_sp2d' },
            { data: 'nm_mak', name: 'nm_mak' },
            { data: 'no_bmrp', name: 'no_bmrp' },
            {
                data: 'tgl_bmrp',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },   
            {
                data: 'nilai_bmrp',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            {
                data: 'status_bmrp',
                name: 'status_bmrp',
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
                    if (row.status_bmrp == 0) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_bmrp + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_bmrp + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ' +                    
                    '<a href="javascript:void(0);" onClick="validasiFunc(' + row.id_bmrp + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" title="Validasi Data" class="delete btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ';
                    }
                    buttons +=  
                    '<a href="barang-masuk-opr-khusus/' + row.id_bmrp_en + '" data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book fa-sm"></i></a> ';
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
    $('#id_bmrp').val('');
}   
     
function editFunc(id_bmrp){    
    $.ajax({
        type:"POST",
        url: "{{ url('barang-masuk-pesanan-opr-khusus-edit') }}",
        data: { id_bmrp: id_bmrp },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_bmrp').val(res.id_bmrp);  
            $('#no_sp2d').val(res.no_sp2d);
            $('#kd_mak').val(res.kd_mak);          
            $('#no_pesanan').val(res.no_bmrp);
            $('#tgl_pesanan').val(res.tgl_bmrp);
            $('#nilai_pesanan').val(res.nilai_bmrp);
            $('#nilai_pesanan2').val(formatRupiah2(res.nilai_bmrp));
        }
    });
}  
 
function deleteFunc(id_bmrp){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_bmrp = id_bmrp;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-masuk-pesanan-opr-khusus-delete') }}",
            data: { id_bmrp: id_bmrp },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                showSaveMessage('Data berhasil dihapus');
            }
        });
    }
}

function validasiFunc(id_bmrp){
    var pesan = "Apakah anda yakin akan validasi data ini ?";
    if (confirm(pesan) == true) {
        var id_bmrp = id_bmrp;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-masuk-pesanan-opr-khusus-validasi') }}",
            data: { id_bmrp: id_bmrp },
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
                    showDangerMessage('Data gagal divalidasi. Nilai Pesanan tidak sama dengan total nilai barang masuk');
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
        url: "{{ url('barang-masuk-pesanan-opr-khusus-store')}}",
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
                showDangerMessage('Data gagal disimpan. Data nomor kontrak dan no sp2d tidak boleh sama');
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
</script>
@endsection