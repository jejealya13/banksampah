<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
        </div>
        <div class="card-body">
    <form method="get" action="<?= base_url('laporan/penarikan'); ?>" class="mb-4">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="date" name="start_date" class="form-control" required placeholder="Tanggal Mulai">
            </div>
            <div class="col-md-4">
                <input type="date" name="end_date" class="form-control" required placeholder="Tanggal Selesai">
            </div>
            <div class="col-md-4">
                <div class="d-flex">
                    <button type="submit" class="btn btn-info me-2">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="<?= base_url('laporan/penarikan'); ?>" class="btn btn-warning">
                        <i class="fa fa-refresh"></i> Refresh
                    </a>
                    <div class="d-flex justify-content-end" style="margin-top: 5px;">
                        <a href="<?= base_url('laporan/export_laporan_pdf/penarikan' . (isset($_GET['start_date']) ? '?start_date=' . $_GET['start_date'] . '&end_date=' . $_GET['end_date'] : '')); ?>" class="btn btn-danger me-2" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> Export PDF
                        </a>
                        <a href="<?= base_url('laporan/export_laporan_excel/penarikan' . (isset($_GET['start_date']) ? '?start_date=' . $_GET['start_date'] . '&end_date=' . $_GET['end_date'] : '')); ?>" class="btn btn-info">
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Data Penarikan</h3>
                </div>
                <div class="box-body">
					<div class="table-responsive"> <!-- Tambahkan div ini -->
                    <table id="example1" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Penarikan</th>
                                <th>ID Nasabah</th>
                                <th>Username</th>
                                <th>Metode</th>
                                <th>No Rek</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $penarikan): ?>
                                    <tr>
                                        <td><?= $penarikan->idpenarikan; ?></td>
                                        <td><?= $penarikan->idnasabah; ?></td>
                                        <td><?= $penarikan->nama_nasabah; ?></td>
                                        <td><?= $penarikan->metode; ?></td>
                                        <td><?= $penarikan->noRek; ?></td>
                                        <td><?= date('d/m/Y', strtotime($penarikan->tanggal)); ?></td>
                                        <td><?= number_format($penarikan->nominal, 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Data tidak ditemukan.</td>
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
    /* Styling yang sama seperti v_laporan_sampah.php */
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
        background-color: #007bff;; /* Warna header untuk penarikan */
        color: white;
    }

    .box-title {
        margin: 0;
    }

    .text-primary {
        color: #007bff;
    }
</style>

<link rel="stylesheet" href="<?= base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css">