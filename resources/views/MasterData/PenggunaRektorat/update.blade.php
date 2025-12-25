<x-form-update>   
    <x-input type="text" : judulform="Username" : placeholder="Entrikan Username" : name="username" : jenis="warning" /> 
    <x-input type="text" : judulform="Nama" : placeholder="Entrikan Nama" : name="nama" : jenis="warning" />  
    <x-input-select judulform="Jenis Kelamin" : name="jk" : jenis="warning">
        <option value=''>Silahkan Pilih Jenis Kelamin</option>
        <option value="L"> Laki-laki </option>
        <option value="P"> Perempuan </option>
    </x-input-select>  
    <x-input type="text" : judulform="No Wa" : placeholder="Entrikan No Wa" : name="nowa" : jenis="warning" /> 
    <x-input-select judulform="Bagian Rektorat" : name="idUnit" : jenis="warning">
        <option value=''>Silahkan Pilih Bagian Rektorat</option>
        @foreach($daftar_unit as $baris)
        <option value={{ $baris->id_ur }}> {{ $baris->nm_ur }}</option>
        @endforeach
    </x-input-select>
    
    <x-input-select judulform="Jabatan Bagian Rektorat" : name="idUnitJabatan" : jenis="warning">
        <option value=''>Silahkan Pilih Jabatan di Bagian Rektorat</option>
        @if (!is_null($cariUnitJabatan))
            @foreach($cariUnitJabatan as $baris)   
                @if($baris->role_id==5)
                <option value={{ $baris->id_urj }}> Operator </option>
                @elseif($baris->role_id==6)
                <option value={{ $baris->id_urj }}> Pimpinan </option>
                @endif
            @endforeach
        @else
            @foreach($daftar_unit_jabatan as $baris)
                @if($baris->role_id==5)
                <option value={{ $baris->id_urj }}> Operator </option>
                @elseif($baris->role_id==6)
                <option value={{ $baris->id_urj }}> Pimpinan </option>
                @endif
            @endforeach
        @endif     
    </x-input-select>     
</x-form-update>