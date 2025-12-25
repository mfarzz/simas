<x-form-input>    
    <x-input-select judulform="Nama Aset" : name="idAset" : jenis="warning">
        <option value=''>Silahkan Pilih Aset</option>
        @foreach($daftar_aset as $baris)
        <option value={{ $baris->id_refasetep }}> {{ $baris->nm_refasetep }}</option>
        @endforeach
    </x-input-select>
    <x-input type="text" : judulform="Nama Aset Tetap" : placeholder="Entrikan Nama Aset Tetap" : name="nama" : jenis="warning" />
</x-form-input>