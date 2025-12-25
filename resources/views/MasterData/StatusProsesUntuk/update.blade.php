<x-form-update>   
    <x-input-select judulform="Status" : name="idStatus" : jenis="warning">
        <option value=''>Silahkan Pilih Status</option>
        @foreach($daftar_status as $baris)
        <option value={{ $baris->id_rsp }}> {{ $baris->nm_rsp }}</option>
        @endforeach
    </x-input-select>
    <x-input-select judulform="Nama" : name="nama" : jenis="warning">
        <option value=''>Silahkan Pilih Nama</option>
        <option value='Oleh'> Oleh </option>
        <option value='Ke'> Ke </option>
    </x-input-select>
    <x-input-select judulform="Level Akses Proses" : name="role_proses" : jenis="warning">
        <option value=''>Silahkan Pilih Level Akses Proses</option>
        @foreach($daftar_level as $baris)
        <option value={{ $baris->id }}> {{ $baris->nama_rp }}</option>
        @endforeach
    </x-input-select>
    <x-input-select judulform="Level Akses Pilihan" : name="role_pilihan" : jenis="warning">
        <option value=''>Silahkan Pilih Level Akses Pilihan</option>
        @foreach($daftar_level as $baris)
        <option value={{ $baris->id }}> {{ $baris->nama_rp }}</option>
        @endforeach
    </x-input-select>
    <x-input-select judulform="Kondisi Data" : name="kondisi" : jenis="warning">
        <option value=''>Silahkan Pilih Kondisi Data</option>
        <option value='0'> Tidak Boleh Edit </option>
        <option value='1'> Boleh Edit </option>        
    </x-input-select>
    <x-input type="number" : judulform="Posisi Proses" : placeholder="Entrikan Posisi Proses" : name="posisi_proses" : jenis="warning" /> 
    <x-input type="number" : judulform="Posisi Pilihan" : placeholder="Entrikan Posisi Pilihan" : name="posisi_pilihan" : jenis="warning" />    
    <x-input-select judulform="Kegiatan" : name="kegiatan" : jenis="warning">
        <option value=''>Silahkan Pilih Kegiatan</option>
        @foreach($daftar_kegiatan as $baris)
        <option value={{ $baris->id_rk }}> {{ $baris->nm_rk }}</option>
        @endforeach
    </x-input-select>
    <x-input-select judulform="Status Data" : name="status_data" : jenis="warning">
        <option value=''>Silahkan Pilih Status Data</option>
        <option value='0'> Belum Selesai </option>
        <option value='1'> Sudah Selesai </option>        
    </x-input-select>
</x-form-update>