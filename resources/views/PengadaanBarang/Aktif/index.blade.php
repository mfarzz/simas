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
                        <li class="breadcrumb-item active" aria-current="page">Aktif</li>
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
                    <div class="ribbon ribbon-success">Daftar Pengadaan Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                            <a  class="btn btn-primary btn-sm" onClick="add()" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Tambah Data"> <i class="fa fa-plus-square"></i> Tambah Data </a>
                        </ul>
                </div>
                <div class="box-body">                  
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Nama" />
                            <x-isi-judul-tabel namakolom="Tanggal" />
                            <x-isi-judul-tabel namakolom="Status" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kebutuhan Untuk</label>
                        <div class="col-sm-8">
                          <input type="text" id="nama" name="nama" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kebutuhan Untuk" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Pengadaan</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl" name="tgl" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Pengadaan" required>
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

<div class="modal fade" id="data-modal-ajuan" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title-ajuan">Form Ajuan Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataFormAjuan" name="DataFormAjuan" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_ajuan" id="id_ajuan">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status Data</label>
                        <div class="col-sm-8">
                            <select name="status_ajuan" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Status---</option>
                                @foreach ($daftar_status as $baris )
                                    <option value={{ $baris->id }}> {{ $baris->nm_rsp }} {{ $baris->nm_rspu }} {{ $baris->nama_rp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="keterangan" name="keterangan" class="form-control" id="horizontal-firstname-input" placeholder="Keterangan" required>
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

<div class="modal fade" id="data-history-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <h4>Histori permintaan untuk: <span id="nm_pb"></span></h4>
                <table class="table table-bordered">
                    <thead>
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Jabatan" />
                            <x-isi-judul-tabel namakolom="Nama" />
                            <x-isi-judul-tabel namakolom="Tanggal" />
                            <x-isi-judul-tabel namakolom="Status" />
                            <x-isi-judul-tabel namakolom="Keterangan" />                    
                        </x-judul-tabel>
                    </thead>
                    <tbody id="history-table-body">
                    </tbody>
                </table>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script>
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
        ajax: "{{ url('pengadaan-barang-aktif') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_pb', name: 'nm_pb' },
            {
                data: 'tgl_pb',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            { data: 'status_pb', name: 'status_pb' },            
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if ((userRoleId === 2 && row.kondisi_rspu === 1) || userRoleId === row.role_id_proses) {
                        var buttons = '';
                        
                        if (userRoleId === 2 && row.kondisi_rspu === 1) {
                            buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
                        }
                        buttons += '<a href="/pengadaan-barang-aktif-detail/' + row.id_pb_en + '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Daftar Barang Diminta"><button type="button" class="btn btn-rounded btn-sm btn-success"><i class="fa fa-book"></i></button></a> ';
                        
                        if (userRoleId === row.role_id_proses) {
                            if (userRoleId === 2){
                                buttons += '<a href="javascript:void(0)"   onClick="ajuanFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Ajukan Permintaan"><button type="button" data-bs-toggle="modal" data-bs-target=".prosesdataModal"  class="btn btn-dark btn-rounded btn-sm waves-effect"><i class="fa fa-handshake-o fa-sm"></i></button></a> ';
                            }
                            else if (userRoleId === 3 || userRoleId === 4){
                                if(row.jumlah_belum_diproses === 0 || row.jumlah_belum_diproses === "" || row.jumlah_belum_diproses === null){
                                    buttons += '<a href="javascript:void(0)"   onClick="ajuanFunc(' + row.id + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Ajukan Permintaan"><button type="button" data-bs-toggle="modal" data-bs-target=".prosesdataModal"  class="btn btn-dark btn-rounded btn-sm waves-effect"><i class="fa fa-handshake-o fa-sm"></i></button></a> ';
                                }
                                else{
                                    buttons += '(Belum diproses semuanya)';
                                }
                            }
                        }


                        buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="History Data" onClick="historyFunc(' + row.id + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-history fa-sm"></i></a> ';
                        
                        return buttons;
                    } else {
                        return '';
                    }
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
        url: "{{ url('pengadaan-barang-aktif-edit') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id').val(res.id);
            $('#nama').val(res.nm_pb);
            $('#tgl').val(res.tgl_pb);
        }
    });
}  

function ajuanFunc(id) {
    $.ajax({
        type: "POST",
        url: "{{ url('pengadaan-barang-aktif-show') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res) {
            $('.modal-title-ajuan').html("Form Pengajuan Data");
            $('#data-modal-ajuan').modal('show');
            $('#id_ajuan').val(res.id);
            $('#keterangan').val('');
            
            var currentStatusId = res.id_rspu;
            
            $('select[name="status_ajuan"] option').each(function() {
                var optionValue = $(this).val();
                
                if (optionValue == currentStatusId) {
                    $(this).prop('selected', true);
                } else {
                    $(this).prop('selected', false);
                }
            });
        }
    });
}

function historyFunc(id) {
    $.ajax({
        type: "GET",
        url: "{{ url('pengadaan-barang-aktif-history') }}",
        data: { id: id },
        dataType: 'json',
        success: function(res) {
            var historyTableBody = $('#history-table-body');
            historyTableBody.empty();

            var counter = 1;
            res.historyData.forEach(function(historyItem) {
                var roleText = '';
                if (historyItem.role_id === 2) {
                    roleText = 'Pengadaan Barang';
                } else if (historyItem.role_id === 1) {
                    roleText = 'User';
                } else if (historyItem.role_id === 3) {
                    roleText = '';
                } else {
                    roleText = '';
                }
                var createdAt = dayjs(historyItem.created_at).format('D MMMM YYYY');

                var row = '<tr>' +
                    '<td>' + counter + '</td>' +
                    '<td>' + roleText + '</td>' +
                    '<td>' + historyItem.name + '</td>' +                    
                    '<td>' + createdAt + '</td>' +
                    '<td>' + historyItem.nm_rsp + ' ' + historyItem.nm_rspu + ' ' + historyItem.nama_rp + '</td>' + 
                    '<td>' + historyItem.ket_pbh + '</td>' +
                    '</tr>';
                historyTableBody.append(row);
            });
            $('#nm_pb').text(res.nm_pb);
            $('#data-history-modal').modal('show');
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function deleteFunc(id){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id = id;
        $.ajax({
            type:"POST",
            url: "{{ url('pengadaan-barang-aktif-delete') }}",
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
        url: "{{ url('pengadaan-barang-aktif-store')}}",
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

$('#DataFormAjuan').submit(function(e) {    
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: "{{ url('pengadaan-barang-aktif-ajuan')}}",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {            
            if (data.status === 1) {
                $("#data-modal-ajuan").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save").attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            } else {
                //showDangerMessage('Data gagal disimpan');
                showDangerMessage(data.status);
            }
        },
        error: function(data) {
            console.log(data);
        }
    });
});
</script>
@endsection