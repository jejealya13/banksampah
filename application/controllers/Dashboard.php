<?php

class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('M_dashboard');
        
        // Tambahkan pengecekan login di sini
        if (!$this->session->userdata('logged_in')) {
            redirect('login'); // Redirect ke halaman login
        }
    }

    public function index() {
        $data['judul'] = 'Dashboard';
        $data['total_nasabah'] = $this->M_dashboard->countNasabah();
        $data['total_sampah'] = $this->M_dashboard->countSampah();
        $data['transaksi_penabungan'] = $this->M_dashboard->countTransaksiPenabungan();
        $data['transaksi_penarikan'] = $this->M_dashboard->countTransaksiPenarikan();
        
        // Tambahkan jumlah button untuk laporan
        $data['total_laporan'] = 4; // Misalnya ada 2 button di halaman laporan

        // Tambahkan ini untuk mendefinisikan konten
        $data['content'] = 'dashboard_content'; // Ganti dengan nama view yang ingin dimuat
        
        // Data untuk grafik jenis sampah
        $data['jenis_sampah'] = $this->M_dashboard->getJenisSampahTerbanyak();

        // Data untuk grafik nasabah per tanggal
        $data['nasabah_per_tanggal'] = $this->M_dashboard->getNasabahTerdaftarPerTanggal();


        $this->load->view('v_dashboard', $data);
        
    }
}