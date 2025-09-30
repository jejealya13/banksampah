<?php
if (!empty($this->session->flashdata('info'))) {?>
<div class="alert alert-success" role="alert"><?= $this->session->flashdata('info'); ?></div>
<?php }
?>

<div class="row">
    <div class="col-md-12">
        <a href="<?= base_url() ?>sampah/tambah_sampah" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Sampah</a>
    </div>
</div>

<br>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Data Tabel Sampah</h3>
    </div>
    <div class="box-body">
		<div class="table-responsive"> <!-- Tambahkan div ini -->
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Id Sampah</th>
                    <th>Jenis</th>
                    <th>Gambar</th>
                    <th>Berat/Satuan</th>
                    <th>Harga (per satuan)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) {?>
                <tr>
                    <td><?= $row->idsampah; ?></td>
                    <td><?= $row->jenis; ?></td>
                    <td>
                        <?php if (isset($row->gambar) && $row->gambar): ?>
                            <a href="<?php echo base_url('uploads/' . $row->gambar); ?>" target="_blank">
                                <img src="<?php echo base_url('uploads/' . $row->gambar); ?>"
                                     alt="Gambar Sampah"
                                     width="100"
                                     style="cursor: pointer;">
                            </a>
                        <?php else: ?>
                            Tidak ada gambar
                        <?php endif; ?>
                    </td>
                    <td><?= $row->berat; ?></td>
                    <td><?= 'Rp ' . number_format($row->harga, 0, ',', '.') . '/' . strtolower(trim(str_replace('Per ', '', $row->berat))); ?></td>
                    <td>
                        <a href="<?= base_url() ?>sampah/edit/<?= $row->idsampah; ?>" class="btn btn-success btn-xs">Edit</a>
                        <a href="<?= base_url() ?>sampah/hapus/<?= $row->idsampah; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus ?');">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>