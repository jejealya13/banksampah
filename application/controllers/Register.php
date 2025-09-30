<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_login');
        $this->load->model('M_logaktivitas'); // <<< TAMBAHKAN INI
        $this->load->library('form_validation');
        $this->load->library('session');
        
    }

    public function index() {
        // Jika user sudah login, redirect ke dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }
        $this->load->view('v_register'); // Load view untuk halaman registrasi
    }

    public function proses_registrasi() {
        // Hapus validasi untuk 'nama'
        // $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|is_unique[admin.email]',
            array('is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.')
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_valid_password_criteria');
        $this->form_validation->set_rules('password_conf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('v_register');

            // --- LOG AKTIVITAS: Registrasi Gagal (Validasi Form) ---
            $user_email = $this->input->post('email', TRUE) ?? 'Guest'; // Mengambil email yang dicoba, atau 'Guest'
            $error_messages = validation_errors(' ', ' '); // Dapatkan semua pesan error validasi
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Registrasi Akun',
                'Percobaan registrasi akun gagal karena validasi form. Email: ' . $user_email . '. Kesalahan: ' . trim($error_messages)
            );
            // --- END LOG AKTIVITAS ---

        } else {
            $data = array(
                // Hapus baris ini jika tidak ada kolom 'nama' di tabel admin atau jika tidak ingin disimpan
                // 'nama' => $this->input->post('nama', TRUE),
                'email' => $this->input->post('email', TRUE),
                'password' => $this->input->post('password', TRUE), // Password akan di-hash di model
            );

            if ($this->m_login->register_user($data)) {
                $this->session->set_flashdata('info', '<div class="alert alert-success" role="alert">Registrasi berhasil! Silakan login dengan akun Anda.</div>');

                // --- LOG AKTIVITAS: Registrasi Akun Berhasil ---
                $user_email = $this->input->post('email', TRUE); // Email yang baru terdaftar
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Registrasi Akun',
                    'Akun baru berhasil didaftarkan dengan email: ' . $user_email
                );
                // --- END LOG AKTIVITAS ---

                redirect('login');
            } else {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Registrasi gagal. Email mungkin sudah terdaftar.</div>');

                // --- LOG AKTIVITAS: Registrasi Akun Gagal (Kemungkinan Email Duplikat/DB Error) ---
                $user_email = $this->input->post('email', TRUE); // Email yang dicoba untuk didaftarkan
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Registrasi Akun',
                    'Registrasi akun gagal untuk email: ' . $user_email . '. Kemungkinan email sudah terdaftar atau masalah database.'
                );
                // --- END LOG AKTIVITAS ---

                redirect('register');
            }
        }
    }

        // --- FUNGSI CALLBACK VALIDASI PASSWORD UNTUK ATURAN KHUSUS ---
    public function valid_password_criteria($password)
    {
        $password_regex = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!_*-])(?=\S+$).*$/";

        $messages = []; // Pastikan ini diinisialisasi di awal fungsi

        if (empty($password)) {
            $this->form_validation->set_message('valid_password_criteria', 'Password tidak boleh kosong.');
            return FALSE;
        }

        if (!preg_match($password_regex, $password)) {
            if (!preg_match('/[a-z]/', $password)) {
                $messages[] = 'Satu huruf kecil';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $messages[] = 'Satu huruf besar';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $messages[] = 'Satu angka';
            }
            if (!preg_match('/[@#$%^&+=!_*-]/', $password)) {
                $messages[] = 'Satu simbol (@#$%^&+=!_*-)';
            }
            if (preg_match('/\s/', $password)) {
                $messages[] = 'Tanpa spasi';
            }
            
            $message_output = 'Password harus mengandung: ' . implode(', ', $messages) . '.';
            
            $this->form_validation->set_message('valid_password_criteria', $message_output);
            return FALSE;
        }
        return TRUE;
    }
}