<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp'; // Protokol
$config['smtp_host'] = 'smtp.gmail.com'; // Ganti dengan host SMTP Anda
$config['smtp_port'] = 587; // Port SMTP, bisa 465 atau 587
$config['smtp_user'] = 'banksampahalmubarok@gmail.com'; // Email Anda
$config['smtp_pass'] = 'banksampah2021'; // Password email Anda
$config['mailtype'] = 'html'; // Tipe email
$config['charset'] = 'utf-8'; // Karakter
$config['newline'] = "\r\n"; // Baris baru