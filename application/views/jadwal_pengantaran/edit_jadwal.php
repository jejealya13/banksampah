<div class="container" style="margin-top: 20px; max-width: 1025px;">
    <!--<h2>Edit Jadwal Pengantaran</h2>-->
    <form action="<?= base_url('jadwal_pengantaran/edit/'.$jadwal->id) ?>" method="post">
        <div class="form-group" style="max-width: 700px;">
            <label for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($jadwal->description) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('jadwal_pengantaran') ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
