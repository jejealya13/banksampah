<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
        </div>
        <div class="card-body">
    <form method="get" action="<?= base_url('laporan/penabungan'); ?>" class="mb-4">
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
                    <a href="<?= base_url('laporan/penabungan'); ?>" class="btn btn-warning">
                        <i class="fa fa-refresh"></i> Refresh
                    </a>
                    <div class="d-flex justify-content-end" style="margin-top: 5px;">
                        <a href="<?= base_url('laporan/export_laporan_pdf/penabungan' . (isset($_GET['start_date']) ? '?start_date=' . $_GET['start_date'] . '&end_date=' . $_GET['end_date'] : '')); ?>" class="btn btn-danger me-2" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> Export PDF
                        </a>
                        <a href="<?= base_url('laporan/export_laporan_excel/penabungan' . (isset($_GET['start_date']) ? '?start_date=' . $_GET['start_date'] . '&end_date=' . $_GET['end_date'] : '')); ?>" class="btn btn-info">
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Data Penabungan</h3>
                </div>
                <div class="box-body">
					<div class="table-responsive"> <!-- Tambahkan div ini -->
                    <table id="example1" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Penabungan</th>
                                <th>ID Nasabah</th>
                                <th>ID Sampah</th>
                                <th>Tanggal</th>
                                <th>Berat</th>
                                <th>Gambar</th>
                                <th>Harga</th>
                                <th>Status</th> </tr>
                        </thead>
                        <tbody>
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $penabungan): ?>
            <tr>
                <td><?= $penabungan->idpenabungan; ?></td>
                <td><?= $penabungan->idnasabah; ?></td>
                <td><?= $penabungan->idsampah; ?></td>
                <td><?= date('d/m/Y', strtotime($penabungan->tanggal)); ?></td>
                <td><?= $penabungan->berat; ?></td>
                <td>
                    <?php if (!empty($penabungan->gambar)): ?>
                        <a href="<?= base_url('uploads/gambar_penabungan/' . $penabungan->gambar); ?>" target="_blank">
                            <img src="<?= base_url('uploads/gambar_penabungan/' . $penabungan->gambar); ?>"
                                alt="Gambar Sampah"
                                width="100"
                                style="cursor: pointer;">
                        </a>
                    <?php else: ?>
                        Tidak ada gambar
                    <?php endif; ?>
                </td>
                <td><?= number_format($penabungan->harga, 0, ',', '.'); ?></td>
                <td><?= $penabungan->status; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="text-center">Data tidak ditemukan.</td>
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
    /* Styling yang sama seperti laporan lain */
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
        background-color: #007bff; /* Warna header untuk penabungan */
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