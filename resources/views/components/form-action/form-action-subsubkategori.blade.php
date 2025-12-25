<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc({{ $id }})" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect">
    <i class="fa fa-pencil fa-sm"></i>
</a>

<a href="javascript:void(0);" onClick="deleteFunc({{ $id }})" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect">
    <i class="fa fa-trash fa-sm"></i>
</a>
@php $encript = Crypt::encryptString($id); @endphp 
<a href="/master-kategoribarang/{{ $encript }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Sub Kategori" class="btn btn-rounded btn-sm btn-success">
    <i class="fa fa-book"></i>
</a>
