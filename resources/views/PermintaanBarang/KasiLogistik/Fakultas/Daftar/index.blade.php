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
                        <li class="breadcrumb-item" aria-current="page">Permintaan Barang</li>
                        <li class="breadcrumb-item" aria-current="page">Fakultas</li>
                        <li class="breadcrumb-item active" aria-current="page">Item Barang</li>
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
                    <div class="ribbon ribbon-success">Daftar Permintaan Barang Fakultas Item Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                        </ul>
                </div>

                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                      <div class="box-header">
                        <h4 class="box-title">Rincian</strong></h4>
                      </div>
                      <div class="box-body">
                        <p>Fakultas : {{ $cek_pbf->nm_fk }}</p>
                        <p>Kebutuhan Untuk : {{ $cek_pbf->butuh_pbf }}</p>
                        <p>Tanggal Permintaan : {{ $tgl = \Carbon\Carbon::parse($cek_pbf->tgl_pbf)->locale('id')->isoFormat('D MMMM Y'); }}</p>
                        <p>Status : {{ $cek_pbf->nm_pbs }}</p>
                      </div>
                    </div>
                </div>
                
                <div class="box-body">                  
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                    <table class="table table-bordered" id="data-datatable">
                        <x-judul-tabel>
                            <x-isi-judul-tabel namakolom="No" />
                            <x-isi-judul-tabel namakolom="Kategori" />
                            <x-isi-judul-tabel namakolom="Item" />
                            <x-isi-judul-tabel namakolom="Jumlah Diajukan" />
                            <x-isi-judul-tabel namakolom="Jumlah Disetujui" />
                            <x-isi-judul-tabel namakolom="Status" />
                            <x-isi-judul-tabel namakolom="Keterangan" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/permintaan-barang-kasilogistik-pimf"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar permintaan barang"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Permintaan Barang </button></a>
            </div>
        </div>
    </div>
</section>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permintaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_pbfd" id="id_pbfd">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Kategori</label>
                        <div class="col-sm-8">
                          <input type="text" id="nm_kategori" name="nm_kategori" class="form-control" id="horizontal-firstname-input" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Item</label>
                        <div class="col-sm-8">
                          <input type="text" id="nm_brg" name="nm_brg" class="form-control" id="horizontal-firstname-input" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah Diajukan</label>
                        <div class="col-sm-8">
                          <input type="number" id="jumlah" name="jumlah" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah Disetujui</label>
                        <div class="col-sm-8">
                          <input type="number" id="jumlah_setujui" name="jumlah_setujui" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                          <select id="status_ajuan" name="status_ajuan" class="form-control" required>
                            <option value="0">Belum Diproses</option>
                            <option value="1">Disetujui</option>
                            <option value="2">Tidak Disetujui</option>
                          </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                        <div class="col-sm-8">
                          <input type="text" id="ket" name="ket" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Keterangan">
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
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#data-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        
        ajax: "{{ url('permintaan-barang-kasilogistik-pimf-daftar', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_kategori', name: 'nm_kategori' },
            { data: 'nm_brg', name: 'nm_brg' },
            { 
                data: 'jmlh_ajuan_pbfd', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },
            { 
                data: 'jmlh_setuju_pbfd', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },      
            {
                data: 'status_pbfd',
                name: 'status_pbfd',
                render: function (data, type, row) {
                    if (data === 0) {
                        return 'Belum di Proses';
                    } else if (data === 1) {
                        return 'Disetujui';
                    } else if (data === 2) {
                        return 'Tidak Disetujui';
                    }
                }
            },         
            { data: 'ket_pbfd', name: 'ket_pbfd' },
            {data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    if (row.level_pbs == 2) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_pbfd + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ';
                    }
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
});

function editFunc(id_pbfd){  
    isTambahData = false;  
    $.ajax({
        type:"POST",
        url: "{{ url('permintaan-barang-kasilogistik-pimf-daftar-edit') }}",
        data: { id_pbfd: id_pbfd },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_pbfd').val(res.id_pbfd);
            $('#nm_kategori').val(res.nm_kt);
            $('#nm_brg').val(res.nm_brg);
            $('#jumlah').val(res.jmlh_ajuan_pbfd);
            $('#jumlah_setujui').val(res.jmlh_setuju_pbfd);
            $('#status_ajuan').val(res.status_pbfd);
            $('#ket').val(res.ket_pbfd);
        }
    });    
}  
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('permintaan-barang-kasilogistik-pimf-daftar-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) 
            { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            }
            else
            {

            }          
            
        },
        error: function(data){
            console.log(data);
        }
    });
});
</script>
@endsection