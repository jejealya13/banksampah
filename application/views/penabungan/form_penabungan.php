<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $judul; ?></h3>
            </div>
            <form role="form" action="<?= base_url('penabungan/simpan'); ?>" method="post" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group">
                        <label for="idnasabah">ID Nasabah</label>
                        <select class="form-control select2" id="idnasabah" name="idnasabah" style="width: 100%;" required>
                            <option value="">-- Pilih Nasabah --</option>
                            <?php if (!empty($data_nasabah)): ?>
                                <?php foreach ($data_nasabah as $nasabah): ?>
                                    <option value="<?= $nasabah->idnasabah; ?>"><?= $nasabah->idnasabah . ' - ' . $nasabah->username; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idsampah">Jenis Sampah</label>
                        <select class="form-control select2" id="idsampah" name="idsampah" style="width: 100%;" required>
                            <option value="">-- Pilih Jenis Sampah --</option>
                            <?php if (!empty($data_sampah)): ?>
                                <?php foreach ($data_sampah as $sampah): ?>
                                    <option value="<?= $sampah->idsampah; ?>" data-harga="<?= $sampah->harga; ?>"><?= $sampah->idsampah . ' - ' . $sampah->jenis . ' (Rp ' . number_format($sampah->harga, 0, ',', '.') . '/' . strtolower(str_replace('Per ', '', $sampah->berat)) . ')'; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar (Opsional)</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                        <p class="help-block">Format file: jpg, jpeg, png, gif</p>
                    </div>
                    <div class="form-group">
                        <label for="berat">Berat/Jumlah</label>
                        <input type="text" class="form-control" id="berat" name="berat" placeholder="Masukkan berat/jumlah sampah (contoh: 2.5 atau 5)" required>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga Total</label>
                        <input type="text" class="form-control" id="harga" name="harga" value="0" readonly>
                    </div>
                    </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
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