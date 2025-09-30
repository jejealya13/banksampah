<?php

defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Api extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('m_sampah');
        $this->load->model('m_penabungan');
        $this->load->model('m_nasabah');
        $this->load->model('m_penarikan');
        $this->load->library('form_validation'); // Load library form_validation
        //$this->load->library('uploads'); // Pastikan library upload di-load di sini
    }

    // Endpoint untuk registrasi nasabah
    public function register() {
        // Set header untuk respons JSON (sudah ada di atas, tapi bisa diulang untuk memastikan)
        $this->output->set_content_type('application/json');

        // Pastikan ini adalah request POST
        if ($this->input->method() !== 'post') {
            $response = array('status' => 'failed', 'message' => 'Metode request tidak diizinkan. Gunakan POST.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // --- PERBAIKAN PENTING DI SINI ---
        // Karena Android mengirim application/x-www-form-urlencoded, gunakan $this->input->post()
        $username = $this->input->post('username');
        $phone = $this->input->post('phone');
        $password = $this->input->post('password');
        $address = $this->input->post('address');
        $birthdate = $this->input->post('birthdate'); // Format dari Android: DD/MM/YYYY

        // --- DEBUGGING (Opsional, sangat disarankan saat pengembangan) ---
        log_message('debug', 'Register API - POST Data: ' . print_r($this->input->post(), true));
        // --- AKHIR DEBUGGING ---

        // Validasi input
        if (empty($username) || empty($phone) || empty($password) || empty($address) || empty($birthdate)) {
            $response = array('status' => 'failed', 'message' => 'Semua field harus diisi.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Validasi format nomor telepon (opsional, tapi bagus untuk konsistensi)
        $phone_cleaned = preg_replace('/[^0-9+]/', '', $phone);
        if (!preg_match('/^\+62\d{9,13}$/', $phone_cleaned)) {
            $response = array('status' => 'failed', 'message' => 'Format nomor telepon tidak valid. Gunakan format internasional (+62...).');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Konversi format tanggal lahir dari DD/MM/YYYY ke YYYY-MM-DD untuk database
        $tanggal_objek = DateTime::createFromFormat('d/m/Y', $birthdate);
        if ($tanggal_objek) {
            $birthdate_database = $tanggal_objek->format('Y-m-d');
        } else {
            $response = array('status' => 'failed', 'message' => 'Format tanggal lahir tidak valid (pastikan DD/MM/YYYY).');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah nomor telepon sudah terdaftar
        if ($this->m_nasabah->is_phone_exists($phone_cleaned)) {
            $response = array('status' => 'failed', 'message' => 'Nomor telepon sudah terdaftar. Gunakan nomor lain atau coba login.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Generate ID nasabah baru
        $new_idnasabah = $this->m_nasabah->idnasabah();
        if ($new_idnasabah === null) {
            $response = array('status' => 'failed', 'message' => 'Gagal membuat ID nasabah baru.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Siapkan data untuk model
        $data_to_insert = array(
            'idnasabah'     => $new_idnasabah,
            'namanasabah'   => $username,
            'username'      => $username,
            'phone'         => $phone_cleaned,
            'password'      => $hashed_password,
            'address'       => $address,
            'birthdate'     => $birthdate_database,
            'tabungan'      => 0,
            'tanggal_daftar'=> date('Y-m-d')
        );

        // Panggil model untuk menyimpan data
        $registration_success = $this->m_nasabah->register_nasabah($data_to_insert);

        if ($registration_success) {
            $response = array(
                'status' => 'success',
                'message' => 'Registrasi berhasil!',
                'idnasabah' => $new_idnasabah,
                'username' => $username,
                'phone' => $phone_cleaned
            );
        } else {
            $response = array('status' => 'failed', 'message' => 'Registrasi gagal. Terjadi kesalahan database.');
            log_message('error', 'Database Error during registration: ' . $this->db->error()['message']);
        }

        $this->output->set_output(json_encode($response));
    }

    // --- Endpoint untuk Login Nasabah ---
    public function login() {
        $this->output->set_content_type('application/json');

        if ($this->input->method() !== 'post') {
            $response = array('status' => 'failed', 'message' => 'Metode request tidak diizinkan. Gunakan POST.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Ambil SEMUA input yang dikirim dari Android
        $username_input = $this->input->post('username'); // <-- Ambil username dari input
        $phone_input = $this->input->post('phone');
        $password_input = $this->input->post('password');

        log_message('debug', 'Login API - POST Data: ' . print_r($this->input->post(), true));

        if (empty($username_input) || empty($phone_input) || empty($password_input)) { // Tambahkan validasi username
            $response = array('status' => 'failed', 'message' => 'Username, nomor telepon, dan password harus diisi.');
            $this->output->set_output(json_encode($response));
            return;
        }

        // Ambil data nasabah berdasarkan USERNAME dan NOMOR TELEPON
        // Anda perlu membuat fungsi baru di m_nasabah untuk ini,
        // misalnya get_nasabah_by_username_and_phone($username, $phone)
        $nasabah = $this->m_nasabah->get_nasabah_by_username_and_phone($username_input, $phone_input);

        if ($nasabah) {
            $hashed_password_from_db = $nasabah->password;
            $login_success = false;
            $needs_rehash = false;

            // Verifikasi password seperti biasa
            if (password_verify($password_input, $hashed_password_from_db)) {
                $login_success = true;
                if (password_needs_rehash($hashed_password_from_db, PASSWORD_DEFAULT)) {
                    $needs_rehash = true;
                }
            } else {
                // ... (logika verifikasi MD5/SHA1 lama) ...
                $hash_length = strlen($hashed_password_from_db);
                if ($hash_length === 32 && ctype_xdigit($hashed_password_from_db)) {
                    if (md5($password_input) === $hashed_password_from_db) {
                        $login_success = true;
                        $needs_rehash = true;
                    }
                } elseif ($hash_length === 40 && ctype_xdigit($hashed_password_from_db)) {
                    if (sha1($password_input) === $hashed_password_from_db) {
                        $login_success = true;
                        $needs_rehash = true;
                    }
                }
            }

            if ($login_success) {
                if ($needs_rehash) {
                    $new_hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
                    $this->db->where('idnasabah', $nasabah->idnasabah);
                    $this->db->update('nasabah', ['password' => $new_hashed_password]);
                    log_message('debug', 'Password for nasabah ID ' . $nasabah->idnasabah . ' rehashed to Bcrypt.');
                }

                $response = array(
                    'status' => 'success',
                    'message' => 'Login berhasil!',
                    'idnasabah' => $nasabah->idnasabah,
                    'username' => $nasabah->username,
                    'phone' => $nasabah->phone,
                    'address' => $nasabah->address,
                    'birthdate' => $nasabah->birthdate,
                    'tabungan' => $nasabah->tabungan
                );
            } else {
                $response = array('status' => 'failed', 'message' => 'Password salah.');
            }
        } else {
            // Jika nasabah tidak ditemukan berdasarkan username DAN nomor telepon
            $response = array('status' => 'failed', 'message' => 'Username atau nomor telepon tidak terdaftar.');
        }

        $this->output->set_output(json_encode($response));
    }


    // --- METODE API LAINNYA (TIDAK ADA PERUBAHAN) ---

    public function harga_sampah() {
        $data = $this->m_sampah->get_all_harga_sampah();
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function tambah_penabungan()
    {
        // Terima data dari aplikasi mobile
        $idsampah = $this->input->post('idsampah');
        $idnasabah = $this->input->post('idnasabah');
        $berat = $this->input->post('berat');
        $waktu_pengantaran = $this->input->post('tanggal'); // Format dari mobile: DD/MM/YYYY
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('idnasabah', 'ID Nasabah', 'required');
        $this->form_validation->set_rules('idsampah', 'ID Sampah', 'required');
        $this->form_validation->set_rules('berat', 'Berat', 'required|numeric');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $response = array('status' => 'error', 'message' => validation_errors());
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return; // Hentikan proses jika validasi gagal
        }
        
        // Konversi format tanggal dari DD/MM/YYYY
        $tanggal_objek = DateTime::createFromFormat('d/m/Y', $waktu_pengantaran);
        if ($tanggal_objek) {
            $tanggal_database = $tanggal_objek->format('Y-m-d');
        } else {
            $response = array('status' => 'error', 'message' => 'Format tanggal tidak valid (pastikan DD/MM/YYYY).');
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }
        
        // Konfigurasi upload gambar
        $config['upload_path'] = './uploads/gambar_penabungan/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 10240;
        $config['file_name'] = 'penabungan_' . time();
        $config['overwrite'] = TRUE;
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('gambar')) {
            $upload_data = $this->upload->data();
            $nama_file_gambar = $upload_data['file_name'];
        
            // Ambil harga satuan dari model M_sampah
            $harga_satuan = $this->m_sampah->get_harga_by_id($idsampah);
            $total_harga = $berat * $harga_satuan;
        
            // Siapkan data untuk disimpan ke database
            $data = array(
                'idsampah' => $idsampah,
                'idnasabah' => $idnasabah,
                'tanggal' => $tanggal_database,
                'berat' => $berat,
                'gambar' => $nama_file_gambar,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'Belum Diverifikasi',
                'harga' => $total_harga
            );
        
            // Simpan data ke database
            if ($this->m_penabungan->insert_penabungan($data)) {
                $response = array('status' => 'success', 'message' => 'Data penabungan berhasil ditambahkan, tunggu verifikasi.');
            } else {
                $response = array('status' => 'error', 'message' => 'Gagal menambahkan data penabungan.');
            }
        } else {
            $error = $this->upload->display_errors();
            $response = array('status' => 'error', 'message' => 'Gagal mengupload gambar: ' . $error);
        }
        
        // Kirim response ke aplikasi mobile
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function get_saldo_tabungan() {
        $idnasabah = $this->input->post('idnasabah');
        if (empty($idnasabah)) {
            $response = array('status' => 'error', 'message' => 'ID Nasabah tidak boleh kosong.');
        } else {
            //perubahan
            $saldo = $this->m_nasabah->getDirectSaldoTabunganById($idnasabah);
            //
            $response = array('status' => 'success', 'saldo' => number_format($saldo, 0, ',', '.'));
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function buat_permintaan_penarikan() {
        // Set header agar respons selalu JSON
        $this->output->set_content_type('application/json');

        // Ambil raw input stream (berisi JSON body)
        $input_json = $this->input->raw_input_stream;

        // Dekode JSON menjadi objek atau array PHP
        $data = json_decode($input_json, true); // true untuk mendapatkan array asosiatif

        // --- DEBUGGING (Opsional, tapi sangat disarankan saat pengembangan) ---
        // Log data yang diterima untuk verifikasi
        log_message('debug', 'Raw Input JSON: ' . $input_json);
        log_message('debug', 'Decoded JSON Data: ' . print_r($data, true));
        // --- AKHIR DEBUGGING ---

        // Ambil data dari array $data yang sudah didekode
        // PASTIKAN NAMA KEY SAMA PERSIS DENGAN @SerializedName DI PenarikanRequest.java ANDA!
        $idnasabah = $data['idnasabah'] ?? null;
        $nominal = $data['nominal'] ?? null;
        $metode = $data['metode'] ?? null;
        $norek = $data['norek'] ?? null;

        // Validasi data yang diterima
        if (empty($idnasabah) || empty($nominal) || empty($metode) || empty($norek)) {
            $response = array('status' => 'error', 'message' => 'Semua field harus diisi.');
        } elseif (!is_numeric($nominal) || $nominal <= 0) {
            $response = array('status' => 'error', 'message' => 'Nominal harus berupa angka positif.');
        } else {
            // Siapkan data untuk disimpan ke database
            $data_db = array( // Ubah nama variabel agar tidak bentrok dengan $data dari json_decode
                'idnasabah' => $idnasabah,
                'nominal' => $nominal,
                'metode' => $metode,
                'norek' => $norek,
                'tanggal' => date('Y-m-d H:i:s'),
                'status' => 'Menunggu' // Status awal permintaan
            );

            // Simpan data menggunakan model m_penarikan
            if ($this->m_penarikan->insertPengajuanPenarikan($data_db)) { // Gunakan $data_db
                $response = array('status' => 'success', 'message' => 'Hai, Permintaan penarikan saldo berhasil diproses. Silahkan pantau WhatsApp untuk informasi penarikan saldo.');
            } else {
                $response = array('status' => 'error', 'message' => 'Gagal mengirim permintaan penarikan.');
            }
        }
        $this->output->set_output(json_encode($response));
    }


    // Endpoint untuk mendapatkan data profil nasabah
    public function get_user_profile() {
        $this->output->set_content_type('application/json');

        if ($this->input->method() !== 'post') {
            $response = array('status' => 'failed', 'message' => 'Metode request tidak diizinkan. Gunakan POST.');
            $this->output->set_output(json_encode($response));
            return;
        }

        $idnasabah = $this->input->post('idnasabah');

        if (empty($idnasabah)) {
            $response = array('status' => 'failed', 'message' => 'ID Nasabah diperlukan.');
            $this->output->set_output(json_encode($response));
            return;
        }

        $nasabah = $this->m_nasabah->get_nasabah_by_id($idnasabah); // Anda perlu membuat fungsi ini di model

        if ($nasabah) {
            $response = array(
                'status' => 'success',
                'message' => 'Data profil berhasil diambil.',
                'idnasabah' => $nasabah->idnasabah,
                'username' => $nasabah->username,
                'phone' => $nasabah->phone,
                'address' => $nasabah->address,
                'birthdate' => $nasabah->birthdate, // Akan dikembalikan dalam format YYYY-MM-DD
                'tabungan' => $nasabah->tabungan,
                'profile_pic_uri' => $nasabah->profile_pic_uri // Jika Anda menyimpan URI foto di DB
            );
        } else {
            $response = array('status' => 'failed', 'message' => 'Profil tidak ditemukan.');
        }

        $this->output->set_output(json_encode($response));
    }

    // Endpoint untuk memperbarui data profil nasabah
    public function update_user_profile() {
        $this->output->set_content_type('application/json');

        if ($this->input->method() !== 'post') {
            $response = array('status' => 'failed', 'message' => 'Metode request tidak diizinkan. Gunakan POST.');
            $this->output->set_output(json_encode($response));
            return;
        }

        $idnasabah = $this->input->post('idnasabah');
        $username = $this->input->post('username');
        $phone = $this->input->post('phone');
        $address = $this->input->post('address');
        $birthdate = $this->input->post('birthdate'); // Diharapkan YYYY-MM-DD dari Android
        $password = $this->input->post('password'); // Ini bisa null atau kosong jika tidak diubah

        if (empty($idnasabah) || empty($username) || empty($phone) || empty($address) || empty($birthdate)) {
            $response = array('status' => 'failed', 'message' => 'Semua field wajib diisi.');
            $this->output->set_output(json_encode($response));
            return;
        }

        $data_to_update = array(
            'username' => $username,
            'phone' => $phone,
            'address' => $address,
            'birthdate' => $birthdate
        );

        // Hanya update password jika dikirim (tidak null dan tidak kosong)
        if (!empty($password)) {
            // Hash password baru sebelum disimpan
            $data_to_update['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Panggil model untuk memperbarui data
        // Anda perlu membuat fungsi update_nasabah_profile di model
        $update_success = $this->m_nasabah->update_nasabah_profile($idnasabah, $data_to_update);

        if ($update_success) {
            $response = array('status' => 'success', 'message' => 'Profil berhasil diperbarui!');
        } else {
            $response = array('status' => 'failed', 'message' => 'Gagal memperbarui profil. Terjadi kesalahan database atau ID tidak ditemukan.');
            log_message('error', 'Database Error during profile update for ID ' . $idnasabah . ': ' . $this->db->error()['message']);
        }

        $this->output->set_output(json_encode($response));
    }
	
	// Endpoint baru untuk Upload Foto Profil
    public function upload_profile_picture() {
        $this->output->set_content_type('application/json');

        // Pastikan ini adalah request POST
        if ($this->input->method() !== 'post') {
            $response = array('status' => 'failed', 'message' => 'Metode request tidak diizinkan. Gunakan POST.');
            $this->output->set_output(json_encode($response));
            return;
        }
        // Ambil ID Nasabah dari POST request
        $idnasabah = $this->input->post('idnasabah'); // Sesuaikan dengan nama parameter dari Android

        if (empty($idnasabah)) {
            $response = array('status' => 'failed', 'message' => 'ID Nasabah tidak ditemukan.');
            $this->output->set_output(json_encode($response));
            return;
        }

        log_message('debug', 'Upload Profile Picture API - ID Nasabah: ' . $idnasabah);
        log_message('debug', 'Upload Profile Picture API - FILES Data: ' . print_r($_FILES, true));
        // --- AKHIR DEBUGGING ---

        // Konfigurasi Upload File
        $config['upload_path']   = './uploads/profile_pics/'; // <--- SESUAIKAN PATH INI
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']      = 10240; // 5MB (dalam KB)
        // Nama file unik berdasarkan ID Nasabah dan timestamp untuk menghindari konflik
        $config['file_name']     = 'profile_' . $idnasabah . '_' . time();
        $config['overwrite']     = TRUE; // Jika ingin menimpa file lama dengan nama yang sama
		
		

        // Pastikan folder upload ada
    // Ini penting! Jika folder tidak ada, upload akan gagal.
    if (!is_dir($config['upload_path'])) {
        // Coba buat folder. Jika gagal, log error atau berikan respons error.
        if (!mkdir($config['upload_path'], 0755, true)) {
            $response = array('status' => 'failed', 'message' => 'Direktori upload tidak dapat dibuat. Periksa izin server.');
            $this->output->set_output(json_encode($response));
            log_message('error', 'Failed to create upload directory: ' . $config['upload_path']);
            return;
        }
        log_message('debug', 'Upload directory created: ' . $config['upload_path']);
    }
       // Memuat library dan menginisialisasi ulang dengan config baru
    $this->load->library('upload', $config); // <--- Baris ini adalah jawabannya

        // Coba upload file. Parameter 'profile_pic' adalah nama field di form-data Android.
        if ( ! $this->upload->do_upload('profile_pic')) { // 'profile_pic' harus sesuai dengan nama field di Android
            $error = $this->upload->display_errors('', ''); // Ambil pesan error tanpa tag HTML
            $response = array('status' => 'failed', 'message' => 'Gagal mengunggah foto: ' . strip_tags($error));
            log_message('error', 'Upload Profile Picture API - Upload Error: ' . $error);
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            // Buat URL publik dari foto yang diunggah
            // Pastikan base_url() Anda sudah dikonfigurasi di config/config.php
            // Contoh: $config['base_url'] = 'https://banksampahalmubarok.pocari.id/';
            $profile_pic_uri = base_url($config['upload_path'] . $file_name);

            // Perbarui URL foto profil di database melalui model m_nasabah
            // Anda perlu membuat metode update_profile_pic_url di m_nasabah
            $update_success = $this->m_nasabah->update_profile_pic_uri($idnasabah, $profile_pic_uri);

            if ($update_success) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Foto profil berhasil diunggah dan diperbarui.',
                    'profile_pic_uri' => $profile_pic_uri // Kirim URL kembali ke aplikasi
                );
            } else {
                $response = array('status' => 'failed', 'message' => 'Foto berhasil diunggah, tetapi gagal memperbarui database.');
                // Opsional: Hapus file yang sudah terunggah jika update database gagal
                // unlink($upload_data['full_path']);
                log_message('error', 'Upload Profile Picture API - DB Update Error for ID ' . $idnasabah . ': ' . $this->db->error()['message']);
            }
        }

        $this->output->set_output(json_encode($response));
    }
}