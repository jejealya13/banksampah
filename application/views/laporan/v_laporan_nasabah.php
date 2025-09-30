<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('laporan/nasabah'); ?>" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="id_min" class="form-control" placeholder="ID Min">
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="id_max" class="form-control" placeholder="ID Max">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <a href="<?= base_url('laporan/nasabah'); ?>" class="btn btn-warning">
                                    <i class="fa fa-refresh"></i> Refresh
                                </a>
                            </div>
                            <div class="d-flex justify-content-end" style="margin-top: 5px;">
                                <a href="<?= base_url('laporan/export_laporan_pdf/nasabah' . (isset($_GET['id_min']) ? '?id_min=' . $_GET['id_min'] . '&id_max=' . $_GET['id_max'] : '')); ?>" class="btn btn-danger me-2" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Export PDF
                                </a>
                                <a href="<?= base_url('laporan/export_laporan_excel/nasabah' . (isset($_GET['id_min']) ? '?id_min=' . $_GET['id_min'] . '&id_max=' . $_GET['id_max'] : '')); ?>" class="btn btn-info">
                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Data Nasabah</h3>
                </div>
                <div class="box-body">
					<div class="table-responsive"> <!-- Tambahkan div ini -->
                    <table id="example1" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Nasabah</th>
                                <th>Nama</th>
                                <th>No Telp</th>
                                <th>Alamat</th>
                                <th>Tabungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $nas): ?>
                                    <tr>
                                        <td><?= $nas->idnasabah; ?></td>
                                        <td><?= $nas->username; ?></td>
                                        <td><?= $nas->phone; ?></td>
                                        <td><?= $nas->address; ?></td>
                                        <td><?= isset($nas->tabungan) ? number_format($nas->tabungan, 0, ',', '.') : '0'; ?></td>
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
        background-color: #007bff; /* Warna header */
        color: white;
    }

    .box-title {
        margin: 0; /* Menghilangkan margin judul */
    }

    .text-primary {
        color: #007bff; /* Warna biru untuk judul filter */
    }
</style>

<link rel="stylesheet" href="<?= base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css">