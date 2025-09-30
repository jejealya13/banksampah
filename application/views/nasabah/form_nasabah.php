<div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $judul; ?></h3>
        </div>

        <form method="post" action="<?= base_url()?>nasabah/simpan" class="form-horizontal">
            <div class="box-body">
                <div class="form-group">
                    <label for="idnasabah_input" class="col-sm-2 control-label">Id Nasabah</label>
                    <div class="col-sm-10">
                        <input type="text" name="idnasabah" value="<?= set_value('idnasabah', $idnasabah_baru ?? ''); ?>" class="form-control" id="idnasabah_input" readonly>
                    </div>
            </div>
                <div class="form-group <?= form_error('namanasabah') ? 'has-error' : ''; ?>">
                    <label for="namanasabah_input" class="col-sm-2 control-label">Nama Nasabah</label>
                    <div class="col-sm-10">
                        <input type="text" name="namanasabah" value="<?= set_value('namanasabah'); ?>" class="form-control" id="namanasabah_input" placeholder="Nama Nasabah" required>
                        <?= form_error('namanasabah', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>
                <div class="form-group <?= form_error('username') ? 'has-error' : ''; ?>">
                    <label for="username_input" class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" name="username" value="<?= set_value('username'); ?>" class="form-control" id="username_input" placeholder="Username" required>
                        <?= form_error('username', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>

                <div class="form-group <?= form_error('phone') ? 'has-error' : ''; ?>">
                    <label for="phone_input" class="col-sm-2 control-label">No Telepon</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon">+62</span>
                            <input type="text" name="phone" value="<?= set_value('phone'); ?>" class="form-control" id="phone_input" placeholder="Contoh: 81234567890" required pattern="[0-9]{8,13}" title="Masukkan nomor telepon diawali angka 8 (tanpa 0) dan tanpa +62. Contoh: 81234567890">
                        </div>
                        <?= form_error('phone', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>

                <div class="form-group <?= form_error('password') ? 'has-error' : ''; ?>">
                    <label for="password_input" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" id="password_input" placeholder="Password" required>
                        <?= form_error('password', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>

                <div class="form-group <?= form_error('birthdate') ? 'has-error' : ''; ?>">
                    <label for="birthdate_input" class="col-sm-2 control-label">Tanggal Lahir</label>
                    <div class="col-sm-10">
                        <input type="date" name="birthdate" value="<?= set_value('birthdate'); ?>" class="form-control" id="birthdate_input" required>
                        <?= form_error('birthdate', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>

                <div class="form-group <?= form_error('address') ? 'has-error' : ''; ?>">
                    <label for="address_input" class="col-sm-2 control-label">Alamat</label>
                    <div class="col-sm-10">
                        <textarea name="address" class="form-control" id="address_input" cols="30" rows="1.5" placeholder="Alamat" required><?= set_value('address'); ?></textarea>
                        <?= form_error('address', '<span class="help-block text-danger">', '</span>'); ?>
                    </div>
                </div>

            </div>

            <div class="box-footer">
                <a href="<?= base_url()?>nasabah" class="btn btn-warning">Cancel</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone_input');

    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value;

        // Hapus karakter non-digit
        value = value.replace(/\D/g, '');

        // Jika diawali dengan '0', hapus '0' tersebut
        if (value.startsWith('0')) {
            value = value.substring(1);
        }
        e.target.value = value;
    });
});
</script>

</div>