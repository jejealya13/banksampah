<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <!-- <h4 class="m-0">Laporan Sampah</h4> -->
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('laporan/sampah'); ?>" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="number" name="id_min" class="form-control" placeholder="ID Min" required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="id_max" class="form-control" placeholder="ID Max" required>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info me-2">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/sampah'); ?>" class="btn btn-warning">
                                <i class="fa fa-refresh"></i> Refresh
                            </a>
                            <div class="d-flex justify-content-end" style="margin-top: 5px;">
                                <a href="<?= base_url('laporan/export_laporan_pdf/sampah' . (isset($_GET['id_min']) ? '?id_min=' . $_GET['id_min'] . '&id_max=' . $_GET['id_max'] : '')); ?>" class="btn btn-danger me-2" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Export PDF
                                </a>
                                <a href="<?= base_url('laporan/export_laporan_excel/sampah' . (isset($_GET['id_min']) ? '?id_min=' . $_GET['id_min'] . '&id_max=' . $_GET['id_max'] : '')); ?>" class="btn btn-info">
                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Data Sampah</h3>
                </div>
                <div class="box-body">
					<div class="table-responsive"> <!-- Tambahkan div ini -->
                    <table id="example1" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Sampah</th>
                                <th>Jenis</th>
                                <th>Gambar</th> <!-- Kolom untuk gambar -->
                                <th>Berat</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $samp): ?>
                                    <tr>
                                        <td><?= $samp->idsampah; ?></td>
                                        <td><?= $samp->jenis; ?></td>
                                        <td>
                                            <?php if (!empty($samp->gambar)): ?>
                                                <a href="<?= base_url('uploads/' . $samp->gambar); ?>" target="_blank">
                                                    <img src="<?= base_url('uploads/' . $samp->gambar); ?>"
                                                         alt="Gambar Sampah"
                                                         width="100"
                                                         style="cursor: pointer;"> </a>
                                            <?php else: ?>
                                                Tidak ada gambar
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $samp->berat; ?></td>
                                        <td><?= 'Rp ' . number_format($samp->harga, 0, ',', '.') . '/' . strtolower(trim(str_replace('Per ', '', $samp->berat))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .card {
        margin-top: 20px;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .box-header {
        background-color: #007bff;/* Warna header */
        color: white;
    }

    .box-title {
        margin: 0; /* Menghilangkan margin judul */
    }

    .text-primary {
        color: #007bff; /* Warna biru untuk judul filter */
    }
</style>

<!-- Pastikan Anda menambahkan link ke file Font Awesome lokal di bagian <head> -->
<link rel="stylesheet" href="<?= base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css">