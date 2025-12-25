<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">	
          <div class="multinav">
          <div class="multinav-scroll" style="height: 100%;">	
              <!-- sidebar menu-->
              <ul class="sidebar-menu" data-widget="tree">	
                @if(auth()->user()->role_id == 1)
                <li class="header">Super Admin</li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                    <span>Master Data</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <!--<li><a href="/master-status-untuk"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Status</a></li>!-->
                    <li><a href="/master-unitrektorat"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rektorat</a></li>
                    <li><a href="/master-fakultas"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Fakultas</a></li>
                    <li><a href="/master-unitrumahsakit"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rumah Sakit</a></li>
                    <li><a href="/master-mata-anggaran"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Mata Anggaran</a></li>
                    <li><a href="/master-satuan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Satuan</a></li>
                    <li><a href="/master-kategori"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kategori</a></li>
                    <li><a href="/jabatan-penandatanganan-uu"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jabatan Pengesahan Universitas</a></li>
                    <li><a href="/jabatan-penandatanganan-ur"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jabatan Pengesahan Rektorat</a></li>
                    <li><a href="/jabatan-penandatanganan-fk"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jabatan Pengesahan Fakultas</a></li>
                    <li><a href="/jabatan-penandatanganan-urs"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jabatan Pengesahan Rumah Sakit</a></li>
                    <li><a href="/pejabat-penandatanganan-opuu"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pejabat Penandatanganan Universitas</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                    <span>Pengguna</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/master-pengguna-rektorat"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengguna Rektorat</a></li>
                    <li><a href="/master-pengguna-fakultas"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengguna Fakultas</a></li>
                    <li><a href="/master-pengguna-rumahsakit"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengguna Rumah Sakit</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                    <span>Pengaturan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/pengaturan-backup-data"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Backup Data</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chart-pie"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 2)
                <li class="header">Kepala Seksi Perlengkapan & Logis</li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                    <span>Master Data</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/master-satuan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Satuan</a></li>
                    <li><a href="/master-kelompok"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kelompok</a></li>
                    <li><a href="/master-kategori"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kategori</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                    <span>Pengadaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/pengadaan-barang-aktif"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Aktif</a></li>
                  </ul>
                </li>
                <!--<li class="treeview">
                  <a href="#">
                    <i class="icon-Cart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Masuk</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">                    
                    <li><a href="/barang-masuk-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Masuk Khusus</a></li>                    
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-File"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Keluar</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/barang-keluar-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Keluar Khusus</a></li>
                  </ul>
                </li>-->
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chat-check"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Usang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/barang-usang-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Usang Khusus</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chart-pie"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 4)
                <li class="header">Direktur Aset</li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                    <span>Pengadaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/pengadaan-barang-aktif"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Aktif</a></li>
                    <li><a href="/aset-tetap"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Arsip</a></li>
                  </ul>
                </li>             
                @elseif(auth()->user()->role_id == 5)
                <li class="header">Operator Bagian Rektorat</li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-database"><span class="path1"></span><span class="path2"></span></i>
                    <span>Master Data</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/master-barang-opr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang</a></li>
                    <li><a href="/barang-keluar-penerima-opr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Unit Penerima</a></li>
                    <li><a href="/pejabat-penandatanganan-opr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pejabat Penandatanganan</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-down"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Masuk</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">                    
                    <li><a href="/barang-masuk-pesanan-opr-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Masuk</a></li>                    
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-up"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Keluar</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/barang-keluar-nota-opr-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pemakaian</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-balance-scale"><span class="path1"></span><span class="path2"></span></i>
                    <span>Opname Fisik</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/opsik-opr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pelaksanaan</a></li>                    
                  </ul>
                </li>   
                <!--<li class="treeview">
                  <a href="#">
                    <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                    <span>Permintaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/permintaan-barang-rektorat-aktif"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Permintaan Aktif</a></li>
                  </ul>
                </li>-->
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 6)
                <li class="header">Pimpinan Bagian Rektorat</li>
                <li><a href="/permintaan-barang-pimr"><i class="icon-Cart"><span class="path1"></span><span class="path2"></span></i>Permintaan Barang</a></li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 7)
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-database"><span class="path1"></span><span class="path2"></span></i>
                    <span>Master Data</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/master-barang-opf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang</a></li>
                    <li><a href="/barang-keluar-penerima-opf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Unit Penerima</a></li>
                    <li><a href="/pejabat-penandatanganan-opf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pejabat Penandatanganan</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-down"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Masuk</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">                    
                    <li><a href="/barang-masuk-pesanan-opf-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Masuk</a></li>                    
                  </ul>
                </li>   
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-up"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Keluar</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/barang-keluar-nota-opf-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pemakaian</a></li>                    
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-balance-scale"><span class="path1"></span><span class="path2"></span></i>
                    <span>Opname Fisik</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/opsik-opf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pelaksanaan</a></li>                    
                  </ul>
                </li>   
                <!--<li class="treeview">
                  <a href="#">
                    <i class="fa fa-refresh"><span class="path1"></span><span class="path2"></span></i>
                    <span>Reklafisikasi</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">                    
                    <li><a href="/reklasifikasi-jenisbrg-opf-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jenis Barang</a></li>                    
                  </ul>
                </li>-->
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-opf-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-opf-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>                     
                @elseif(auth()->user()->role_id == 8)
                <li class="header">Pimpinan Fakultas</li>    
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Cart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Permintaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/permintaan-barang-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Sedang Proses</a></li>
                    <li><a href="/permintaan-barang-selesaiproses-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Selesai Proses</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-opf-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-opf-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>  
                @elseif(auth()->user()->role_id == 9)
                <li class="header">Subdit Akuntansi</li>   
                <!--<li class="treeview">
                  <a href="#">
                    <i class="icon-File"><span class="path1"></span><span class="path2"></span></i>
                    <span>Opname Fisik</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/opsik-opu"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pelaksanaan</a></li>                    
                  </ul>
                </li> --> 
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chart-pie"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>    
                    <li><a href="/lap-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>                
                  </ul>
                </li>  
                @elseif(auth()->user()->role_id == 10)
                <li class="header">Operator Bagian Rumah Sakit</li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-database"><span class="path1"></span><span class="path2"></span></i>
                    <span>Master Data</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/master-barang-opr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang</a></li>
                    <li><a href="/barang-keluar-penerima-oprs"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Unit Penerima</a></li>
                    <li><a href="/pejabat-penandatanganan-oprs"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pejabat Penandatanganan</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-down"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Masuk</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">                    
                    <li><a href="/barang-masuk-pesanan-oprs-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Barang Masuk</a></li>                    
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-arrow-circle-up"><span class="path1"></span><span class="path2"></span></i>
                    <span>Barang Keluar</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/barang-keluar-nota-oprs-khusus"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pemakaian</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-balance-scale"><span class="path1"></span><span class="path2"></span></i>
                    <span>Opname Fisik</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/opsik-oprs"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pelaksanaan</a></li>                    
                  </ul>
                </li>   
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-oprs-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-oprs-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>  
                @elseif(auth()->user()->role_id == 12)
                <li class="header">Kasi Logistik</li>    
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-shopping-basket"><span class="path1"></span><span class="path2"></span></i>
                    <span>Permintaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">      
                    <li class="treeview">
                      <a href="#">
                        <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                        <span>Sedang Proses</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-right pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">                    
                        <li><a href="/permintaan-barang-kasilogistik-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Fakultas</a></li>
                        <li><a href="/permintaan-barang-kasilogistik-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rektorat</a></li>
                      </ul>
                    </li> 
                    <li class="treeview">
                      <a href="#">
                        <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                        <span>Selesai Proses</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-right pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">                    
                        <li><a href="/permintaan-barang-kasilogistik-selesaiproses-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Fakultas</a></li>
                        <li><a href="/permintaan-barang-kasilogistik-selesaiproses-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rektorat</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>  
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chart-pie"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 13)
                <li class="header">Pimpinan Unit Rektorat (Kasi)</li>
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Cart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Permintaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/permintaan-barang-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Sedang Proses</a></li>
                    <li><a href="/permintaan-barang-selesaiproses-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Selesai Proses</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-area-chart"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-opr-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>
                @elseif(auth()->user()->role_id == 14)
                <li class="header">Kepala Gudang</li>    
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-shopping-basket"><span class="path1"></span><span class="path2"></span></i>
                    <span>Permintaan Barang</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu"> 
                    <li class="treeview">
                      <a href="#">
                        <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                        <span>Sedang Proses</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-right pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">                    
                        <li><a href="/permintaan-barang-kg-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Fakultas</a></li>
                        <li><a href="/permintaan-barang-kg-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rektorat</a></li>
                      </ul>
                    </li>                   
                    <li class="treeview">
                      <a href="#">
                        <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                        <span>Selesai Proses</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-right pull-right"></i>
                        </span>
                      </a>
                      <ul class="treeview-menu">                    
                        <li><a href="/permintaan-barang-kg-selesaiproses-pimf"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Fakultas</a></li>
                        <li><a href="/permintaan-barang-kg-selesaiproses-pimr"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rektorat</a></li>
                      </ul>
                    </li> 
                  </ul>
                </li>   
                <li class="treeview">
                  <a href="#">
                    <i class="icon-Chart-pie"><span class="path1"></span><span class="path2"></span></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="/lap-posisi-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Posisi Persediaan</a></li>
                    <li><a href="/lap-persediaan"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Persediaan</a></li>
                  </ul>
                </li>      
                @endif
              </ul>
          </div>
        </div>
    </section>
    @if(auth()->user()->pengguna == 2 or auth()->user()->pengguna == 3)
    <div class="sidebar-footer">
      <form action="/beranda-aset" method="post">
        @csrf
        <input type="hidden" name="pil_aplikasi" value="aset">
          <div class="row">
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-success mt-10"><span class="icon-Settings-2"></span>Masuk Sistem Informasi Aset</button>
          </div>
          </div>
      </form>	
    </div>
    @endif
  </aside>