<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $judul; ?></h3>
            </div>
            <form role="form" action="<?= base_url('penabungan/update'); ?>" method="post" enctype="multipart/form-data">
                <div class="box-body">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="idpenabungan">ID Penabungan</label>
                        <input type="text" class="form-control" id="idpenabungan" name="idpenabungan" value="<?= $penabungan->idpenabungan; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="idnasabah">ID Nasabah</label>
                        <select class="form-control select2" id="idnasabah" name="idnasabah" style="width: 100%;">
                            <option value="">-- Pilih Nasabah --</option>
                            <?php if (!empty($data_nasabah)): ?>
                                <?php foreach ($data_nasabah as $nasabah): ?>
                                    <option value="<?= $nasabah->idnasabah; ?>" <?= ($nasabah->idnasabah == $penabungan->idnasabah) ? 'selected' : ''; ?>><?= $nasabah->idnasabah . ' - ' . $nasabah->username; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="idsampah">Jenis Sampah</label>
                        <select class="form-control select2" id="idsampah" name="idsampah" style="width: 100%;">
                            <option value="">-- Pilih Jenis Sampah --</option>
                            <?php if (!empty($data_sampah)): ?>
                                <?php foreach ($data_sampah as $sampah): ?>
                                    <option value="<?= $sampah->idsampah; ?>" data-harga="<?= $sampah->harga; ?>" <?= ($sampah->idsampah == $penabungan->idsampah) ? 'selected' : ''; ?>><?= $sampah->idsampah . ' - ' . $sampah->jenis . ' (Rp ' . number_format($sampah->harga, 0, ',', '.') . '/' . strtolower(str_replace('Per ', '', $sampah->berat)) . ')'; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar (Opsional)</label>
                        <?php if ($penabungan->gambar) : ?>
                            <img src="<?= base_url('uploads/gambar_penabungan/' . $penabungan->gambar); ?>" style="width: 100px; height: auto; margin-bottom: 10px;">
                        <?php else : ?>
                            <p>Tidak ada gambar.</p>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <p class="help-block">Pilih gambar baru jika ingin mengganti.</p>
                    </div>

                    <div class="form-group">
                        <label for="berat">Berat/Jumlah</label>
                        <input type="text" class="form-control" id="berat" name="berat" value="<?= $penabungan->berat; ?>">
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga Total</label>
                        <input type="text" class="form-control" id="harga" name="harga" value="<?= $penabungan->harga; ?>" readonly>
                    </div>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?= base_url('penabungan'); ?>" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#idsampah').change(function() {
            var hargaSatuan = $(this).find(':selected').data('harga');
            var berat = $('#berat').val();
            var totalHarga = berat * hargaSatuan;
            $('#harga').val(totalHarga);
        });

        $('#berat').keyup(function() {
            var hargaSatuan = $('#idsampah').find(':selected').data('harga');
            var berat = $(this).val();
            var totalHarga = berat * hargaSatuan;
            $('#harga').val(totalHarga);
        });
    });
</script>