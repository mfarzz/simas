<x-form-histori>
    Histori permintaan untuk <b>{{ $nama }}</b>
    <div class="table-rep-plugin">
        <div class="table-responsive mb-0" data-pattern="priority-columns">
            <table id="tech-companies-1" class="table table-striped">
                <thead>
                <x-judul-tabel>
                    <x-isi-judul-tabel namakolom="No" />
                    <x-isi-judul-tabel namakolom="Unit Kerja" />
                    <x-isi-judul-tabel namakolom="Jabatan" />
                    <x-isi-judul-tabel namakolom="Nama" />
                    <x-isi-judul-tabel namakolom="Tanggal" />
                    <x-isi-judul-tabel namakolom="Status" />
                    <x-isi-judul-tabel namakolom="Keterangan" />                    
                </x-judul-tabel>
                </thead>
                <tbody>
                    @foreach ($tampil_histori as $value)
                    <tr>
                        <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$nomor }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_ur }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nama_rp }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->name }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ \Carbon\Carbon::parse($value->created_at)->format('d M Y')}}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->nm_rsp }} {{ $value->nm_rspu }} {{ $value->nama_rp }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $value->ket_pbrh }}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-form-histori>