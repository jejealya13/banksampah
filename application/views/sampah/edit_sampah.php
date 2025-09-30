<div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $judul; ?></h3>
        </div>

        <form method="post" action="<?= base_url() ?>sampah/update" enctype="multipart/form-data" class="form-horizontal">
            <div class="box-body">
                <input type="hidden" name="idsampah" value="<?= $data['idsampah']; ?>">

                <div class="form-group">
                    <label for="inputJenis" class="col-sm-2 control-label">Jenis Sampah</label>
                    <div class="col-sm-10">
                        <input type="text" name="jenis" value="<?= $data['jenis']; ?>" class="form-control" placeholder="Jenis Sampah" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputGambar" class="col-sm-2 control-label">Gambar</label>
                    <div class="col-sm-10">
                        <input type="file" name="gambar" class="form-control">
                        <img src="<?= base_url('uploads/' . $data['gambar']); ?>" style="width: 100px; height: auto;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputBerat" class="col-sm-2 control-label">Berat</label>
                    <div class="col-sm-10">
                        <select name="berat" class="form-control" required>
                            <option value="Per Kg" <?= $data['berat'] == 'Per Kg' ? 'selected' : ''; ?>>Per Kg</option>
                            <option value="Per Buah" <?= $data['berat'] == 'Per Buah' ? 'selected' : ''; ?>>Per Buah</option>
                            <option value="Per Botol" <?= $data['berat'] == 'Per Botol' ? 'selected' : ''; ?>>Per Botol</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputHarga" class="col-sm-2 control-label">Harga</label>
                    <div class="col-sm-10">
                    <input type="text" name="harga" value="<?= number_format($data['harga'], 0, ',', '.'); ?>" class="form-control" placeholder="Harga" required>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <a href="<?= base_url() ?>sampah" class="btn btn-warning">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>