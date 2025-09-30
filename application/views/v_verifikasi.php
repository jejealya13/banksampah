<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bank Sampah | Verifikasi</title>
	
	   <!-- Favicon -->
  <link rel="icon" href="<?= base_url()?>assets/dist/img/logotab.png" type="image/png">
  <link rel="shortcut icon" href="<?= base_url()?>assets/dist/img/logotab.png" type="image/png">
	
  <link rel="stylesheet" href="<?= base_url()?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <style>
    body {
        background-image: url('<?= base_url()?>assets/dist/img/agrowisata.jpeg');
        background-size: cover;
        color: white;
    }
    .login-box {
        background-color: #051cf1;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        width: 360px; /* Lebar standar untuk login box */
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    .login-logo {
        text-align: center;
        margin-bottom: 20px;
        margin-top: 80px;
    }
    .login {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 2px 2px 0 black;
    }
    .login-box-msg {
        font-size: 20px;
        color: white;
        font-weight: bold;
        text-align: center;
        text-shadow: 2px 2px 0 black;
        margin-bottom: 15px;
    }
    .form-control {
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .btn-login {
        background-color: white;
        color: black;
        width: 100%;
        border-radius: 5px;
        font-weight: bold;
    }
    .login-box a {
        color: white; /* Warna link menjadi putih agar terlihat */
        font-weight: bold;
    }
    .text-white-error {
        color: white !important; /* Menggunakan !important untuk memastikan override */
        /* Anda juga bisa menyesuaikan ukuran font atau margin jika diperlukan */
        font-size: 0.875em; /* Opsional: membuat font sedikit lebih kecil dari default */
        margin-top: 5px;   /* Opsional: memberi sedikit jarak dari input field */
        display: block;    /* Pastikan pesan error menempati barisnya sendiri */
    }
  </style>
</head>
<body>
<div class="login-logo">
    <img src="<?= base_url()?>assets/dist/img/logobank.png" width="200" alt=""><br>
    <b class="login">Bank Sampah</b><br>
    <b class="login">Agrowisata Ibnu Al-Mubarok</b>
</div>
<div class="login-box">
  <div class="login-box-body" style="background-color: #051cf1; border-radius: 10px;">
    <p class="login-box-msg">Verifikasi</p>
    <p style="color: white; margin-bottom: 15px;">Masukkan kode OTP yang telah dikirimkan ke email Anda. Kode OTP berlaku 5 menit.</p>

    <?= $this->session->flashdata('info'); ?>
    <form action="<?= base_url('login/proses_verifikasi'); ?>" method="post">
      <div class="form-group">
        <input type="text" name="otp" class="form-control" placeholder="Masukkan Kode OTP" required maxlength="6">
        <?= form_error('otp', '<small class="text-white-error">', '</small>'); ?>
      </div>
      <button type="submit" class="btn btn-login">Verifikasi</button>
    </form>
    <a href="<?= base_url('login') ?>" class="text-center" style="margin-top: 10px; display: block;">Kembali ke halaman login</a>
  </div>
</div>
</body>
</html>