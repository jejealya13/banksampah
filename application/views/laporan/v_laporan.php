<div class="container mt-5">
    <!-- <h1 class="text-center mb-4"><?= $judul; ?></h1> -->
    <div class="row justify-content-center">
        <div class="col-md-5 text-center">
            <div class="mb-4">
                <i class="fa fa-recycle fa-4x mb-2" style="color: #28a745;" aria-hidden="true"></i><br>
                <a href="<?= base_url('laporan/sampah'); ?>" class="btn btn-success btn-lg" style="width: 100%;">
                    Laporan Sampah
                </a>
            </div>
        </div>
        <div class="col-md-5 text-center">
            <div class="mb-4">
                <i class="fa fa-user fa-4x mb-2" style="color: #007bff;" aria-hidden="true"></i><br>
                <a href="<?= base_url('laporan/nasabah'); ?>" class="btn btn-primary btn-lg" style="width: 100%;">
                    Laporan Nasabah
                </a>
            </div>
        </div>
        <div class="col-md-5 text-center">
            <div class="mb-4">
                <i class="fa fa-plus-circle fa-4x mb-2" style="color: #ffc107;" aria-hidden="true"></i><br> <!-- Ubah warna menjadi kuning -->
                <a href="<?= base_url('laporan/penabungan'); ?>" class="btn btn-warning btn-lg" style="width: 100%;">
                    Transaksi Penabungan
                </a>
            </div>
        </div>
        <div class="col-md-5 text-center">
            <div class="mb-4">
                <i class="fa fa-minus-circle fa-4x mb-2" style="color: #dc3545;" aria-hidden="true"></i><br>
                <a href="<?= base_url('laporan/penarikan'); ?>" class="btn btn-danger btn-lg" style="width: 100%;">
                    Transaksi Penarikan
                </a>
            </div>
        </div>
    </div>
</div>