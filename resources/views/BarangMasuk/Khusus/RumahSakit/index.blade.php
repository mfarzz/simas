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
                    <div class="ribbon ribbon-success">Daftar Barang Masuk Item Barang
                    </div>                    
                        <ul class="box-controls pull-right">
                            @if($cek_bmrsp->status_bmrsp==0)
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
                        <p>Nomor Pesanan : {{ $cek_bmrsp->no_bmrsp }}</p>
                        <p>Tanggal Pesanan : {{ $tgl = \Carbon\Carbon::parse($cek_bmrsp->tgl_bmrsp)->locale('id')->isoFormat('D MMMM Y'); }}</p>
                        <p>Nilai Pesanan : {{ 'Rp ' . number_format($cek_bmrsp->nilai_bmrsp, 0, ',', '.') }}</p>
                        <p id="totalNilaiItemBarang">Total Nilai Item Barang : {{ 'Rp ' . number_format($total_hrg, 0, ',', '.') }}</p>
                        <p>Status : @if($cek_bmrsp->status_bmrsp==0) Draf @elseif($cek_bmrsp->status_bmrsp == 1)Kirim @endif</p>
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
                            <x-isi-judul-tabel namakolom="Jumlah" />
                            <x-isi-judul-tabel namakolom="Harga" />
                            <x-isi-judul-tabel namakolom="Total Harga" />
                            <x-isi-judul-tabel namakolom="Tanggal Perolehan" />
                            <x-isi-judul-tabel namakolom="" />
                        </x-judul-tabel>
                    </table>
                    </div>
                </div>
                <a href="/barang-masuk-pesanan-oprs-khusus"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar barang keluar nota"><button type="button" class="btn btn-success waves-effect btn-label waves-light"><i class="fa fa-mail-reply"></i> Kembali Ke Daftar Barang Masuk Pesanan </button></a>
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
                    <input type="hidden" name="id_bmrs" id="id_bmrs">
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
                                    <option value={{ $baris->kd_brg }}> {{ $baris->ket }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah</label>
                        <div class="col-sm-8">
                          <input type="number" id="jumlah" name="jumlah" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Harga</label>
                        <div class="col-sm-8">
                            <input type="text" id="harga2" name="harga2" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Harga" required>
                            <input type="hidden" id="harga" name="harga" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Harga">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Perolehan</label>
                        <div class="col-sm-8">
                          <input type="date" id="tgl_perolehan" name="tgl_perolehan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Perolehan" required>
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
        var Kat = $(this).val();    
        if(Kat){
            $.ajax({
            type:"GET",
            url:"/barang-masuk-oprs-khusus-item?item="+Kat,
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

    function formatRupiah2(value) {
        // Pisahkan bagian desimal jika ada
        var parts = value.split(",");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");  // Format ribuan pada bagian utama
        return parts.join(",");  // Gabungkan kembali bagian utama dan desimal
    }

    $('#harga2').keyup(function(){        
        var value = $(this).val();
        value = value.replace(/\./g, '');  // Menghapus titik sebagai pemisah ribuan sementara
        value = value.replace(/,/g, '.');  // Mengubah koma menjadi titik untuk disimpan sebagai angka desimal

        // Format angka untuk ditampilkan dengan pemisah ribuan (koma untuk desimal di tampilan)
        var formattedValue = formatRupiah2(value.replace('.', ','));
        $(this).val(formattedValue);

        // Menyimpan nilai asli (tanpa titik ribuan, koma desimal diubah menjadi titik) untuk dikirim ke server
        $('#harga').val(value);
    });
    
    $('#data-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        
        ajax: "{{ url('barang-masuk-oprs-khusus', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },            
            { data: 'nm_kategori', name: 'nm_kategori' },
            { data: 'nm_brg', name: 'nm_brg' },
            { 
                data: 'jmlh_brg', 
                "render": function(data) {
                    return '<div class="centered-cell">' + data + '</div>';
                }
            },    
            { 
                data: 'hrg_brg',
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
            {
                data: 'tglperolehan_bmrs',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            {data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    if (row.status_bmrsp == 0) {
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.id_bmrs + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-pencil fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc(' + row.id_bmrs + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
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
    $('#id_bmrs').val('');
}   

function editFunc(id_bmrs){  
    isTambahData = false;  
    $.ajax({
        type:"POST",
        url: "{{ url('barang-masuk-oprs-khusus-edit') }}",
        data: { id_bmrs: id_bmrs },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#id_bmrs').val(res.id_bmrs);
            $('#kategori').val(res.kd_kl).trigger('change');
            $('#idBarang').val(res.kd_brg).trigger('change');
            $('#id_bmrs').val(res.id_bmrs);
            $('#jumlah').val(res.jmlh_awal_bmrs);
            $('#harga').val(res.hrg_bmrs);
            $('#tgl_perolehan').val(res.tglperolehan_bmrs);

            $.ajax({
                type: "GET",
                url: "/barang-masuk-oprs-khusus-item?item=" + res.kd_kt,
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

function deleteFunc(id_bmrs){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var id_bmrs = id_bmrs;
        $.ajax({
            type:"POST",
            url: "{{ url('barang-masuk-oprs-khusus-delete') }}",
            data: { id_bmrs : id_bmrs },
            dataType: 'json',
            success: function(res){    
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                if (res.status === 1) 
                { 
                    showSaveMessage('Data berhasil dihapus');
                }
                else if (res.status === 2) 
                {   
                    showDangerMessage('Data gagal dihapus, karena data barang masuk ini sudah digunakan pada barang keluar');
                }
                else if (res.status === 3) {
                    showDangerMessage('Maaf, tanggal perolehan yang anda hapus lebih kecil dari data tanggal perolehan yang sudah ada pada barang masuk');
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
        url: "{{ url('barang-masuk-oprs-khusus-store')}}",
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
            else if (data.status === 0)
            {
                showDangerMessage('Maaf, jumlah yang anda entrikan melebihi jumlah stok yang tersedia');
            }
            else if (data.status === 2)
            {
                showDangerMessage('Maaf, tanggal perolehan yang anda entrikan lebih kecil dari data tanggal perolehan yang sudah ada pada barang masuk');
            }
            else if (data.status === 4)
            {
                showDangerMessage('Maaf, item barang sudah anda entrikan dalam nomor pesanan yang sama');
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