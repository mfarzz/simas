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
                    <div class="ribbon ribbon-success">Daftar Permintaan Barang Item Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                            @if($cek_pbr->untuk_pbs==2 and $cek_pbr->level_pbs==1)
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
                        <p>Kebutuhan Untuk : {{ $cek_pbr->butuh_pbr }}</p>
                        <p>Tanggal Permintaan : {{ $tgl = \Carbon\Carbon::parse($cek_pbr->tgl_pbr)->locale('id')->isoFormat('D MMMM Y'); }}</p>
                        <p>Status : {{ $cek_pbr->nm_pbs }}</p>
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
                            <x-isi-judul-tabel namakolom="Keterangan" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/permintaan-barang-pimr"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar permintaan barang"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Permintaan Barang </button></a>
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
                    <input type="hidden" name="id_pbrd" id="id_pbrd">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Kategori</label>
                        <div class="col-sm-8">
                            <select id="kategori" name="idKategori" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Kategori---</option>
                                @foreach ($daftar_kategori as $baris )
                                    <option value={{ $baris->kd_kt }}> {{ $baris->kd_kt }} - {{ $baris->nm_kt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Item</label>
                        <div class="col-sm-8">
                            <select id="idItem" name="idItem" class="form-select form-control">
                                <option value=''>---Silahkan Pilih Item---</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Item Barang Keseluruhan</label>
                        <div class="col-sm-8">
                            <select id="idBarang" name="idBarang" class="form-select form-control" aria-label="Default select example" >
                                <option value=''>---Silahkan Pilih Item Barang---</option>
                                @foreach ($daftar_barang as $baris )
                                    <option value={{ $baris->kd_brg }}> {{ $baris->ket }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah Diajukan</label>
                        <div class="col-sm-8">
                          <input type="number" id="jumlah" name="jumlah" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
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
    if(isTambahData){   
        $('#idBarang').val('').trigger('change');
        var kat = $(this).val();    
        if(kat){
            $.ajax({
            type:"GET",
            url:"/permintaan-barang-pimr-daftar-item?item="+kat,
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
        responsive: true,
        processing: true,
        serverSide: true,
        
        ajax: "{{ url('permintaan-barang-pimr-daftar', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nm_kategori', name: 'nm_kategori' },
            { data: 'nm_brg', name: 'nm_brg' },
            { 
                data: 'jmlh_ajuan_pbrd', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },
            { 
                data: 'jmlh_setuju_pbrd', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },               
            { data: 'ket_pbrd', name: 'ket_pbrd' },
            {data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    if (row.level_pbs == 1 && row.status_pbrd == 0) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_pbrd + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_pbrd + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
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
    $('#kategori').val('').trigger('change');
    $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
    $('#idBarang').val('').trigger('change');
    $('#DataForm').trigger("reset");
    $('#DataModal').html("Add Data");
    $('#data-modal').modal('show');
    $('#id_bmr').val('');
}   

function editFunc(id_pbrd){  
    isTambahData = false;  
    $.ajax({
        type:"POST",
        url: "{{ url('permintaan-barang-pimr-daftar-edit') }}",
        data: { id_pbrd: id_pbrd },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_pbrd').val(res.id_pbrd);
            $('#kategori').val(res.kd_kt).trigger('change');
            $('#idBarang').val(res.kd_brg).trigger('change');
            $('#jumlah').val(res.jmlh_ajuan_pbrd);
            $.ajax({
                type: "GET",
                url: "/permintaan-barang-pimr-daftar-item?item=" + res.kd_kt,
                dataType: 'JSON',
                success: function(item) {
                    $("#idItem").empty();
                    $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                    $.each(item, function(ket, kd_brg) {
                        $("#idItem").append('<option value="' + kd_brg + '">' + kd_brg + ' - ' + ket + '</option>');
                    });                    
                    $('#idItem').val(res.kd_brg).trigger('change');
                    isTambahData = true;
                }
            });
        }
    });    
}  

function deleteFunc(id_pbrd){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_pbrd = id_pbrd;
        $.ajax({
            type:"POST",
            url: "{{ url('permintaan-barang-pimr-daftar-delete') }}",
            data: { id_pbrd : id_pbrd },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                if (res.status === 1) 
                { 
                    showSaveMessage('Data berhasil dihapus');
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
        url: "{{ url('permintaan-barang-pimr-daftar-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) 
            { 
                $('#totalNilaiItemBarang').text('Total Nilai Item Barang : ' + data.total_hrg);

                $('#kategori').val('').trigger('change');
                $("#idItem").append("<option value=''>---Silahkan Pilih Item---</option>");
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            }
            else if (data.status === 4)
            {
                showDangerMessage('Maaf, item barang sudah anda entrikan dalam permintaan barang yang sama');
            }
            else if (data.status === 5)
            {
                showDangerMessage('Maaf, item barang atau item barang keseluruhan wajib di isi salah satunya');
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