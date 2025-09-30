<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Bank Sampah | Login</title>
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
        height: 100vh; /* Penting: agar body mengisi seluruh tinggi viewport */
        display: flex; /* Gunakan flexbox untuk penataan vertikal */
        flex-direction: column; /* Tata item secara kolom */
        justify-content: center; /* Posisikan konten di tengah vertikal */
        align-items: center; /* Posisikan konten di tengah horizontal */
        padding: 20px 0; /* Padding atas dan bawah */
        box-sizing: border-box; /* Include padding in the element's total width and height */
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
        margin-bottom: 15px; /* Tetap pertahankan ini untuk jarak dari form */
    }
    .btn-login {
        background-color: white;
        color: black;
        font-weight: bold;
        width: 100%;
    }
    .login-box {
        border-radius: 10px;
        width: 360px; /* Lebar standar untuk login box */
        /* margin-left: auto; */ /* Dihapus, align-items: center yang menangani */
        /* margin-right: auto; */ /* Dihapus, align-items: center yang menangani */
        /* margin-top: 30px; */ /* Dihapus, flexbox yang menangani */
        flex-shrink: 0; /* Mencegah box menyusut */
        padding: 20px; /* Tambahkan padding ke box */
        margin-top: 5px; /* Jarak dari logo, sesuaikan dengan v_register */
    }
    .login-logo {
        text-align: center;
        /* margin-bottom: 20px; */ /* Dihapus, gunakan margin-top/bottom pada logo dan box */
        /* margin-top: 80px; */ /* Dihapus, flexbox yang menangani */
        flex-shrink: 0; /* Mencegah logo menyusut */
        margin-bottom: 20px; /* Jarak dari form, sesuaikan dengan v_register */
    }
    .login-box a {
        color: white; /* Warna link menjadi putih agar terlihat */
        font-weight: bold;
    }
    .form-group { /* Tambahkan ini agar ada jarak antar input form */
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
    <p class="login-box-msg">Login</p>

    <?= $this->session->flashdata('info'); ?>

    <form action="<?= base_url('login/proses_login'); ?>" method="post">
      <div class="form-group has-feedback">
        <input type="email" name="username" class="form-control" placeholder="Email" value="<?= set_value('username') ?>" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        <?= form_error('username', '<small class="text-white-error">', '</small>'); ?>
      </div>

      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        <?= form_error('password', '<small class="text-white-error">', '</small>'); ?>
      </div>

     <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-login btn-flat">Login</button>
        </div>
     </div>
</form>
    <a href="<?= base_url('login/forgot_password') ?>" class="text-center" style="margin-top: 10px; display: block;">Lupa kata sandi?</a>
    <a href="<?= base_url('register') ?>" class="text-center" style="margin-top: 10px; display: block;">Daftar akun baru</a>
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