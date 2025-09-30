<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bank Sampah | Registrasi</title>
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
            /* Add some padding to prevent content from touching edges if desired */
            padding: 20px 0; /* Padding atas dan bawah */
            box-sizing: border-box; /* Include padding in the element's total width and height */
        }
        .register-box {
            background-color: #051cf1;
            border-radius: 10px;
            padding: 10px;
            width: 360px;
            /* margin-top: 30px; */ /* Dihapus, flexbox akan menangani */
            flex-shrink: 0; /* Mencegah box menyusut */
            /* Tambahkan margin-top/bottom untuk kontrol jarak jika diperlukan */
            margin-top: 5px; /* Contoh: beri jarak sedikit dari logo di atasnya */
        }
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
            /* margin-top: 80px; */ /* Dihapus, flexbox akan menangani */
            flex-shrink: 0; /* Mencegah logo menyusut */
            /* Tambahkan margin-top/bottom untuk kontrol jarak jika diperlukan */
            margin-bottom: 20px; /* Contoh: beri jarak sedikit dari form di bawahnya */
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
        .register-box-msg {
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
        .btn-register {
            background-color: white;
            color: black;
            width: 100%;
            border-radius: 5px;
            font-weight: bold;
        }
        .register-box a {
            color: white;
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
<body class="hold-transition register-page">
<div class="login-logo">
    <img src="<?= base_url()?>assets/dist/img/logobank.png" width="200" alt=""><br>
    <b class="login">Bank Sampah</b><br>
    <b class="login">Agrowisata Ibnu Al-Mubarok</b>
</div>
<div class="register-box">
    <div class="register-box-body" style="background-color: #051cf1; border-radius: 10px;">
        <p class="register-box-msg">Registrasi</p>

        <?php echo $this->session->flashdata('info'); ?>

        <form action="<?= base_url('register/proses_registrasi') ?>" method="post">
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Email" name="email" value="<?= set_value('email') ?>" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                <?= form_error('email', '<small class="text-white-error">', '</small>'); ?>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                <?= form_error('password', '<small class="text-white-error">', '</small>'); ?>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Konfirmasi Password" name="password_conf" required>
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                <?= form_error('password_conf', '<small class="text-white-error">', '</small>'); ?>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-register btn-flat">Daftar</button>
                </div>
            </div>
        </form>

        <a href="<?= base_url('login') ?>" class="text-center" style="margin-top: 10px; display: block;">Sudah punya akun? Login di sini.</a>

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
            increaseArea: '20%' /* optional */
        });
    });
</script>
</body>
</html>