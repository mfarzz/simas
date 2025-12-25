<x-form-input>    
    <x-input-select judulform="Kelompok" : name="idKelompok" : jenis="warning">
        <option value=''>Silahkan Pilih Kelompok</option>
        @foreach($daftar_kelompok as $baris)
        <option value={{ $baris->kd_kl }}> {{ $baris->kd_kl }} - {{ $baris->nm_kl }}</option>
        @endforeach
    </x-input-select> 
    <x-input type="text" : judulform="Kode Sub Sub Kategori" : placeholder="Entrikan Kode Sub Sub Kategori" : name="kode" : jenis="warning" />
    <x-input type="text" : judulform="Nama Sub Sub Kategori" : placeholder="Entrikan Nama Sub Sub Kategori" : name="nama" : jenis="warning" />
</x-form-input>