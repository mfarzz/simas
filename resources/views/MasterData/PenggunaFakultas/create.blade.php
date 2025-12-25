<x-form-input>
    <x-input type="text" : judulform="Username" : placeholder="Entrikan Username" : name="username" : jenis="warning" /> 
    <x-input type="text" : judulform="Nama" : placeholder="Entrikan Nama" : name="nama" : jenis="warning" />  
    <x-input-select judulform="Jenis Kelamin" : name="jk" : jenis="warning">
        <option value=''>Silahkan Pilih Jenis Kelamin</option>
        <option value="L"> Laki-laki </option>
        <option value="P"> Perempuan </option>
    </x-input-select>  
    <x-input type="text" : judulform="No Wa" : placeholder="Entrikan No Wa" : name="nowa" : jenis="warning" /> 
    <x-input-select judulform="Bagian Fakultas" : name="idFakultas" : jenis="warning">
        <option value=''>Silahkan Pilih Fakultas</option>
        @foreach($daftar_fakultas as $baris)
        <option value={{ $baris->id_fk }}> {{ $baris->nm_fk }}</option>
        @endforeach
    </x-input-select>
    @if (!is_null($cariFakultasJabatan))
    <x-input-select judulform="Jabatan Pada Fakultas" : name="idFakultasJabatan" : jenis="warning">
        <option value=''>Silahkan Pilih Jabatan di Fakultas</option>
        @foreach($cariFakultasJabatan as $baris)   
            @if($baris->role_id==7)
            <option value={{ $baris->id_fkj }}> Operator </option>
            @elseif($baris->role_id==8)
            <option value={{ $baris->id_fkj }}> Pimpinan </option>
            @endif
        @endforeach
    </x-input-select>     
    @endif
</x-form-input>