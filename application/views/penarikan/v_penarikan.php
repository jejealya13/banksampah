<?php
if (!empty($this->session->flashdata('info'))) { ?>
    <div class="alert alert-success" role="alert"><?= $this->session->flashdata('info'); ?></div>
<?php }
if (!empty($this->session->flashdata('error'))) { ?>
    <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
<?php }
?>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Data Tabel Penarikan</h3>
    </div>
    <div class="box-body">
		<div class="table-responsive"> <!-- Tambahkan div ini -->
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Penarikan</th>
                    <th>ID Nasabah</th>
                    <th>Nama Nasabah</th>
                    <th>Metode</th>
                    <th>No Rek</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nominal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
               <!-- <?php if (empty($data)) : ?>
                    <tr><td colspan="8" class="text-center">Tidak ada pengajuan penarikan.</td></tr>
                <?php else : ?> -->
                    <?php foreach ($data as $row) : ?> 
                        <tr>
                            <td><?= $row->idpenarikan; ?></td>
                            <td><?= $row->idnasabah; ?></td>
                            <td><?= $row->nama_nasabah; ?></td>
                            <td><?= $row->metode; ?></td>
                            <td><?= $row->noRek; ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($row->tanggal)); ?></td>
                            <td><?= number_format($row->nominal, 0, ',', '.'); ?></td>
                            <td>
                                    <?php
                                    $status = isset($row->status) ? $row->status : 'Menunggu Verifikasi'; // Default status jika belum ada di DB
                                    if ($status == 'Menunggu Verifikasi'):
                                    ?>
                                        <a href="<?= site_url('penarikan/verifikasi_penarikan/' . $row->idpenarikan); ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin memverifikasi penarikan ini dan mengurangi saldo nasabah?');">Verifikasi</a>
                                        <a href="<?= site_url('penarikan/tolak_penarikan/' . $row->idpenarikan); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menolak penarikan ini?');">Tolak</a>
                                    <?php elseif ($status == 'Selesai'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php elseif ($status == 'Ditolak'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Diketahui</span>
                                    <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- <script>
    $(function () {
        $('#example1').DataTable();
    });
</script> -->

<script>
    $(document).ready(function() {
        $('#example1').DataTable({
            "order": [[ 0, "desc" ]] // <--- Mengurutkan kolom 'ID Penarikan' (indeks 0) secara descending
            // Hapus atau komentari baris columnDefs dan $.fn.dataTable.moment jika tidak mengurutkan tanggal
            // "columnDefs": [ {
            //     "targets": 5,
            //     "type": "datetime"
            // }]
        });
    });
</script>