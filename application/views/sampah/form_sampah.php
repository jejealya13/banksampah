<div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $judul; ?></h3>
        </div>

        <!-- Menampilkan pesan flashdata jika ada -->
        <?php if ($this->session->flashdata('info')): ?>
        <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('info'); ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url() ?>sampah/simpan" enctype="multipart/form-data" class="form-horizontal">
            <div class="box-body">

                <!-- Jenis Sampah -->
                <div class="form-group">
                    <label for="inputJenis" class="col-sm-2 control-label">Jenis Sampah</label>
                    <div class="col-sm-10">
                        <input type="text" name="jenis" id="jenis" class="form-control" placeholder="Masukkan jenis sampah" required>
                    </div>
                </div>

                <!-- Gambar -->
                <div class="form-group">
                    <label for="inputGambar" class="col-sm-2 control-label">Gambar</label>
                    <div class="col-sm-10">
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <!-- Berat -->
                <div class="form-group">
                    <label for="inputBerat" class="col-sm-2 control-label">Berat</label>
                    <div class="col-sm-10">
                        <select name="berat" class="form-control" required>
                            <option value="Per Kg">Per Kg</option>
                            <option value="Per Buah">Per Buah</option>
                            <option value="Per Botol">Per Botol</option>
                        </select>
                        <small class="form-text text-muted">Pilih satuan berat sesuai dengan jenis sampah.</small>
                    </div>
                </div>

                <!-- Harga -->
                <div class="form-group">
                    <label for="inputHarga" class="col-sm-2 control-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="text" name="harga" id="harga" class="form-control" placeholder="Masukkan harga (misal: 1000)" required>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <a href="<?= base_url()?>sampah" class="btn btn-warning">Cancel</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>