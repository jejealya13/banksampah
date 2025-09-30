<?php
defined('BASEPATH') OR exit('No direct script access allowed'); // Pastikan ini ada

class Penarikan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_penarikan');
        $this->load->model('m_nasabah'); // Pastikan model ini memuat metode untuk saldo
        $this->load->model('M_logaktivitas'); // Pastikan model log aktivitas ini ada dan berfungsi
        $this->load->library('session'); // Pastikan library session dimuat
        $this->load->helper('url'); // Pastikan helper url dimuat untuk redirect() dan base_url()/site_url()

        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    public function index()
    {
        $isi['content'] = 'penarikan/v_penarikan';
        $isi['judul']   = 'Daftar Pengajuan Penarikan';
        // Menggunakan method yang sudah ada untuk mendapatkan semua pengajuan penarikan
        $isi['data']    = $this->m_penarikan->getAllPengajuanPenarikanWithNamaNasabah();
        $this->load->view('v_dashboard', $isi);
    }

    // --- METODE BARU: Untuk memverifikasi pengajuan penarikan ---
    public function verifikasi_penarikan($idpenarikan)
    {
        $user_email = $this->session->userdata('email');
        $penarikan = $this->m_penarikan->getPengajuanPenarikanById($idpenarikan);

        if (!$penarikan) {
            $this->session->set_flashdata('error', 'Data pengajuan penarikan tidak ditemukan.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Verifikasi Penarikan Dana',
                'Gagal memproses verifikasi. Data pengajuan penarikan ID: ' . $idpenarikan . ' tidak ditemukan.'
            );
            redirect('penarikan');
            return;
        }

        // Cek status penarikan. Hanya bisa diverifikasi jika 'Menunggu Verifikasi'
        if ($penarikan->status != 'Menunggu Verifikasi') {
            $this->session->set_flashdata('error', 'Pengajuan penarikan sudah dalam status "' . $penarikan->status . '". Tidak dapat diverifikasi.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Verifikasi Penarikan Dana',
                'Pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" sudah berstatus ' . $penarikan->status . '. Tidak dapat diverifikasi.'
            );
            redirect('penarikan');
            return;
        }

        // Ambil saldo aktual nasabah
        $saldo_nasabah_aktual = $this->m_nasabah->getDirectSaldoTabunganById($penarikan->idnasabah);

        // Validasi nominal penarikan dengan saldo aktual
        if ($penarikan->nominal > $saldo_nasabah_aktual) {
            $this->session->set_flashdata('error', 'Nominal penarikan (Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ') melebihi saldo nasabah yang tersedia saat ini (Rp ' . number_format($saldo_nasabah_aktual, 0, ',', '.') . ').');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Verifikasi Penarikan Dana',
                'Nominal penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" melebihi saldo. Nominal diajukan: Rp' . number_format($penarikan->nominal, 0, ',', '.') . ', Saldo: Rp' . number_format($saldo_nasabah_aktual, 0, ',', '.') . '.'
            );
            redirect('penarikan');
            return;
        }

        // Lakukan pengurangan saldo nasabah terlebih dahulu
        $berhasil_kurangi_saldo = $this->m_nasabah->kurangi_saldo($penarikan->idnasabah, $penarikan->nominal);

        if ($berhasil_kurangi_saldo) {
            // Jika pengurangan saldo berhasil, update status penarikan
            $update_data_penarikan = [
                'status' => 'Selesai',
                'tanggal_diproses' => date('Y-m-d H:i:s'),
                // 'id_admin_proses' => $this->session->userdata('id_admin') // Jika ada kolom ini
            ];
            $update_status_penarikan = $this->m_penarikan->updateStatusPenarikan($idpenarikan, $update_data_penarikan);

            if ($update_status_penarikan) {
                $this->session->set_flashdata('info', 'Pengajuan penarikan berhasil diverifikasi dan saldo nasabah telah dikurangi.');
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Verifikasi Penarikan Dana',
                    'Berhasil memverifikasi penarikan dana ID: ' . $idpenarikan . ' sebesar Rp' . number_format($penarikan->nominal, 0, ',', '.') . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '". Saldo nasabah berhasil dikurangi.'
                );
            } else {
                // Rollback: Jika update status penarikan gagal, kembalikan saldo nasabah
                $this->m_nasabah->tambah_saldo($penarikan->idnasabah, $penarikan->nominal);
                $this->session->set_flashdata('error', 'Gagal memperbarui status pengajuan penarikan. Saldo nasabah telah dikembalikan.');
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Verifikasi Penarikan Dana',
                    'Gagal memperbarui status pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" menjadi "Selesai". Saldo telah dikembalikan.'
                );
            }
        } else {
            $this->session->set_flashdata('error', 'Gagal mengurangi saldo nasabah. Proses verifikasi dibatalkan.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Verifikasi Penarikan Dana',
                'Gagal mengurangi saldo nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" (ID: ' . $penarikan->idnasabah . ') sebesar Rp' . number_format($penarikan->nominal, 0, ',', '.') . ' untuk penarikan ID: ' . $idpenarikan . '. Proses verifikasi dibatalkan.'
            );
        }
        redirect('penarikan');
    }

    // --- METODE BARU: Untuk menolak pengajuan penarikan ---
    public function tolak_penarikan($idpenarikan)
    {
        $user_email = $this->session->userdata('email');
        $penarikan = $this->m_penarikan->getPengajuanPenarikanById($idpenarikan);

        if (!$penarikan) {
            $this->session->set_flashdata('error', 'Data pengajuan penarikan tidak ditemukan.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Tolak Penarikan Dana',
                'Gagal memproses penolakan. Data pengajuan penarikan ID: ' . $idpenarikan . ' tidak ditemukan.'
            );
            redirect('penarikan');
            return;
        }

        // Hanya bisa ditolak jika statusnya 'Menunggu Verifikasi'
        if ($penarikan->status != 'Menunggu Verifikasi') {
            $this->session->set_flashdata('error', 'Pengajuan penarikan sudah dalam status "' . $penarikan->status . '". Tidak dapat ditolak.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Tolak Penarikan Dana',
                'Pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" sudah berstatus ' . $penarikan->status . '. Tidak dapat ditolak.'
            );
            redirect('penarikan');
            return;
        }

        $update_data_penarikan = [
            'status' => 'Ditolak',
            'tanggal_diproses' => date('Y-m-d H:i:s'), // Opsional, bisa juga menggunakan kolom terpisah untuk tanggal tolak
            // 'id_admin_proses' => $this->session->userdata('id_admin')
        ];
        $update_status_penarikan = $this->m_penarikan->updateStatusPenarikan($idpenarikan, $update_data_penarikan);

        if ($update_status_penarikan) {
            $this->session->set_flashdata('info', 'Pengajuan penarikan berhasil ditolak.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Tolak Penarikan Dana',
                'Berhasil menolak pengajuan penarikan dana ID: ' . $idpenarikan . ' sebesar Rp' . number_format($penarikan->nominal, 0, ',', '.') . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '".'
            );
        } else {
            $this->session->set_flashdata('error', 'Gagal menolak pengajuan penarikan.');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Tolak Penarikan Dana',
                'Gagal memperbarui status pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" menjadi "Ditolak".'
            );
        }
        redirect('penarikan');
    }

    // --- HAPUS ATAU KOMENTARI METODE INI KARENA TIDAK DIGUNAKAN LAGI ---
    // public function proses_tarik_saldo($id)
    // {
    //     // ... (kode lama yang memuat form_tarik_saldo) ...
    // }

    // --- HAPUS ATAU KOMENTARI METODE INI KARENA TIDAK DIGUNAKAN LAGI ---
    // public function simpan_tarik_saldo()
    // {
    //     // ... (kode lama untuk menyimpan tarik saldo dari form terpisah) ...
    // }

    // --- Opsional: Metode Hapus (jika Anda ingin ada fungsi hapus terpisah, meskipun tidak di view) ---
    // public function hapus($idpenarikan)
    // {
    //     $user_email = $this->session->userdata('email');
    //     $penarikan = $this->m_penarikan->getPengajuanPenarikanById($idpenarikan);

    //     if (!$penarikan) {
    //         $this->session->set_flashdata('error', 'Pengajuan penarikan tidak ditemukan.');
    //         $this->M_logaktivitas->log_aktivitas(
    //             $user_email,
    //             'Gagal Hapus Pengajuan Penarikan',
    //             'Gagal menghapus. Data pengajuan penarikan ID: ' . $idpenarikan . ' tidak ditemukan.'
    //         );
    //         redirect('penarikan');
    //         return;
    //     }

    //     // Anda dapat menentukan kondisi kapan sebuah pengajuan penarikan bisa dihapus
    //     // Misalnya, hanya jika statusnya 'Menunggu Verifikasi' atau 'Ditolak'
    //     if ($penarikan->status == 'Menunggu Verifikasi' || $penarikan->status == 'Ditolak') {
    //         if ($this->m_penarikan->deletePenarikan($idpenarikan)) { // Pastikan ada method deletePenarikan di model
    //             $this->session->set_flashdata('info', 'Pengajuan penarikan berhasil dihapus.');
    //             $this->M_logaktivitas->log_aktivitas(
    //                 $user_email,
    //                 'Hapus Pengajuan Penarikan',
    //                 'Berhasil menghapus pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '".'
    //             );
    //         } else {
    //             $this->session->set_flashdata('error', 'Gagal menghapus pengajuan penarikan.');
    //             $this->M_logaktivitas->log_aktivitas(
    //                 $user_email,
    //                 'Gagal Hapus Pengajuan Penarikan',
    //                 'Gagal menghapus pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '".'
    //             );
    //         }
    //     } else {
    //         $this->session->set_flashdata('error', 'Tidak dapat menghapus pengajuan penarikan yang sudah ' . $penarikan->status . '.');
    //         $this->M_logaktivitas->log_aktivitas(
    //             $user_email,
    //             'Gagal Hapus Pengajuan Penarikan',
    //             'Tidak dapat menghapus pengajuan penarikan ID: ' . $idpenarikan . ' untuk nasabah "' . ($penarikan->nama_nasabah ?? 'Tidak Diketahui') . '" karena statusnya sudah ' . $penarikan->status . '.'
    //         );
    //     }
    //     redirect('penarikan');
    // }
}