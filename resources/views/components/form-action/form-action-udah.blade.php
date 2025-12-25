@if ($kondisi_rspu == 1)
    <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc({{ $id }})" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect">
        <i class="fa fa-pencil fa-sm"></i>
    </a>

    <a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc({{ $id }})" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect">
        <i class="fa fa-trash fa-sm"></i>
    </a>
@endif
