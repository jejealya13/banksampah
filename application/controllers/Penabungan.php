<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Penabungan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_penabungan'); // Load model m_penabungan
        $this->load->model('m_nasabah'); // Load model m_nasabah (mungkin diperlukan untuk detail)
        $this->load->model('m_sampah'); // Load model m_sampah (mungkin diperlukan untuk detail)
		$this->load->model('m_logaktivitas'); // --- FOKUS: Menambahkan model m_logaktivitas

        // Pastikan user sudah login (sesuaikan dengan sistem autentikasi kamu)
        if (!$this->session->userdata('logged_in')) {
            redirect('login'); // Redirect ke halaman login jika belum login
        }
    }

    // Metode untuk menampilkan data transaksi penabungan
    public function index()
    {
        $isi['content'] = 'penabungan/v_penabungan'; // View untuk menampilkan data penabungan
        $isi['judul'] = 'Daftar Transaksi Penabungan'; // Judul halaman
        $isi['data'] = $this->m_penabungan->get_all_penabungan(); // Ambil semua data penabungan
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard (CI layout)
    }

    // Metode untuk menampilkan detail transaksi penabungan
    public function detail($idpenabungan)
    {
        $isi['content'] = 'penabungan/v_detail_penabungan'; // View untuk detail penabungan
        $isi['judul'] = 'Detail Transaksi Penabungan'; // Judul halaman
        $isi['penabungan'] = $this->m_penabungan->get_penabungan_by_id($idpenabungan); // Ambil data penabungan berdasarkan ID
        if (!$isi['penabungan']) {
            $this->session->set_flashdata('error', 'Data transaksi tidak ditemukan.');
            redirect('penabungan');
            return;
        }
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard
    }

    public function verifikasi_transaksi($idpenabungan)
    {
        // Ambil data penabungan berdasarkan ID
        $penabungan = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
		$user_email = $this->session->userdata('email'); // Variabel ini perlu diambil untuk log
        
        // Pastikan penabungan ada dan statusnya belum diverifikasi
        if ($penabungan && $penabungan->status == 'Belum Diverifikasi') {
            // Ubah status menjadi terverifikasi
            if ($this->m_penabungan->update_status($idpenabungan, 'Terverifikasi')) {
                // Ambil detail harga transaksi
                $detail_penabungan = $this->m_penabungan->get_harga_penabungan($idpenabungan);
                if ($detail_penabungan) {
                    // Tambahkan harga ke saldo tabungan nasabah
                    $this->m_nasabah->tambah_saldo($detail_penabungan->idnasabah, $detail_penabungan->harga);
                    $this->session->set_flashdata('success', 'Transaksi berhasil diverifikasi dan saldo nasabah telah diperbarui.');
					
					// --- LOG AKTIVITAS: Verifikasi Transaksi Penabungan Berhasil ---
                    $log_aktivitas_text = 'Verifikasi Transaksi Berhasil'; // Kategori aktivitas
                    // *** PERUBAHAN DI SINI: Menyederhanakan deskripsi log ***
                    $log_deskripsi_final = 'Pengguna berhasil memverifikasi transaksi penabungan ID: ' . $idpenabungan . '.';
                    
                    $this->m_logaktivitas->log_aktivitas(
                        $user_email,
                        $log_aktivitas_text,
                        $log_deskripsi_final
                    );
                    // --- END LOG AKTIVITAS ---
					
                } else {
                    $this->session->set_flashdata('error', 'Gagal mendapatkan detail harga transaksi.');
					
					// --- LOG AKTIVITAS: Verifikasi Transaksi Penabungan Gagal (Detail Harga) ---
                    $log_aktivitas_text = 'Verifikasi Transaksi Gagal';
                    $log_deskripsi_final = 'Pengguna gagal mendapatkan detail harga saat memverifikasi transaksi penabungan ID: ' . $idpenabungan . '.'; // Cukup sampai ID penabungan
                    
                    $this->m_logaktivitas->log_aktivitas(
                        $user_email,
                        $log_aktivitas_text,
                        $log_deskripsi_final
                    );
                    // --- END LOG AKTIVITAS ---
					
                }
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui status transaksi.');
				
				// --- LOG AKTIVITAS: Verifikasi Transaksi Penabungan Gagal (Update Status) ---
                $log_aktivitas_text = 'Verifikasi Transaksi Gagal';
                $log_deskripsi_final = 'Pengguna gagal memperbarui status transaksi penabungan ID: ' . $idpenabungan . ' menjadi "Terverifikasi".';
                
                $this->m_logaktivitas->log_aktivitas(
                    $user_email,
                    $log_aktivitas_text,
                    $log_deskripsi_final
                );
                // --- END LOG AKTIVITAS ---
				
            }
        } else {
            $this->session->set_flashdata('warning', 'Transaksi tidak ditemukan atau sudah diverifikasi/dibatalkan.');
			
			
            // --- LOG AKTIVITAS: Verifikasi Transaksi Penabungan Gagal (Kondisi Tidak Memenuhi) ---
            $status_sekarang = $penabungan ? $penabungan->status : 'Tidak Ditemukan';
            $log_aktivitas_text = 'Verifikasi Transaksi Gagal';
            $log_deskripsi_final = 'Pengguna mencoba verifikasi transaksi penabungan ID: ' . $idpenabungan . ' gagal. Kondisi tidak memenuhi syarat.'; // Deskripsi sampai 'Kondisi tidak memenuhi syarat'
            
            $this->m_logaktivitas->log_aktivitas(
                $user_email,
                $log_aktivitas_text,
                $log_deskripsi_final
            );
            // --- END LOG AKTIVITAS ---
			
        }
        redirect('penabungan');
    }
    
    public function batal_verifikasi($idpenabungan)
    {
		$user_email = $this->session->userdata('email'); //Tambahkan untuk LOGAKTIVITAS
        $penabungan = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
        if ($penabungan && $penabungan->status == 'Belum Diverifikasi') {
            if ($this->m_penabungan->update_status($idpenabungan, 'Ditolak')) {
                $this->session->set_flashdata('success', 'Transaksi berhasil dibatalkan.');
				
				// --- LOG AKTIVITAS: Batalkan Verifikasi Transaksi Penabungan Berhasil ---
                $log_aktivitas_text = 'Verifikasi Transaksi Dibatalkan'; // Kategori aktivitas
                
                // *** PERUBAHAN DI SINI: Menyederhanakan deskripsi log ***
                $log_deskripsi_final = 'Pengguna berhasil membatalkan (menolak) transaksi penabungan ID: ' . $idpenabungan . '.';
                
                $this->m_logaktivitas->log_aktivitas(
                    $user_email,
                    $log_aktivitas_text,
                    $log_deskripsi_final
                );
                // --- END LOG AKTIVITAS ---
				
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui status transaksi.');
				
				// --- LOG AKTIVITAS: Batalkan Verifikasi Transaksi Penabungan Gagal (Update Status) ---
                $log_aktivitas_text = 'Gagal Batalkan Verifikasi Transaksi';
                $log_deskripsi_final = 'Pengguna gagal memperbarui status transaksi penabungan ID: ' . $idpenabungan . ' menjadi "Ditolak".'; // Deskripsi hanya sampai "Ditolak"

                $this->m_logaktivitas->log_aktivitas(
                    $user_email,
                    $log_aktivitas_text,
                    $log_deskripsi_final
                );
            // --- END LOG AKTIVITAS ---
				
            }
        } else {
            $this->session->set_flashdata('warning', 'Transaksi tidak ditemukan atau sudah diverifikasi/dibatalkan.');
			
			// --- LOG AKTIVITAS: Batalkan Verifikasi Transaksi Penabungan Gagal (Kondisi Tidak Memenuhi) ---
            $status_sekarang = $penabungan ? $penabungan->status : 'Tidak Ditemukan';
            $log_aktivitas_text = 'Gagal Batalkan Verifikasi Transaksi';
            $log_deskripsi_final = 'Pengguna mencoba membatalkan transaksi penabungan ID: ' . $idpenabungan . ' gagal. Kondisi tidak memenuhi syarat.'; // Deskripsi hanya sampai "Kondisi tidak memenuhi syarat"

            $this->m_logaktivitas->log_aktivitas(
                $user_email,
                $log_aktivitas_text,
                $log_deskripsi_final
            );
        // --- END LOG AKTIVITAS ---
			
        }
        redirect('penabungan');
    }

        // --- Fungsi untuk CRUD Penabungan Offline ---

        public function tambah()
        {
			
		// --- LOG AKTIVITAS: Mengakses Form Tambah Penabungan ---
       // $user_email = $this->session->userdata('email');
        //$this->m_logaktivitas->log_aktivitas(
          //  $user_email,
            //$user_email,
         //   'Mengakses Form Tambah Penabungan',
            //'Pengguna mengakses halaman untuk menambah transaksi penabungan baru.'
        //);
        // --- END LOG AKTIVITAS ---
			
            $isi['content'] = 'penabungan/form_penabungan';
            $isi['judul'] = 'Tambah Transaksi Penabungan';
            $isi['data_nasabah'] = $this->m_nasabah->get_all_nasabah(); // Untuk dropdown pilihan nasabah
            $isi['data_sampah'] = $this->m_sampah->get_all_sampah(); // Untuk dropdown pilihan sampah
            $this->load->view('v_dashboard', $isi);
        }
    
        public function simpan()
        {
            $idnasabah = $this->input->post('idnasabah');
            $idsampah = $this->input->post('idsampah');
            $berat = $this->input->post('berat');
            $harga = $this->input->post('harga'); // Ambil harga dari input tersembunyi
			$user_email = $this->session->userdata('email'); // Variabel ini perlu diambil untuk log
        
            // Konfigurasi upload gambar (jika ada)
            $upload_gambar = null;
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = './uploads/gambar_penabungan/'; // Buat folder 'penabungan' di 'uploads'
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = 'PENABUNGAN_' . time(); // Nama file unik
                $this->load->library('upload', $config);
        
                if ($this->upload->do_upload('gambar')) {
                    $upload_gambar = $this->upload->data('file_name');
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengupload gambar: ' . $this->upload->display_errors());
					
			 // --- LOG AKTIVITAS: Gagal Upload Gambar Penabungan (Simpan) ---
            //$this->m_logaktivitas->log_aktivitas(
              //  $user_email,
                //'Gagal Upload Gambar Penabungan', // Kategori aktivitas
                //'Pengguna gagal mengupload gambar saat mencoba menyimpan transaksi penabungan baru. Pesan: ' . $this->upload->display_errors()
            //);
            // --- END LOG AKTIVITAS ---
					
                    redirect('penabungan/tambah');
                    return;
                }
            }
        
            $data = array(
                'idnasabah' => $idnasabah,
                'idsampah' => $idsampah,
                'tanggal' => date('Y-m-d H:i:s'),
                'berat' => $berat,
                'harga' => $harga, // Gunakan harga yang dihitung JavaScript
                'status' => 'Belum Diverifikasi', // Set status default menjadi 'Belum Diverifikasi'
                'gambar' => $upload_gambar // Simpan nama file gambar (jika ada)
            );
        
            if ($this->m_penabungan->insert_penabungan($data)) {
                $this->session->set_flashdata('success', 'Transaksi penabungan berhasil ditambahkan dan menunggu verifikasi.');
				
		// --- LOG AKTIVITAS: Menambah Transaksi Penabungan Berhasil ---
        $nasabah_info = $this->m_nasabah->getNasabahById($idnasabah);
        $sampah_info = $this->m_sampah->get_sampah_by_id($idsampah);
        $nasabah_nama = $nasabah_info ? $nasabah_info->username : 'N/A';
        $sampah_nama = $sampah_info ? $sampah_info->jenis : 'N/A';

        // Teks untuk kolom 'aktivitas' (lebih singkat)
        $log_aktivitas_text = 'Menambah Transaksi Penabungan';

        // Teks untuk kolom 'deskripsi' (lebih detail)
        $log_deskripsi_text = 'Transaksi baru oleh Nasabah: ' . $nasabah_nama . ' (ID: ' . $idnasabah . '), ' .
                              'Jenis Sampah: ' . $sampah_nama . ' (ID: ' . $idsampah . '), ' .
                              'Berat: ' . $berat . ' kg, ' .
                              'Harga: Rp ' . number_format($harga, 0, ',', '.') . '. Status: Belum Diverifikasi.';

        $this->m_logaktivitas->log_aktivitas(
            $user_email,
            $log_aktivitas_text, // Mengirim teks aktivitas yang lebih singkat
            $log_deskripsi_text  // Mengirim teks deskripsi yang detail
        );
        // --- END LOG AKTIVITAS ---
				
                redirect('penabungan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan transaksi penabungan.');
				
		// --- LOG AKTIVITAS: Gagal Menambah Transaksi Penabungan ---
        $nasabah_info = $this->m_nasabah->getNasabahById($idnasabah);
        $sampah_info = $this->m_sampah->get_sampah_by_id($idsampah);
        $nasabah_nama = $nasabah_info ? $nasabah_info->username : 'N/A';
        $sampah_nama = $sampah_info ? $sampah_info->jenis : 'N/A';

        // Teks untuk kolom 'aktivitas' (lebih singkat)
        $log_aktivitas_text = 'Gagal Menambah Transaksi Penabungan';

        // Teks untuk kolom 'deskripsi' (lebih detail)
        $log_deskripsi_text = 'Pengguna gagal menambahkan transaksi penabungan baru untuk Nasabah: ' . $nasabah_nama . ' (ID: ' . $idnasabah . '), ' .
                              'Jenis Sampah: ' . $sampah_nama . ' (ID: ' . $idsampah . '), ' .
                              'Berat: ' . $berat . ' kg, ' .
                              'Harga: Rp ' . number_format($harga, 0, ',', '.') . '.';

        $this->m_logaktivitas->log_aktivitas(
            $user_email,
            $log_aktivitas_text, // Mengirim teks aktivitas yang lebih singkat
            $log_deskripsi_text  // Mengirim teks deskripsi yang detail
        );
        // --- END LOG AKTIVITAS ---
				
                redirect('penabungan/tambah');
            }
        }
    
        public function edit($idpenabungan)
        {
            $isi['content'] = 'penabungan/edit_penabungan';
            $isi['judul'] = 'Edit Transaksi Penabungan';
            $isi['penabungan'] = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
            $isi['data_nasabah'] = $this->m_nasabah->get_all_nasabah();
            $isi['data_sampah'] = $this->m_sampah->get_all_sampah();
            if (!$isi['penabungan']) {
                $this->session->set_flashdata('error', 'Data transaksi tidak ditemukan.');
                redirect('penabungan');
                return;
            }
            $this->load->view('v_dashboard', $isi);
        }
    
        /*public function update()
        {
            $idpenabungan = $this->input->post('idpenabungan');
            $idnasabah = $this->input->post('idnasabah');
            $idsampah = $this->input->post('idsampah');
            $berat = $this->input->post('berat');
            $harga = $this->input->post('harga'); // Ambil harga dari input
        
            $upload_gambar = null;
            // Cek apakah ada file gambar yang diunggah
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = './uploads/gambar_penabungan/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = 'PENABUNGAN_' . time();
                $this->load->library('upload', $config);
        
                if ($this->upload->do_upload('gambar')) {
                    $upload_gambar = $this->upload->data('file_name');
        
                    // Hapus gambar lama jika ada (opsional)
                    $penabungan_lama = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
                    if ($penabungan_lama && $penabungan_lama->gambar) {
                        $file_path = './uploads/gambar_penabungan/' . $penabungan_lama->gambar;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengupload gambar: ' . $this->upload->display_errors());
                    redirect('penabungan/edit/' . $idpenabungan);
                    return;
                }
            } else {
                // Jika tidak ada gambar baru, gunakan gambar lama
                $penabungan_lama = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
                $upload_gambar = $penabungan_lama ? $penabungan_lama->gambar : null;
            }
        
            $data = array(
                'idnasabah' => $idnasabah,
                'idsampah' => $idsampah,
                'berat' => $berat,
                'harga' => $harga,
                'gambar' => $upload_gambar
            );
        
            if ($this->m_penabungan->update_penabungan($idpenabungan, $data)) {
                $this->session->set_flashdata('success', 'Transaksi penabungan berhasil diperbarui.');
                redirect('penabungan');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui transaksi penabungan.');
                redirect('penabungan/edit/' . $idpenabungan);
            }
        } */
	
	    public function update()
    {

    $idpenabungan = $this->input->post('idpenabungan');
    $idnasabah_new = $this->input->post('idnasabah'); // ID Nasabah BARU dari form
    $idsampah_new = $this->input->post('idsampah');   // ID Sampah BARU dari form
    $berat_new = $this->input->post('berat');         // Berat BARU dari form
    $harga_new = $this->input->post('harga');         // Harga BARU dari form
    $user_email = $this->session->userdata('email');

    // --- PENTING: Ambil data penabungan LAMA sebelum ada perubahan ---
    $penabungan_lama = $this->m_penabungan->get_penabungan_by_id($idpenabungan);

    // Persiapan untuk log: Ambil info nasabah & sampah LAMA
    $nasabah_info_lama = null;
    $nasabah_nama_lama = 'N/A';
    if ($penabungan_lama && $penabungan_lama->idnasabah) {
        $nasabah_info_lama = $this->m_nasabah->getNasabahById($penabungan_lama->idnasabah);
        $nasabah_nama_lama = $nasabah_info_lama ? $nasabah_info_lama->username : 'N/A';
    }

    $sampah_info_lama = null;
    $sampah_nama_lama = 'N/A';
    if ($penabungan_lama && $penabungan_lama->idsampah) {
        $sampah_info_lama = $this->m_sampah->get_sampah_by_id($penabungan_lama->idsampah);
        $sampah_nama_lama = $sampah_info_lama ? $sampah_info_lama->jenis : 'N/A';
    }

    // --- LOG GAGAL UPLOAD GAMBAR (tetap sama, tetapi parameter M_logaktivitas diperbaiki) ---
    $upload_gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $config['upload_path'] = './uploads/gambar_penabungan/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['file_name'] = 'PENABUNGAN_' . time();
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('gambar')) {
            $upload_gambar = $this->upload->data('file_name');

            if ($penabungan_lama && $penabungan_lama->gambar) {
                $file_path = './uploads/gambar_penabungan/' . $penabungan_lama->gambar;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Gagal mengupload gambar: ' . $this->upload->display_errors());

            $this->m_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Upload Gambar Penabungan (Update)', // Aktivitas singkat
                'Pengguna gagal mengupload gambar saat mencoba memperbarui transaksi penabungan ID: ' . $idpenabungan . '. Pesan: ' . $this->upload->display_errors()
            );
            redirect('penabungan/edit/' . $idpenabungan);
            return;
        }
    } else {
        $upload_gambar = $penabungan_lama ? $penabungan_lama->gambar : null;
    }
    // --- AKHIR LOG GAGAL UPLOAD GAMBAR ---

    $data_for_db = array(
        'idnasabah' => $idnasabah_new,
        'idsampah' => $idsampah_new,
        'berat' => $berat_new,
        'harga' => $harga_new,
        'gambar' => $upload_gambar
    );

    if ($this->m_penabungan->update_penabungan($idpenabungan, $data_for_db)) {
        $this->session->set_flashdata('success', 'Transaksi penabungan berhasil diperbarui.');

        // --- LOG AKTIVITAS: Memperbarui Transaksi Penabungan Berhasil ---
        // Ambil info nasabah & sampah BARU (setelah update)
        $nasabah_info_new = $this->m_nasabah->getNasabahById($idnasabah_new);
        $nasabah_nama_new = $nasabah_info_new ? $nasabah_info_new->username : 'N/A';

        $sampah_info_new = $this->m_sampah->get_sampah_by_id($idsampah_new);
        $sampah_nama_new = $sampah_info_new ? $sampah_info_new->jenis : 'N/A';

        $deskripsi_log = 'Berhasil memperbarui transaksi penabungan ID: ' . $idpenabungan . '. ';
        $changes = [];

        // Perbandingan field demi field
        if ($idnasabah_new !== ($penabungan_lama ? $penabungan_lama->idnasabah : null)) {
            $changes[] = 'Nasabah: "' . $nasabah_nama_lama . '" (ID: ' . ($penabungan_lama ? $penabungan_lama->idnasabah : 'N/A') . ') -> "' . $nasabah_nama_new . '" (ID: ' . $idnasabah_new . ')';
        }
        if ($idsampah_new !== ($penabungan_lama ? $penabungan_lama->idsampah : null)) {
            $changes[] = 'Jenis Sampah: "' . $sampah_nama_lama . '" (ID: ' . ($penabungan_lama ? $penabungan_lama->idsampah : 'N/A') . ') -> "' . $sampah_nama_new . '" (ID: ' . $idsampah_new . ')';
        }
        if ($berat_new !== ($penabungan_lama ? $penabungan_lama->berat : null)) {
            $changes[] = 'Berat: "' . number_format(($penabungan_lama ? $penabungan_lama->berat : 0), 2, ',', '.') . ' kg" -> "' . number_format($berat_new, 2, ',', '.') . ' kg"';
        }
        if ($harga_new !== ($penabungan_lama ? $penabungan_lama->harga : null)) {
            $changes[] = 'Harga: "Rp ' . number_format(($penabungan_lama ? $penabungan_lama->harga : 0), 0, ',', '.') . '" -> "Rp ' . number_format($harga_new, 0, ',', '.') . '"';
        }
        // Jika ada perubahan gambar
        if ($upload_gambar && $penabungan_lama && $upload_gambar !== $penabungan_lama->gambar) {
            $changes[] = 'Gambar: Diperbarui';
        } elseif ($upload_gambar && !$penabungan_lama->gambar) { // Jika sebelumnya tidak ada gambar, sekarang ada
             $changes[] = 'Gambar: Ditambahkan';
        }


        if (!empty($changes)) {
            $deskripsi_log .= 'Detail perubahan: ' . implode(', ', $changes) . '.';
        } else {
            $deskripsi_log .= 'Tidak ada perubahan data transaksi yang terdeteksi.';
        }

        $this->m_logaktivitas->log_aktivitas(
            $user_email,
            'Update Transaksi Penabungan', // Kategori aktivitas
            $deskripsi_log                 // Deskripsi detail
        );
        // --- END LOG AKTIVITAS ---

        redirect('penabungan');
    } else {
        $this->session->set_flashdata('error', 'Gagal memperbarui transaksi penabungan.');

        // --- LOG AKTIVITAS: Gagal Memperbarui Transaksi Penabungan ---
        $log_aktivitas_text = 'Gagal Memperbarui Transaksi Penabungan';
        $log_deskripsi_text = 'Pengguna gagal memperbarui transaksi penabungan ID: ' . $idpenabungan . '. Kemungkinan masalah database. Data lama: Nasabah: ' . $nasabah_nama_lama . ', Sampah: ' . $sampah_nama_lama . ', Berat: ' . ($penabungan_lama ? $penabungan_lama->berat : 'N/A') . ' kg, Harga: Rp ' . number_format(($penabungan_lama ? $penabungan_lama->harga : 0), 0, ',', '.') . '.';

        $this->m_logaktivitas->log_aktivitas(
            $user_email,
            $log_aktivitas_text,
            $log_deskripsi_text
        );
        // --- END LOG AKTIVITAS ---

        redirect('penabungan/edit/' . $idpenabungan);
    }
}
    
        public function hapus($idpenabungan)
        {
            // Ambil data penabungan yang akan dihapus
            $penabungan = $this->m_penabungan->get_penabungan_by_id($idpenabungan);
			$user_email = $this->session->userdata('email'); // Mengambil user email untuk log
        
            if ($penabungan) {
                $idnasabah = $penabungan->idnasabah;
                $harga_dihapus = $penabungan->harga; // Harga transaksi yang dihapus
                $status_penabungan = $penabungan->status; // Ambil status transaksi
        
                if ($this->m_penabungan->delete_penabungan($idpenabungan)) {
                    // Hanya kurangi saldo jika status penabungan adalah "Terverifikasi"
                    if ($status_penabungan == 'Terverifikasi') {
                        $this->m_nasabah->kurangi_saldo($idnasabah, $harga_dihapus);
                        $this->session->set_flashdata('success', 'Transaksi penabungan berhasil dihapus dan saldo nasabah telah dikurangi.');
						
					// --- LOG AKTIVITAS: Menghapus Transaksi Penabungan (Saldo Dikurangi) ---
                    $log_aktivitas_text = 'Hapus Transaksi Penabungan'; // Kategori aktivitas singkat
                    // *** PERUBAHAN DI SINI: Menyederhanakan deskripsi log ***
                    $log_deskripsi_final = 'Pengguna berhasil menghapus transaksi penabungan ID: ' . $idpenabungan . '.'; 
                    
                    // **PERBAIKAN**: Hanya 3 parameter untuk log_aktivitas
                    $this->m_logaktivitas->log_aktivitas(
                        $user_email,
                        $log_aktivitas_text,
                        $log_deskripsi_final
                    );
                    // --- END LOG AKTIVITAS ---
						
                    } else {
                        $this->session->set_flashdata('success', 'Transaksi penabungan berhasil dihapus.');
						
					// --- LOG AKTIVITAS: Menghapus Transaksi Penabungan (Saldo Tidak Dikurangi) ---
                    $log_aktivitas_text = 'Hapus Transaksi Penabungan'; // Kategori aktivitas singkat
                    // *** PERUBAHAN DI SINI: Menyederhanakan deskripsi log ***
                    $log_deskripsi_final = 'Pengguna berhasil menghapus transaksi penabungan ID: ' . $idpenabungan . '.';
                    
                    // **PERBAIKAN**: Hanya 3 parameter untuk log_aktivitas
                    $this->m_logaktivitas->log_aktivitas(
                        $user_email,
                        $log_aktivitas_text,
                        $log_deskripsi_final
                    );
                    // --- END LOG AKTIVITAS ---
						
                    }
                } else {
                    $this->session->set_flashdata('error', 'Gagal menghapus transaksi penabungan.');
					
				// --- LOG AKTIVITAS: Gagal Menghapus Transaksi Penabungan (Gagal Delete DB) ---
                $log_aktivitas_text = 'Gagal Hapus Transaksi Penabungan';
                $log_deskripsi_final = 'Pengguna gagal menghapus transaksi penabungan ID: ' . $idpenabungan . 
                                       ' (Nasabah: ' . $nasabah_nama . ', Sampah: ' . $sampah_nama . ', Status: ' . $status_penabungan . ').';
                
                // **PERBAIKAN**: Hanya 3 parameter untuk log_aktivitas
                $this->m_logaktivitas->log_aktivitas(
                    $user_email,
                    $log_aktivitas_text,
                    $log_deskripsi_final
                );
                // --- END LOG AKTIVITAS ---
					
                }
            } else {
                $this->session->set_flashdata('warning', 'Data transaksi penabungan tidak ditemukan.');
				
			// --- LOG AKTIVITAS: Gagal Menghapus Transaksi Penabungan (Data Tidak Ditemukan) ---
            $log_aktivitas_text = 'Gagal Hapus Transaksi Penabungan';
            $log_deskripsi_final = 'Pengguna mencoba menghapus transaksi penabungan ID: ' . $idpenabungan . ' tetapi data tidak ditemukan.';
            
            // **PERBAIKAN**: Hanya 3 parameter untuk log_aktivitas
            $this->m_logaktivitas->log_aktivitas(
                $user_email,
                $log_aktivitas_text,
                $log_deskripsi_final
            );
            // --- END LOG AKTIVITAS ---
				
            }
            redirect('penabungan');
        }

        public function saldo($idnasabah)
{
    // Load model M_nasabah jika belum diload di __construct
    $this->load->model('m_nasabah');
    $this->load->model('m_penabungan'); // Pastikan model penabungan juga diload

    // Ambil informasi detail nasabah
    $detail_nasabah = $this->m_nasabah->getNasabahById($idnasabah);

    // Ambil total saldo terverifikasi nasabah
    $total_saldo = $this->m_nasabah->getDirectSaldoTabunganById($idnasabah);

    // Ambil riwayat transaksi penabungan yang sudah terverifikasi untuk nasabah ini
    $riwayat_penabungan = $this->m_penabungan->get_penabungan_by_nasabah_dan_status($idnasabah, 'Terverifikasi');

    if ($detail_nasabah) {
        $isi['content'] = 'penabungan/v_saldo_nasabah'; // Nama view yang akan kita buat
        $isi['judul'] = '';
        $isi['nasabah'] = $detail_nasabah;
        $isi['total_saldo'] = $total_saldo;
        $isi['riwayat_penabungan'] = $riwayat_penabungan;
        $this->load->view('v_dashboard', $isi); // Memuat layout dashboard dan mengisi bagian 'content' dengan view saldo
    } else {
        $this->session->set_flashdata('error', 'Data nasabah tidak ditemukan.');
        redirect('nasabah');
    }
}
}