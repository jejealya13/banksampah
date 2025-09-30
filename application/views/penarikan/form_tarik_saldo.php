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
        width: 100%; /* Anda bisa menyesuaikan persentase ini */
        max-width: 1000px; /* Atau lebar maksimum dalam pixel */
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="row center-card">
    <div class="wide-card"> <div class="box box-primary">
            <div class="box-header with-border" style="background-color: #051cf1; color: white;">
                <h3 class="box-title">Informasi Saldo dan Tarik Saldo</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($this->session->flashdata('error'))) : ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                <?php if (!empty($this->session->flashdata('info'))) : ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('info'); ?></div>
                <?php endif; ?>
                <?php if ($penarikan) : ?>
                    <div class="well well-sm" style="background-color: #f0f8ff; border: 1px solid #051cf1;">
                        <h4>Informasi Pengajuan Penarikan</h4>
                        <p><strong>ID Penarikan:</strong> <span style="color:rgb(32, 33, 32);"><?= $penarikan->idpenarikan; ?></span></p>
                        <p><strong>ID Nasabah:</strong> <span style="color:rgb(32, 33, 32);"><?= $penarikan->idnasabah; ?> (<?= $penarikan->nama_nasabah; ?>)</span></p>
                        <p><strong>Metode:</strong> <span style="color:rgb(32, 33, 32);"><?= $penarikan->metode; ?></span></p> 
						<p><strong>NoRek:</strong> <span style="color:rgb(32, 33, 32);"><?= $penarikan->nomor_rekening; ?></span></p> 
						<p><strong>Tanggal Pengajuan:</strong> <span style="color:rgb(32, 33, 32);"><?= date('d/m/Y H:i:s', strtotime($penarikan->tanggal)); ?></span></p>
						<!-- <p><strong>Tanggal Pengajuan:</strong> <span style="color:rgb(32, 33, 32);"><?= date('d/m/Y H:i:s', strtotime($penarikan->tanggal)); ?></span></p> -->
                        <p><strong>Nominal Diajukan:</strong> <span style="color:rgb(32, 33, 32);">Rp. <?= number_format($penarikan->nominal, 0, ',', '.'); ?></span></p>
                    </div>

                    <h4 style="margin-top: 20px;">Saldo Saat Ini</h4>
                    <div style="padding: 15px; border: 2px solid #28a745; border-radius: 5px; margin-bottom: 20px; background-color: #e9ecef; text-align: center;">
                        <strong style="font-size: 2em; color: #28a745;">Rp <?= number_format($saldo_nasabah_aktual, 0, ',', '.'); ?></strong>
                    </div>

                    <!-- <h4 style="margin-top: 20px;">Form Tarik Saldo</h4> -->
                    <form method="post" action="<?= base_url('penarikan/simpan_tarik_saldo'); ?>">
                        <input type="hidden" name="idpenarikan" value="<?= $penarikan->idpenarikan; ?>">
                        <div class="form-group">
                            <label for="nominal_tarik">Nominal Tarik:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                <input type="number" class="form-control" id="nominal_tarik" name="nominal_tarik"
                                       value="<?= $penarikan->nominal; ?>" readonly placeholder="Masukkan jumlah yang ingin ditarik">
                            </div>
                            <small class="form-text text-muted">Nominal ini sudah terisi sesuai permintaan.</small>
                        </div>
                        <button type="submit" class="btn btn-danger" <?= ($penarikan->status == 'Selesai') ? 'disabled' : ''; ?>>
                            <i class="fa fa-arrow-circle-left"></i> Simpan dan Tarik Saldo
                        </button>
                        <a href="<?= base_url('penarikan'); ?>" class="btn btn-default"><i class="fa fa-reply"></i> Batal</a>
                    </form>
                <?php else : ?>
                    <div class="row center-card">
                        <div class="wide-card"> <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Data Tidak Ditemukan</h3>
                                </div>
                                <div class="box-body">
                                    <p>Data pengajuan penarikan tidak ditemukan.</p>
                                    <a href="<?= base_url('penarikan'); ?>" class="btn btn-default"><i class="fa fa-reply"></i> Kembali ke Daftar Pengajuan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>