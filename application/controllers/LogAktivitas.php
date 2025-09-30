<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LogAktivitas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Pastikan model M_logaktivitas sudah dimuat
        $this->load->model('M_logaktivitas');
        // Tambahkan pengecekan login atau hak akses jika ini halaman admin/internal
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    public function index() {
        // 1. Ambil data log aktivitas dari model
        $data_logs = $this->M_logaktivitas->getLogAktivitas(); // Mengambil data log

        // 2. Siapkan data untuk layout v_dashboard
        $isi = array(); // Inisialisasi array isi
        $isi['content'] = 'v_log_aktivitas'; // Ini adalah view konten yang akan dimuat di dalam v_dashboard
        $isi['judul'] = 'Riwayat Aktivitas Sistem'; // Judul untuk halaman ini

        // 3. PENTING: Kirim data log ke view konten (v_log_aktivitas) melalui array $isi
        // Variabel '$log_aktivitas' di view akan berisi data dari $data_logs
        $isi['log_aktivitas'] = $data_logs;

        // 4. Load v_dashboard dan sertakan semua data dari array 'isi'
        $this->load->view('v_dashboard', $isi);
    }

    // Anda bisa punya fungsi lain untuk pagination, filter, dll.
}