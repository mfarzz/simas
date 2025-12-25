<x-form-update>   
    <x-input-select judulform="Level Akses" : name="level" : jenis="warning">
        <option value=''>Silahkan Pilih Level Akses</option>
        @foreach($daftar_level as $baris)
        <option value={{ $baris->id }}> {{ $baris->nama_rp }}</option>
        @endforeach
    </x-input-select>    
    <x-input type="text" : judulform="Nama Jabatan" : placeholder="Entrikan Nama Jabatan" : name="nama" : jenis="warning" />
</x-form-update>