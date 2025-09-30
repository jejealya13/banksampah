<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'login'; // Pastikan ini mengarah ke login sebagai halaman awal
$route['register'] = 'register';
$route['register/proses_registrasi'] = 'register/proses_registrasi';
$route['verifikasi'] = 'login/verifikasi'; // Rute untuk halaman verifikasi OTP
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['verifikasi'] = 'login/verifikasi';
$route['dashboard'] = 'dashboard/index';

$route['penarikan'] = 'penarikan/index';
$route['penarikan/tarikSaldo/(:num)'] = 'penarikan/tarikSaldo/$1';

$route['laporan'] = 'laporan/index'; // Mengarahkan ke menu laporan
//$route['laporan'] = 'laporan/nasabah'; // Redirect to laporan nasabah
$route['laporan/nasabah'] = 'laporan/nasabah';
$route['laporan/sampah'] = 'laporan/sampah';
$route['laporan/filter_nasabah'] = 'laporan/filter_nasabah';
$route['laporan/filter_sampah'] = 'laporan/filter_sampah';
$route['api/harga-sampah'] = 'api/harga_sampah';

$route['log_aktivitas'] = 'LogAktivitas/index';

$route['api/penabungan/tambah'] = 'api/tambah_penabungan';
$route['api/get_saldo_tabungan'] = 'api/get_saldo_tabungan';
$route['penabungan/saldo/(:num)'] = 'penabungan/saldo/$1';

$route['api/buat_permintaan_penarikan'] = 'api/buat_permintaan_penarikan';
$route['api/get_saldo_terbaru_penarikan'] = 'api/get_saldo_terbaru_penarikan';

$route['nasabah/proses_tarik_saldo'] = 'nasabah/proses_tarik_saldo';

// --- BARU DITAMBAHKAN: Rute untuk update FCM token ---
$route['api/update_fcm_token'] = 'api/update_fcm_token';
// --- AKHIR BARU DITAMBAHKAN ---
