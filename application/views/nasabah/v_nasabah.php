<?php
if (!empty($this->session->flashdata('success'))) { ?>
    <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
<?php }

if (!empty($this->session->flashdata('error'))) { ?>
    <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
<?php }

if (!empty($this->session->flashdata('info'))) { ?>
    <div class="alert alert-info" role="alert"><?= $this->session->flashdata('info'); ?></div>
<?php }
?>

<div class="row">
    <div class="col-md-12">
        <a href="<?= base_url()?>nasabah/tambah_nasabah" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Nasabah</a>
    </div>
</div>

<br>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Data Tabel Nasabah</h3>
    </div>
    <div class="box-body">
		<div class="table-responsive"> <!-- Tambahkan div ini -->
        <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Id Nasabah</th>
                <th>Nama Nasabah</th>
                <th>Username</th>
                <th>No. Telpon</th>
                <th>Alamat</th>
                <th>Tabungan</th> <th>Info Saldo</th> <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
    <?php
        if (empty($data_nasabah)) {
            echo '<tr><td colspan="7" class="text-center">Tidak ada data nasabah.</td></tr>';
        } else {
            foreach ($data_nasabah as $row) { ?>
                <tr>
                    <td><?= $row->idnasabah; ?></td>
                    <td><?= $row->namanasabah; ?></td>
                    <td><?= $row->username; ?></td>
                    <td><?= $row->phone; ?></td>
                    <td><?= $row->address; ?></td>
                    <td><?= number_format($row->tabungan, 0, ',', '.'); ?></td>
                    <td>
                        <a href="<?= base_url() ?>penabungan/saldo/<?= $row->idnasabah; ?>" class="btn btn-info btn-xs">Tarik Saldo</a>
                    </td>
                    <td>
                        <a href="<?= base_url() ?>nasabah/edit/<?= $row->idnasabah; ?>" class="btn btn-success btn-xs">Edit</a>
                        <a href="<?= base_url() ?>nasabah/hapus/<?= $row->idnasabah; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus ?');">Hapus</a>
                    </td>
                </tr>
    <?php }
        }
    ?>
    </tbody>
    </table>
    </div>
</div>