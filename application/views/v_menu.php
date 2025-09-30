<aside class="main-sidebar">
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?= base_url()?>assets/dist/img/avatar5.png" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>Admin Bank Sampah</p>
        <p style="margin: 0;">
          <span class="fa fa-circle" style="color: #28a745; font-size: 10px;"></span> Online
        </p>
      </div>
    </div>

    <style>
      .sidebar {
        background-color: #051cf1 !important; /* Mengubah warna background sidebar */
        height: auto; /* Mengatur tinggi sidebar */
        overflow-y: auto; /* Scrollbar muncul jika konten melebihi tinggi */
      }
      .sidebar-menu > li.header {
        background-color: #051cf1 !important; /* Mengubah warna header menu */
        color: #FFFFFF; /* Mengubah warna teks header menu */
      }
      .sidebar-menu > li.active > a {
        background-color: #051cf1 !important; /* Mengubah warna menu aktif */
        color: #FFFFFF; /* Mengubah warna font menu aktif */
      }
      .treeview-menu {
        background-color: #051cf1 !important; /* Menetapkan warna background submenu */
        padding-left: 0; /* Menghapus padding kiri */
        border: none; /* Menghilangkan border pada treeview */
        display: none; /* Sembunyikan submenu secara default */
      }
      .treeview.active .treeview-menu {
        display: block; /* Tampilkan submenu saat treeview aktif */
      }
      .treeview-menu > li > a {
        background-color: #051cf1 !important; /* Ubah warna latar belakang submenu */
        color:rgb(224, 218, 218) !important; /* Ubah warna teks submenu menjadi hitam */
        border: none; /* Menghilangkan border */
        outline: none; /* Menghilangkan outline */
        padding-left: 30px; /* Menambahkan padding kiri untuk menyesuaikan */
        transition: color 0.3s ease; /* Transisi halus untuk perubahan warna submenu */
      }
      .treeview-menu > li > a:hover {
        background-color:  #051cf1 !important; /* Ubah warna saat hover pada submenu */
        color: #000000; /* Ubah warna teks saat hover pada submenu */
      }
      .sidebar-menu > li > a {
        background-color: #051cf1 !important; /* Mengubah warna latar belakang menu */
        color: #FFFFFF; /* Mengubah warna teks menu */
        transition: color 0.3s ease;
      }
      .sidebar-menu > li:hover > a {
        background-color: #051cf1 !important; /* Mengubah warna saat hover pada menu */
        color: #FFFFFF; /* Mengubah warna teks saat hover */
      }
      .sidebar-menu > li.active {
        border-left: none !important; /* Menghilangkan border kiri pada menu aktif */
      }
      .sidebar-menu > li.active > a {
        border-left: none !important; /* Menghilangkan border kiri pada link menu aktif */
        outline: none !important; /* Menghilangkan outline pada link menu aktif */
      }
      .sidebar-menu {
        list-style: none; /* Menghapus bullet points */
        padding: 0; /* Menghapus padding default */
      }
      .sidebar-menu > li:hover {
        background-color: rgba(255, 255, 255, 0.2); /* Warna latar belakang saat hover */
        cursor: pointer; /* Menunjukkan bahwa item dapat diklik */
      }
      hr {
        display: none; /* Menghilangkan garis horizontal */
      }
      .treeview-menu > li:hover > a {
        color: #FFFFFF !important; /* Mengubah warna teks submenu menjadi putih terang saat hover */
      }

    </style>

    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li class="active">
        <a href="<?= base_url()?>dashboard">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      <li><a href="<?= base_url()?>nasabah"><i class="fa fa-user"></i> Data Nasabah</a></li>
      <li><a href="<?= base_url()?>sampah"><i class="fa fa-leaf"></i> Data Sampah</a></li>

      <li>
        <a href="<?= base_url()?>jadwal_pengantaran">
          <i class="fa fa-calendar"></i> <span>Jadwal Pengantaran</span>
        </a>
      </li>
      
      <li class="treeview">
        <a href="#">
          <i class="fa fa-money"></i>
          <span>Transaksi</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="<?= base_url()?>penabungan"><i class="fa fa-circle-o"></i> Transaksi Penabungan</a></li>
          <li><a href="<?= base_url()?>penarikan"><i class="fa fa-circle-o"></i> Transaksi Penarikan</a></li>
        </ul>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-file-text"></i>
          <span>Laporan</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="<?= base_url()?>laporan"><i class="fa fa-circle-o"></i> Laporan Bank Sampah</a></li>
        </ul>
      </li>

      <li><a href="<?= base_url()?>log_aktivitas"><i class="fa fa-book"></i> Log Aktivitas</a></li>
      <li><a href="<?= base_url()?>login/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>