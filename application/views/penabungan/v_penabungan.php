<div class="row">
    <div class="col-md-12">
    <a href="<?= base_url('penabungan/tambah'); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Penabungan</a>
        </div>
</div>
<br>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Data Tabel Penabungan</h3>
    </div>
    <div class="box-body">
		<div class="table-responsive"> <!-- Tambahkan div ini -->
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Penabungan</th>
                    <th>ID Nasabah</th>
                    <th>ID Sampah</th>
                    <th>Tanggal</th>
                    <th>Berat</th>
                    <th>Gambar</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- <?php if (empty($data)): ?>
                    <tr><td colspan="9" class="text-center">Tidak ada data transaksi penabungan.</td></tr>
                <?php else: ?> -->
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo isset($row['idpenabungan']) ? $row['idpenabungan'] : ''; ?></td>
                            <td><?php echo isset($row['idnasabah']) ? $row['idnasabah'] : ''; ?></td>
                            <td><?php echo isset($row['idsampah']) ? $row['idsampah'] : ''; ?></td>
                            <td><?php echo isset($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : ''; ?></td>
                            <td><?php echo isset($row['berat']) ? $row['berat'] : ''; ?></td>
                            <td>
                                <?php if (isset($row['gambar']) && $row['gambar']): ?>
                                <a href="<?php echo base_url('uploads/gambar_penabungan/' . $row['gambar']); ?>" target="_blank">
                                    <img src="<?php echo base_url('uploads/gambar_penabungan/' . $row['gambar']); ?>"
                                    alt="Gambar Sampah"
                                    width="100"
                                    style="cursor: pointer;"> </a>
                                <?php else: ?>
                                    Tidak ada gambar
                                <?php endif; ?>
                            </td>

                           <!-- <td>
                                <?php if (isset($row['gambar']) && $row['gambar']): ?>
                                    <img src="<?php echo base_url('uploads/gambar_penabungan/' . $row['gambar']); ?>" alt="Gambar Sampah" width="50">
                                <?php else: ?>
                                    Tidak ada gambar
                                <?php endif; ?>
                            </td> -->
                            <td><?php echo isset($row['harga']) ? number_format($row['harga'], 0, ',', '.') : ''; ?></td>
                            <td>
                                <?php
                                $status = isset($row['status']) ? $row['status'] : '';
                                if ($status == 'Belum Diverifikasi'): ?>
                                    <a href="<?php echo site_url('penabungan/verifikasi_transaksi/' . $row['idpenabungan']); ?>" class="btn btn-sm btn-success">Verifikasi</a>
                                    <a href="<?php echo site_url('penabungan/batal_verifikasi/' . $row['idpenabungan']); ?>" class="btn btn-sm btn-danger">Tolak</a>
                                <?php elseif ($status == 'Terverifikasi'): ?>
                                    <span class="badge bg-success">Terverifikasi</span>
                                    <?php elseif ($status == 'Ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Diketahui</span>
                                <?php endif; ?>
                            </td>
                            <td>
                               <?php
                                // Perubahan di sini: Tombol membeku jika status Terverifikasi ATAU Ditolak
                                $is_disabled_aksi = (isset($row['status']) && ($row['status'] == 'Terverifikasi' || $row['status'] == 'Ditolak'));
                                ?>
                                <a href="<?= base_url('penabungan/edit/' . $row['idpenabungan']); ?>" class="btn btn-success btn-xs <?= $is_disabled_aksi ? 'disabled' : ''; ?>" <?= $is_disabled_aksi ? 'onclick="return false;"' : ''; ?>>Edit</a>
                                <a href="<?= base_url('penabungan/hapus/' . $row['idpenabungan']); ?>" class="btn btn-danger btn-xs <?= $is_disabled_aksi ? 'disabled' : ''; ?>" onclick="return <?= $is_disabled_aksi ? 'false' : 'confirm(\'Yakin ingin menghapus transaksi ini?\')'; ?>;">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- <script>
    $(document).ready( function () {
        $('#example1').DataTable();
    } );
</script>  -->

<script>
    $(document).ready( function () {
        $('#example1').DataTable({
            "order": [[ 0, "desc" ]] // <--- Ubah ini: indeks 0 (ID Penabungan), urutan descending
            // Baris "columnDefs" untuk tanggal tidak diperlukan lagi jika tidak mengurutkan berdasarkan tanggal
            // "columnDefs": [ {
            //     "targets": 3,
            //     "type": "date-eu"
            // }]
        });
    } );
</script>