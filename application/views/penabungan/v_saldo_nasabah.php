<style>
    /* Untuk browser berbasis WebKit (Chrome, Safari, Edge) */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Untuk Firefox (seharusnya sudah dengan -moz-appearance) */
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* Gaya untuk memusatkan card */
    .center-card {
        display: flex;
        justify-content: center;
    }

    /* Gaya untuk memperlebar card */
    .wide-card {
        width: 100%; /* Sesuaikan jika perlu */
        max-width: 1000px; /* Sesuaikan jika perlu */
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="row center-card">
    <div class="wide-card">
        <div class="box box-primary">
            <div class="box-header with-border" style="background-color: #051cf1; color: white;">
                <h3 class="box-title">Informasi Saldo dan Tarik Saldo</h3>
            </div>
            <div class="box-body">
                <div class="well well-sm" style="background-color: #f0f8ff; border: 1px solid #051cf1;">
                    <h4>Informasi Nasabah</h4>
                    <p><strong>Nama:</strong> <span style="color:rgb(18, 19, 19);"><?= $nasabah->username; ?></span></p>
                    <p><strong>ID Nasabah:</strong> <span style="color:rgb(32, 33, 32);"><?= $nasabah->idnasabah; ?></span></p>
                </div>

                <h4 style="margin-top: 20px;">Saldo Saat Ini</h4>
                <div style="padding: 15px; border: 2px solid #28a745; border-radius: 5px; margin-bottom: 20px; background-color: #e9ecef; text-align: center;">
                    <strong style="font-size: 2em; color: #28a745;">Rp <?= number_format($total_saldo, 0, ',', '.'); ?></strong>
                </div>

                <!-- <h4 style="margin-top: 20px;">Tarik Saldo</h4> -->
                <?= form_open('nasabah/proses_tarik_saldo'); ?>
                    <?php
                    if (!empty($this->session->flashdata('error'))) {
                        echo '<div class="alert alert-danger">' . $this->session->flashdata('error') . '</div>';
                    }
                    if (!empty($this->session->flashdata('success'))) {
                        echo '<div class="alert alert-success">' . $this->session->flashdata('success') . '</div>';
                    }
                    ?>
                    <div class="form-group">
                        <label for="jumlah_tarik">Jumlah Penarikan (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                            <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" required placeholder="Masukkan jumlah yang ingin ditarik">
                        </div>
                    </div>
                    <input type="hidden" name="idnasabah" value="<?= $nasabah->idnasabah; ?>">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-arrow-circle-left"></i> Tarik Saldo</button>
                    <a href="<?= base_url('nasabah'); ?>" class="btn btn-default"><i class="fa fa-reply"></i> Kembali ke Daftar Nasabah</a>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>