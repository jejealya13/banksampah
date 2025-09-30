<?php

class Nasabah extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_nasabah');
        $this->load->model('m_penarikan'); // Ini yang penting!
        $this->load->library('form_validation'); // Tambahkan ini untuk validasi input
		$this->load->model('M_logaktivitas'); // <<< TAMBAH BARIS INI UNTUK MEMUAT MODEL LOG AKTIVITAS

        // Tambahkan pengecekan login di sini
        if (!$this->session->userdata('logged_in')) {
            redirect('login'); // Redirect ke halaman login
        }
    }

    public function index()
{
    $isi['content'] = 'nasabah/v_nasabah';
    $isi['judul'] = 'Daftar Data Nasabah';
    $isi['data_nasabah'] = $this->m_nasabah->get_all_nasabah();
    //$isi['total_tabungan_terverifikasi'] = $this->m_nasabah->getTotalTabunganTerverifikasiPerNasabah();
    $this->load->view('v_dashboard', $isi);
		
		    // --- LOG AKTIVITAS: Melihat Daftar Nasabah ---
       // $user_email = $this->session->userdata('email');
       // if ($user_email) { // Pastikan email user tersedia
         //    $this->M_logaktivitas->log_aktivitas(
           //     $user_email,
             //   'Melihat Daftar Nasabah',
               // 'Admin melihat daftar nasabah.'
            //);
       // }
    // --- END LOG AKTIVITAS ---
}
    public function tambah_nasabah()
    {
        $isi['content'] = 'nasabah/form_nasabah';
        $isi['judul']   = 'Form Tambah Nasabah';
        // KOREKSI: Gunakan 'idnasabah_baru' sesuai dengan yang diharapkan di form_nasabah.php
        $isi['idnasabah_baru'] = $this->m_nasabah->idnasabah();
        $this->load->view('v_dashboard', $isi);
		
		        // --- LOG AKTIVITAS: Mengakses Form Tambah Nasabah ---
        //$user_email = $this->session->userdata('email');
        //if ($user_email) {
          //  $this->M_logaktivitas->log_aktivitas(
            //    $user_email,
              //  'Mengakses Form Tambah Nasabah',
                //'Admin membuka form penambahan nasabah baru.'
            //);
       // }
        // --- END LOG AKTIVITAS ---
    }
	
    public function simpan()
    {
       // Set rules untuk validasi input dari form
        $this->form_validation->set_rules('namanasabah', 'Nama Nasabah', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|callback_no_space_username');
        $this->form_validation->set_rules('phone', 'Nomor Telepon', 'required|numeric|is_unique[nasabah.phone]'); // Cek unik di database
        $this->form_validation->set_rules('address', 'Alamat', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_valid_password_criteria'); // Pastikan ada input password
        $this->form_validation->set_rules('birthdate', 'Tanggal Lahir', 'required'); // Pastikan ada input tanggal lahir

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembalikan ke form dengan error
            $isi['content'] = 'nasabah/form_nasabah';
            $isi['judul']   = 'Form Tambah Nasabah';
            $isi['idnasabah_baru'] = $this->m_nasabah->idnasabah(); 
            $this->load->view('v_dashboard', $isi);
			
			// --- LOG AKTIVITAS: Gagal Simpan Nasabah (Validasi) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Simpan Nasabah',
                    'Admin gagal menyimpan nasabah baru karena validasi input: ' . validation_errors()
                );
            }
            // --- END LOG AKTIVITAS ---
			
        } else {
            // Ambil data dari POST
           // Ambil data dari POST (nama field sesuai DB/API)
            $idnasabah = $this->input->post('idnasabah');
            $namanasabah = $this->input->post('namanasabah');
            $username = $this->input->post('username'); // KOREKSI
            $phone = $this->input->post('phone', TRUE); // KOREKSI
            $address = $this->input->post('address'); // KOREKSI
            $password = $this->input->post('password');
            $birthdate = $this->input->post('birthdate'); // KOREKSI

            // --- START POINT 3: PEMROSESAN NOMOR TELEPON ---
            // 1. Hapus semua karakter non-digit
            $phone = preg_replace('/[^0-9]/', '', $phone);

            // 2. Jika nomor diawali '0', hapus '0' tersebut
            if (substr($phone, 0, 1) === '0') {
                $phone = substr($phone, 1);
            }
            
            // 3. Tambahkan prefix '+62'
            $formatted_phone = '+62' . $phone;
            // --- END POINT 3 ---

            // --- Lakukan validasi is_unique secara manual setelah nomor diformat ---
            // Cek apakah nomor telepon yang sudah diformat sudah ada di database
            if ($this->m_nasabah->is_phone_exists($formatted_phone)) {
                // Daripada set_flashdata, kita akan menambahkan error langsung ke form_validation
                $this->form_validation->set_message('manual_phone_unique', 'Nomor Telepon %s sudah terdaftar untuk nasabah lain. Silakan gunakan nomor lain.');
                $this->form_validation->set_rules('phone', 'Nomor Telepon', 'required|numeric|min_length[8]|max_length[15]|callback_manual_phone_unique');
                // Set the value back so it appears in the form
                $this->form_validation->set_value('phone', $this->input->post('phone'));
                $this->form_validation->run(); // Jalankan ulang validasi untuk menampilkan error

                // Langsung tampilkan kembali form dengan error
                $isi['content'] = 'nasabah/form_nasabah';
                $isi['judul']   = 'Form Tambah Nasabah';
                $isi['idnasabah_baru'] = set_value('idnasabah', $this->m_nasabah->idnasabah());  
                $this->load->view('v_dashboard', $isi);

                // LOG AKTIVITAS: Gagal Simpan Nasabah (Nomor Telepon Duplikat)
                $user_email = $this->session->userdata('email');
                if ($user_email) {
                    $this->M_logaktivitas->log_aktivitas(
                        $user_email,
                        'Gagal Simpan Nasabah',
                        'Admin gagal menyimpan nasabah baru karena nomor telepon duplikat: ' . $formatted_phone
                    );
                }
                return; // Penting: Hentikan eksekusi lebih lanjut
            }
            // --- END VALIDASI UNIK MANUAL ---

            // Hash password
            $hashed_password = md5($password); // Konsisten dengan API, atau gunakan password_hash()

            $data = array(
                'idnasabah'     => $idnasabah,
                'namanasabah'   => $namanasabah,
                'username'      => $username,
                'phone'         => $formatted_phone,
                'password'      => $hashed_password,
                'address'       => $address,
                'birthdate'     => $birthdate, // Masukkan tanggal lahir
                'tabungan'      => 0, // Default saldo awal
				'tanggal_daftar'=> date('Y-m-d') // Tambahkan tanggal daftar
            );

            // Gunakan metode register_nasabah() dari model untuk konsistensi
            // Meskipun is_phone_exists() sudah ada di model, validasi is_unique di controller lebih awal
            if ($this->m_nasabah->register_nasabah($data)) {
                $this->session->set_flashdata('info', 'Data berhasil di Simpan');
				
				// --- LOG AKTIVITAS: Berhasil Simpan Nasabah ---
                $user_email = $this->session->userdata('email');
                if ($user_email) {
                    $this->M_logaktivitas->log_aktivitas(
                        $user_email,
                        'Tambah Nasabah Baru',
                        'Berhasil menambahkan nasabah baru: ' . $username . ' (ID: ' . $idnasabah . ')'
                    );
                }
                // --- END LOG AKTIVITAS ---
				
                redirect('nasabah');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan data nasabah. Mungkin ada masalah database atau nomor telepon sudah terdaftar (walaupun sudah divalidasi awal).');
				
				// --- LOG AKTIVITAS: Gagal Simpan Nasabah (Database) ---
                $user_email = $this->session->userdata('email');
                if ($user_email) {
                    $this->M_logaktivitas->log_aktivitas(
                        $user_email,
                        'Gagal Simpan Nasabah',
                        'Admin gagal menyimpan nasabah baru: ' . $username . '. Masalah database atau nomor telepon sudah terdaftar.'
                    );
                }
                // --- END LOG AKTIVITAS ---
				
                redirect('nasabah/tambah_nasabah'); // Kembali ke form jika gagal
            }
        }
    }

    public function edit($id)
    {
        $isi['content'] = 'nasabah/edit_nasabah';
        $isi['judul']   = 'Form Edit Nasabah';
        $isi['data']   = $this->m_nasabah->edit($id);
        $this->load->view('v_dashboard', $isi);
		
		        // --- LOG AKTIVITAS: Mengakses Form Edit Nasabah ---
       // $user_email = $this->session->userdata('email');
        //if ($user_email) {
          //  $nasabah_info = $this->m_nasabah->edit($id);
            //$nasabah_name = isset($nasabah_info->username) ? $nasabah_info->username : 'Tidak Diketahui';
            //$this->M_logaktivitas->log_aktivitas(
              //  $user_email,
                //'Mengakses Form Edit Nasabah',
                //'Admin membuka form edit untuk nasabah: ' . $nasabah_name . ' (ID: ' . $id . ').'
            //);
        //}
        // --- END LOG AKTIVITAS ---
    }

    /*public function update()
    {
        $idnasabah = $this->input->post('idnasabah');

        // Aturan validasi
        $this->form_validation->set_rules('username', 'Nama', 'required'); // KOREKSI: 'username'
        // Callback function for unique phone number validation during edit
        // Pastikan callback_unique_phone_edit di bawah juga menggunakan 'phone'
        $this->form_validation->set_rules('phone', 'Nomor Telepon', 'required|numeric|callback_unique_phone_edit['.$idnasabah.']'); // KOREKSI: 'phone'
        $this->form_validation->set_rules('address', 'Alamat', 'required'); // KOREKSI: 'address'
        $this->form_validation->set_rules('birthdate', 'Tanggal Lahir', 'required|date'); // KOREKSI: 'birthdate'
        // Password tidak wajib diisi saat update, tapi jika diisi harus valid
        $this->form_validation->set_rules('password', 'Password', 'min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembalikan ke form edit
            $isi['content'] = 'nasabah/edit_nasabah';
            $isi['judul']   = 'Form Edit Nasabah';
            $isi['data']    = $this->m_nasabah->edit($idnasabah); // Load data kembali
            $this->load->view('v_dashboard', $isi);
        } else {
            $data = array(
                'username'      => $this->input->post('username'), // KOREKSI
                'phone'         => $this->input->post('phone'), // KOREKSI
                'address'       => $this->input->post('address'), // KOREKSI
                'birthdate'     => $this->input->post('birthdate') // KOREKSI
            );

            // Opsional: Jika Anda ingin admin bisa mengubah password, tambahkan logika ini
            // Pastikan ada field input 'password' di form edit_nasabah
            if ($this->input->post('password')) {
                $data['password'] = md5($this->input->post('password'));
            }

            $query = $this->m_nasabah->update($idnasabah, $data);
            if ($query) { // Perbaiki kondisi dari = true menjadi hanya $query
                $this->session->set_flashdata('info', 'Data berhasil di Update');
                redirect('nasabah');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengupdate data nasabah.');
                redirect('nasabah/edit/' . $idnasabah);
            }
        }
    }*/
	
	    public function update()
    {
        $idnasabah = $this->input->post('idnasabah');

        // --- PENTING: Ambil data nasabah lama sebelum validasi atau update ---
        // $nasabah_lama sekarang adalah ARRAY (sesuai M_nasabah->edit() Anda)
        $nasabah_lama = $this->m_nasabah->edit($idnasabah);

        // Aturan validasi (TIDAK BERUBAH DARI KODE ANDA YANG TERAKHIR DIBERIKAN)
        $this->form_validation->set_rules('namanasabah', 'Nama Nasabah', 'required');
        $this->form_validation->set_rules('username', 'Nama', 'required|callback_no_space_username');
        $this->form_validation->set_rules('phone', 'Nomor Telepon', 'required|numeric|min_length[8]|max_length[15]'); 
        $this->form_validation->set_rules('address', 'Alamat', 'required');
        $this->form_validation->set_rules('birthdate', 'Tanggal Lahir', 'required|date'); 
        // --- START PERUBAHAN UTAMA UNTUK PASSWORD DI UPDATE ---
        // Password hanya divalidasi jika diisi
        if (!empty($this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]|callback_valid_password_criteria');
        }
        // --- END PERUBAHAN UTAMA UNTUK PASSWORD DI UPDATE ---

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembalikan ke form edit
            $isi['content'] = 'nasabah/edit_nasabah';
            $isi['judul']   = 'Form Edit Nasabah';
            $isi['data']    = $nasabah_lama; // $nasabah_lama adalah ARRAY, ini juga sudah benar untuk view
            $this->load->view('v_dashboard', $isi);

            // --- LOG AKTIVITAS: Gagal Update Nasabah (Validasi) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                // Akses properti sebagai array karena $nasabah_lama adalah array
                $nasabah_name = (isset($nasabah_lama['username'])) ? $nasabah_lama['username'] : 'ID ' . $idnasabah;
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Update Nasabah',
                    'Admin gagal memperbarui nasabah: ' . $nasabah_name . ' (ID: ' . $idnasabah . ') karena validasi input: ' . validation_errors()
                );
            }
            // --- END LOG AKTIVITAS ---

        } else {
            // Ambil data yang dikirim dari form
            $new_namanasabah = $this->input->post('namanasabah');
            $new_username  = $this->input->post('username');
            $new_phone     = $this->input->post('phone');
            $new_address   = $this->input->post('address');
            $new_birthdate = $this->input->post('birthdate'); 
            $password_input = $this->input->post('password');

            // Siapkan array data untuk update ke database
            $data_for_db = array(
                'namanasabah'   => $new_namanasabah,
                'username'      => $new_username,
                'phone'         => $new_phone,
                'address'       => $new_address,
                // KOREKSI: Konversi tanggal dari format input ke YYYY-MM-DD untuk DB
                'birthdate'     => date('Y-m-d', strtotime(str_replace('/', '-', $new_birthdate))) 
            );

            // Inisialisasi status perubahan password
            $password_changed = false;
            if (!empty($password_input)) { 
                $data_for_db['password'] = md5($password_input); 
                $password_changed = true;
            }
            
            $query = $this->m_nasabah->update($idnasabah, $data_for_db);
            if ($query) {
                $this->session->set_flashdata('info', 'Data berhasil di Update');

                // --- LOG AKTIVITAS: Berhasil Update Nasabah ---
                $user_email = $this->session->userdata('email');
                if ($user_email) {
                    // Ambil username nasabah lama yang akurat
                    // Akses properti sebagai array karena $nasabah_lama adalah array
                    $nasabah_name_lama = (isset($nasabah_lama['username'])) ? $nasabah_lama['username'] : 'ID ' . $idnasabah;
                    $nasabah_name_baru = $new_username; 

                    $deskripsi_log = 'Berhasil memperbarui data nasabah: ' . $nasabah_name_lama . ' (ID: ' . $idnasabah . ') menjadi ' . $nasabah_name_baru . '. ';
                    
                    $changes = [];

                    // --- DEBUGGING LOG BARU ---
                    // Ini akan menulis ke PHP error log (biasanya php_error.log atau serupa)
                    // Pastikan error_logging di php.ini Anda aktif dan levelnya sesuai.
                    error_log("DEBUG LOG - nasabah_lama['username']: " . (isset($nasabah_lama['username']) ? $nasabah_lama['username'] : 'NULL/NotSet'));
                    error_log("DEBUG LOG - new_username: " . $new_username);
                    error_log("DEBUG LOG - nasabah_lama['phone']: " . (isset($nasabah_lama['phone']) ? $nasabah_lama['phone'] : 'NULL/NotSet'));
                    error_log("DEBUG LOG - new_phone: " . $new_phone);
                    error_log("DEBUG LOG - nasabah_lama['address']: " . (isset($nasabah_lama['address']) ? $nasabah_lama['address'] : 'NULL/NotSet'));
                    error_log("DEBUG LOG - new_address: " . $new_address);
                    error_log("DEBUG LOG - nasabah_lama['birthdate'] (raw): " . (isset($nasabah_lama['birthdate']) ? $nasabah_lama['birthdate'] : 'NULL/NotSet'));
                    error_log("DEBUG LOG - new_birthdate (from form): " . $new_birthdate);
                    // Definisi ini harus di atas error_log jika ingin log nilai finalnya.
                    $old_birthdate_db_format = '';
                    // Akses properti sebagai array
                    if (isset($nasabah_lama['birthdate']) && !empty($nasabah_lama['birthdate'])) { 
                        $old_birthdate_db_format = date('Y-m-d', strtotime($nasabah_lama['birthdate']));
                    }
                    $new_birthdate_db_format = $data_for_db['birthdate']; 

                    error_log("DEBUG LOG - old_birthdate_db_format: " . $old_birthdate_db_format);
                    error_log("DEBUG LOG - new_birthdate_db_format: " . $new_birthdate_db_format);
                    error_log("DEBUG LOG - password_changed: " . ($password_changed ? 'TRUE' : 'FALSE'));
                    // --- AKHIR DEBUGGING LOG BARU ---

                    // Perbandingan field demi field (menggunakan trim() dan isset() untuk keamanan)
                    // Nama
                    // Akses properti sebagai array
                    if (isset($nasabah_lama['namanasabah']) && trim($nasabah_lama['namanasabah']) !== trim($new_namanasabah)) {
                        $changes[] = 'Nama Nasabah: "' . (isset($nasabah_lama['namanasabah']) ? $nasabah_lama['namanasabah'] : 'Kosong') . '" -> "' . $new_namanasabah . '"';
                    }
                    if (isset($nasabah_lama['username']) && trim($nasabah_lama['username']) !== trim($new_username)) {
                        $changes[] = 'Nama: "' . (isset($nasabah_lama['username']) ? $nasabah_lama['username'] : 'Kosong') . '" -> "' . $new_username . '"';
                    }

                    // Nomor Telepon
                    // Akses properti sebagai array
                    if (isset($nasabah_lama['phone']) && trim($nasabah_lama['phone']) !== trim($new_phone)) {
                        $changes[] = 'Nomor Telepon: "' . (isset($nasabah_lama['phone']) ? $nasabah_lama['phone'] : 'Kosong') . '" -> "' . $new_phone . '"';
                    }

                    // Alamat
                    // Akses properti sebagai array
                    if (isset($nasabah_lama['address']) && trim($nasabah_lama['address']) !== trim($new_address)) {
                        $changes[] = 'Alamat: "' . (isset($nasabah_lama['address']) ? $nasabah_lama['address'] : 'Kosong') . '" -> "' . $new_address . '"';
                    }

                    // Tanggal Lahir (Perbandingan setelah dikonversi ke format yang sama)
                    // $old_birthdate_db_format dan $new_birthdate_db_format sudah didefinisikan di atas.
                    if ($old_birthdate_db_format !== $new_birthdate_db_format) {
                        // Tampilkan tanggal dalam format yang mudah dibaca di log (sesuai input form)
                        // Akses properti sebagai array
                        $changes[] = 'Tanggal Lahir: "' . (isset($nasabah_lama['birthdate']) ? $nasabah_lama['birthdate'] : 'Kosong') . '" -> "' . $new_birthdate . '"';
                    }

                    // Password
                    if ($password_changed) { 
                        $changes[] = 'Password: (Diubah)';
                    }

                    if (!empty($changes)) {
                        $deskripsi_log .= 'Detail perubahan: ' . implode(', ', $changes) . '.';
                    } else {
                        if ($password_changed) {
                            $deskripsi_log .= 'Hanya password yang diubah.';
                        } else {
                             $deskripsi_log .= 'Tidak ada perubahan data yang terdeteksi.';
                        }
                    }
                    
                    $this->M_logaktivitas->log_aktivitas(
                        $user_email,
                        'Update Data Nasabah',
                        $deskripsi_log
                    );
                }
                // --- END LOG AKTIVITAS ---

                redirect('nasabah');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengupdate data nasabah.');

                // --- LOG AKTIVITAS: Gagal Update Nasabah (Database) ---
                $user_email = $this->session->userdata('email');
                if ($user_email) {
                    // Akses properti sebagai array
                    $nasabah_name = (isset($nasabah_lama['username'])) ? $nasabah_lama['username'] : 'ID ' . $idnasabah;
                    $this->M_logaktivitas->log_aktivitas(
                        $user_email,
                        'Gagal Update Nasabah',
                        'Admin gagal memperbarui nasabah: ' . $nasabah_name . ' (ID: ' . $idnasabah . '). Kemungkinan masalah database.'
                    );
                }
                // --- END LOG AKTIVITAS ---

                redirect('nasabah/edit/' . $idnasabah);
            }
        }
    }

    // Callback function for unique phone number validation during edit
    public function unique_phone_edit($phone, $idnasabah) {
        $this->db->where('phone', $phone);
        $this->db->where('idnasabah !=', $idnasabah);
        $query = $this->db->get('nasabah');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('unique_phone_edit', 'Nomor Telepon {field} sudah terdaftar untuk nasabah lain.');
            return FALSE;
        }
        return TRUE;
    }

    public function hapus($id)
    {
        $query = $this->m_nasabah->hapus($id);
        if ($query = true) {
            $this->session->set_flashdata('info', 'Data berhasil di Hapus');
			
			 // --- LOG AKTIVITAS: Berhasil Hapus Nasabah ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $nasabah_name = isset($nasabah_info->username) ? $nasabah_info->username : '';
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Hapus Data Nasabah',
                    'Berhasil menghapus nasabah: ' . $nasabah_name . ' (ID: ' . $id . ').'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('nasabah');
        } else {
             $this->session->set_flashdata('error', 'Gagal menghapus data nasabah.');
			
			 // --- LOG AKTIVITAS: Gagal Hapus Nasabah ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $nasabah_name = isset($nasabah_info->username) ? $nasabah_info->username : 'Tidak Diketahui';
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Hapus Data Nasabah',
                    'Gagal menghapus nasabah: ' . $nasabah_name . ' (ID: ' . $id . '). Kemungkinan masalah database.'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('nasabah');
    }
}

    public function proses_tarik_saldo()
    {
        $idnasabah = $this->input->post('idnasabah');
        $jumlah_tarik = $this->input->post('jumlah_tarik');

        // Ambil saldo nasabah saat ini dari tabel nasabah
        $current_saldo = $this->m_nasabah->getDirectSaldoTabunganById($idnasabah);

        if ($jumlah_tarik <= 0) {
            $this->session->set_flashdata('error', 'Jumlah penarikan harus lebih dari nol.');
			
			 // --- LOG AKTIVITAS: Gagal Proses Penarikan (Jumlah Invalid) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Proses Penarikan Saldo',
                    'Admin gagal memproses penarikan saldo untuk nasabah ' . $nasabah_name . ' (ID: ' . $idnasabah . ') karena jumlah penarikan tidak valid (kurang dari atau sama dengan nol). Nominal coba tarik: Rp' . number_format($jumlah_tarik, 0, ',', '.')
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('penabungan/saldo/' . $idnasabah);
            return;
        }

        if ($jumlah_tarik > $current_saldo) {
            $this->session->set_flashdata('error', 'Nominal penarikan (Rp ' . number_format($jumlah_tarik, 0, ',', '.') . ') melebihi saldo nasabah (Rp ' . number_format($current_saldo, 0, ',', '.') . ').');
			
			 // --- LOG AKTIVITAS: Gagal Proses Penarikan (Saldo Kurang) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Proses Penarikan Saldo',
                    'Admin gagal memproses penarikan saldo untuk nasabah ' . $nasabah_name . ' (ID: ' . $idnasabah . ') karena nominal penarikan (Rp' . number_format($jumlah_tarik, 0, ',', '.') . ') melebihi saldo (Rp' . number_format($current_saldo, 0, ',', '.') . ').'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('penabungan/saldo/' . $idnasabah);
            return;
        }

        // Mulai transaksi database
        $this->db->trans_start();

        // 1. Kurangi saldo nasabah di tabel `nasabah`
        $data_update_nasabah = array(
            'tabungan' => $current_saldo - $jumlah_tarik
        );
        $update_nasabah_result = $this->m_nasabah->update($idnasabah, $data_update_nasabah);

        // 2. Catat transaksi penarikan ke tabel `penarikan`
        $data_penarikan_history = array(
            'idpenarikan' => $this->m_penarikan->idpenarikan(), // Generate ID Penarikan baru
            'idnasabah' => $idnasabah,
            'nominal' => $jumlah_tarik,
            'tanggal' => date('Y-m-d H:i:s'), // Waktu penarikan
            'status' => 'Selesai', // Karena ini penarikan offline, langsung set Selesai
            'metode' => 'TUNAI (Admin)', // Default untuk penarikan offline
            'noRek' => 'N/A', // Default untuk penarikan offline
            'tanggal_diproses' => date('Y-m-d H:i:s'), // Waktu diproses
            // 'id_admin_proses' => $this->session->userdata('id_admin') // Opsional, jika Anda menyimpan ID admin yang memproses
        );
        $insert_penarikan_result = $this->m_penarikan->insertPengajuanPenarikan($data_penarikan_history);

        // Selesaikan transaksi database
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // Jika ada masalah dalam transaksi
            $this->session->set_flashdata('error', 'Gagal memproses penarikan saldo dan mencatat riwayat. Transaksi dibatalkan.');
			
			 // --- LOG AKTIVITAS: Gagal Proses Penarikan (Transaksi Database) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Proses Penarikan Saldo',
                    'Admin gagal memproses penarikan saldo untuk nasabah ' . $nasabah_name . ' (ID: ' . $idnasabah . '). Transaksi database gagal.'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('penabungan/saldo/' . $idnasabah);
        } elseif ($update_nasabah_result && $insert_penarikan_result) {
            $this->session->set_flashdata('info', 'Saldo nasabah berhasil ditarik dan riwayat penarikan tercatat.');
			
			 // --- LOG AKTIVITAS: Berhasil Proses Penarikan ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Proses Penarikan Saldo',
                    'Admin berhasil menarik saldo Rp' . number_format($jumlah_tarik, 0, ',', '.') . ' dari nasabah ' . $nasabah_name . ' (ID: ' . $idnasabah . '). Saldo baru: Rp' . number_format($current_saldo - $jumlah_tarik, 0, ',', '.') . '.'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('penarikan'); // Redirect ke halaman daftar penarikan
        } else {
            // Fallback error, meskipun seharusnya tertangkap oleh trans_status()
            $this->session->set_flashdata('error', 'Terjadi kesalahan tidak terduga saat memproses penarikan.');
			
			// --- LOG AKTIVITAS: Gagal Proses Penarikan (Kesalahan Tak Terduga) ---
            $user_email = $this->session->userdata('email');
            if ($user_email) {
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Proses Penarikan Saldo',
                    'Terjadi kesalahan tidak terduga saat admin memproses penarikan saldo untuk nasabah ' . $nasabah_name . ' (ID: ' . $idnasabah . ').'
                );
            }
            // --- END LOG AKTIVITAS ---
			
            redirect('penabungan/saldo/' . $idnasabah);
        }
    }

    // Callback function untuk validasi unik nomor telepon secara manual
    // Fungsi ini tidak melakukan pengecekan ke DB, hanya dipanggil untuk memicu pesan error
    public function manual_phone_unique($phone) {
        // Karena pengecekan keunikan sudah dilakukan di fungsi simpan(),
        // fungsi ini hanya berfungsi sebagai "placeholder" untuk menampilkan pesan error
        // yang sudah diset melalui set_message().
        // Jika kode sampai sini, berarti pengecekan keunikan di atas sudah gagal
        // dan set_message sudah dipanggil.
        return FALSE; 
    }

    // --- FUNGSI CALLBACK BARU UNTUK VALIDASI KRITERIA PASSWORD ---
   public function valid_password_criteria($password)
{
    $password_regex = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!_*-])(?=\S+$).*$/";

    // --- Pindahkan inisialisasi array $messages ke sini ---
    $messages = [];
    // --- Akhir Pemindahan ---

    if (empty($password)) {
        $this->form_validation->set_message('valid_password_criteria', 'Password tidak boleh kosong.');
        return FALSE;
    }

    if (preg_match($password_regex, $password)) {
        return TRUE;
    } else {
        // Logika pengisian $messages tetap di sini
        
        if (!preg_match('/[a-z]/', $password)) {
            $messages[] = 'Satu huruf kecil';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $messages[] = 'Satu huruf besar';
        }
        if (!preg_match('/[@#$%^&+=!_*-]/', $password)) {
            $messages[] = 'Satu simbol (@#$%^&+=!_*-)';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $messages[] = 'Satu angka';
        }
        if (preg_match('/\s/', $password)) {
            $messages[] = 'Tanpa spasi';
        }
        
        $message_output = 'Password harus mengandung: ' . implode(', ', $messages) . '.';
        
        $this->form_validation->set_message('valid_password_criteria', $message_output);
        return FALSE;
    }
}

/**
     * Callback function for form_validation to check if username contains spaces.
     * @param string $str The input string from the username field.
     * @return bool TRUE if no spaces, FALSE otherwise.
     */
    public function no_space_username($str)
    {
        if (preg_match('/\s/', $str)) {
            $this->form_validation->set_message('no_space_username', '{field} tidak boleh mengandung spasi.');
            return FALSE;
        }
        return TRUE;
    }
    
    // --- AKHIR FUNGSI CALLBACK BARU ---
 /*   public function proses_tarik_saldo()
{
    $idnasabah = $this->input->post('idnasabah');
    $jumlah_tarik = $this->input->post('jumlah_tarik');

    // Validasi apakah jumlah tarik valid (misalnya, tidak melebihi saldo)
    $nasabah = $this->m_nasabah->getNasabahById($idnasabah);
    if ($jumlah_tarik > $nasabah->tabungan) {
        $this->session->set_flashdata('error', 'Jumlah penarikan melebihi saldo.');
        redirect('penabungan/saldo/' . $idnasabah); // Kembali ke form tarik saldo
        return;
    }

    // Kurangi saldo nasabah
    $result = $this->m_nasabah->kurangi_saldo($idnasabah, $jumlah_tarik);

    if ($result) {
        $this->session->set_flashdata('success', 'Saldo berhasil ditarik.');
        redirect('nasabah'); // Redirect ke halaman daftar nasabah
    } else {
        $this->session->set_flashdata('error', 'Gagal melakukan penarikan saldo.');
        redirect('penabungan/saldo/' . $idnasabah); // Kembali ke form tarik saldo
    }
} */

}