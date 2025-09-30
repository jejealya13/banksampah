<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Bank Sampah | Lupa Kata Sandi</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	
	   <!-- Favicon -->
  <link rel="icon" href="<?= base_url()?>assets/dist/img/logotab.png" type="image/png">
  <link rel="shortcut icon" href="<?= base_url()?>assets/dist/img/logotab.png" type="image/png">
	
  <link rel="stylesheet" href="<?= base_url()?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url()?>assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= base_url()?>assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_url()?>assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/iCheck/square/blue.css">

  <style>
    body {
        background-image: url('<?= base_url()?>assets/dist/img/agrowisata.jpeg');
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px 0;
        box-sizing: border-box;
    }
    .login {
        font-size: 24px;
        font-weight: bold;
        color: white;
        text-align: center;
        text-shadow: 2px 2px 0 black;
        white-space: nowrap;
        margin-bottom: 2px;
    }
    .login-box-msg {
        font-size: 20px;
        color: white;
        font-weight: bold;
        text-align: center;
        text-shadow: 2px 2px 0 black;
        margin-bottom: 15px;
    }
    .btn-login {
        background-color: white;
        color: black;
        font-weight: bold;
        width: 100%;
    }
    .login-box {
        border-radius: 10px;
        width: 360px;
        flex-shrink: 0;
        padding: 20px;
        margin-top: 5px;
    }
    .login-logo {
        text-align: center;
        flex-shrink: 0;
        margin-bottom: 20px;
    }
    .login-box a {
        color: white;
        font-weight: bold;
    }
    .form-group {
        margin-bottom: 10px;
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
<body class="hold-transition login-page">
<div class="login-logo">
    <img src="<?= base_url()?>assets/dist/img/logobank.png" width="200" alt=""><br>
    <b class="login">Bank Sampah</b><br>
    <b class="login">Agrowisata Ibnu Al-Mubarok</b>
</div>
<div class="login-box">
  <div class="login-box-body" style="background-color: #051cf1; border-radius: 10px;">
    <p class="login-box-msg">Lupa Kata Sandi</p>
    <p class="text-center" style="color: white; margin-bottom: 20px;">Masukkan email Anda untuk reset kata sandi.</p>

    <?= $this->session->flashdata('info'); ?>

    <form action="<?= base_url('login/send_reset_link'); ?>" method="post">
      <div class="form-group has-feedback">
        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= set_value('email') ?>" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        <?= form_error('email', '<small class="text-white-error">', '</small>'); ?>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-login btn-flat">Kirim Tautan Reset</button>
        </div>
    </div>
</form>
    <a href="<?= base_url('login') ?>" class="text-center" style="margin-top: 10px; display: block;">Kembali ke Halaman Login</a>
    </div>
</div>

<script src="<?= base_url()?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?= base_url()?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url()?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
</script>
</body>
</html>