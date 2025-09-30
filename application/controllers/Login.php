<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    private $otp_storage_path; // Properti untuk path penyimpanan OTP

    public function __construct() {
        parent::__construct();
        $this->load->model('m_login');
        $this->load->model('M_logaktivitas');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->library('session');

        // Konfigurasi email PENGIRIM
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'banksampahalmubarok@gmail.com',
            'smtp_pass' => 'hdov pceh pfpj zbpr',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'smtp_crypto' => 'tls'
        ];
        $this->email->initialize($config);

        // Path penyimpanan OTP
        // Pastikan folder ini ada dan memiliki izin tulis oleh web server
        $this->otp_storage_path = APPPATH . 'otp_storage/';

        // Buat folder jika belum ada
        if (!is_dir($this->otp_storage_path)) {
            mkdir($this->otp_storage_path, 0755, TRUE);
        }
    }

    // --- Helper Functions for OTP File Storage ---
    private function _get_otp_file_path($admin_id) {
        return $this->otp_storage_path . 'otp_' . md5($admin_id) . '.txt'; // Menggunakan MD5 untuk ID file
    }

    private function _save_otp_to_file($admin_id, $otp_code) {
        $file_path = $this->_get_otp_file_path($admin_id);
        $expiration_time = time() + (5 * 60); // OTP berlaku 5 menit (dalam detik)
        $data = $otp_code . '|' . $expiration_time; // Format: OTP_CODE|TIMESTAMP_KADALUWARSA

        // Hapus file lama jika ada (untuk mencegah penumpukan jika user sering minta OTP baru)
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        return file_put_contents($file_path, $data) !== FALSE;
    }

    private function _get_otp_from_file($admin_id) {
        $file_path = $this->_get_otp_file_path($admin_id);

        if (file_exists($file_path)) {
            $data = file_get_contents($file_path);
            list($otp_code, $expiration_time) = explode('|', $data);

            if (time() < $expiration_time) {
                return ['otp_code' => $otp_code, 'expiration_time' => $expiration_time];
            } else {
                // OTP sudah kadaluarsa, hapus file
                $this->_delete_otp_file($admin_id);
            }
        }
        return FALSE; // OTP tidak ditemukan atau sudah kadaluarsa
    }

    private function _delete_otp_file($admin_id) {
        $file_path = $this->_get_otp_file_path($admin_id);
        if (file_exists($file_path)) {
            return unlink($file_path);
        }
        return TRUE; // Jika file tidak ada, anggap berhasil dihapus
    }
    // --- End Helper Functions ---

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }
        $this->load->view('v_login');
    }

    public function proses_login() {
        $this->form_validation->set_rules('username', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('v_login');
        } else {
            $email = $this->input->post('username', TRUE);
            $pass_input = $this->input->post('password', TRUE);

            $user_data = $this->m_login->proses_login($email, $pass_input);

            if ($user_data === 'locked') {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Akun Anda terkunci. Silakan coba lagi setelah 1 menit.</div>');
                $this->M_logaktivitas->log_aktivitas($email, 'Login Gagal (Akun Terkunci)', 'Gagal login karena akun terkunci otomatis setelah beberapa percobaan');
                redirect('login');
            } elseif ($user_data) {
                $otp = rand(100000, 999999);

                // Ganti: Simpan OTP ke file teks
                if ($this->_save_otp_to_file($user_data->id, $otp)) {
                    $this->session->set_userdata('temp_admin_id', $user_data->id);
                    $this->session->set_userdata('temp_admin_email', $user_data->email);

                    $this->M_logaktivitas->log_aktivitas($user_data->email, 'Upaya Login (OTP Terkirim)', 'Berhasil login tahap 1, OTP terkirim ke email untuk verifikasi');

                    if ($this->send_otp($user_data->email, $otp)) {
                        redirect('verifikasi');
                    } else {
                        $this->_delete_otp_file($user_data->id); // Hapus OTP jika gagal kirim email
                        $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Gagal mengirim email verifikasi. Silakan coba lagi nanti.</div>');
                        redirect('login');
                    }
                } else {
                    $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Terjadi kesalahan saat menyimpan OTP. Silakan coba lagi.</div>');
                    $this->M_logaktivitas->log_aktivitas($user_data->email, 'Upaya Login Gagal (Simpan OTP)', 'Gagal menyimpan OTP ke file teks');
                    redirect('login');
                }
            } else {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Login Gagal, Silahkan Periksa Email dan Password!</div>');
                $this->M_logaktivitas->log_aktivitas($email, 'Login Gagal (Email/Password Salah)', 'Upaya login dengan email/password yang salah');
                redirect('login');
            }
        }
    }

    private function send_otp($email_penerima, $otp) {
        $this->email->from($this->email->smtp_user, 'Bank Sampah');
        $this->email->to($email_penerima);
        $this->email->subject('Kode Verifikasi Login Bank Sampah');
        $this->email->message('Kode OTP Anda adalah: <strong>' . $otp . '</strong>. Kode ini berlaku selama 5 menit.');

        if ($this->email->send()) {
            log_message('info', 'Email OTP berhasil dikirim ke ' . $email_penerima);
            $this->M_logaktivitas->log_aktivitas($email_penerima, 'Pengiriman OTP Berhasil', 'OTP berhasil dikirim ke email');
            return TRUE;
        } else {
            log_message('error', 'Email OTP gagal dikirim ke ' . $email_penerima);
            log_message('error', $this->email->print_debugger());
            $this->M_logaktivitas->log_aktivitas($email_penerima, 'Pengiriman OTP Gagal', 'Gagal mengirim OTP ke email');
            return FALSE;
        }
    }

    public function verifikasi() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }
        if (!$this->session->userdata('temp_admin_id')) {
            $this->session->set_flashdata('info', '<div class="alert alert-warning" role="alert">Anda harus login terlebih dahulu untuk mengakses halaman verifikasi.</div>');
            $email_attempt = $this->session->userdata('temp_admin_email') ? $this->session->userdata('temp_admin_email') : 'Tidak Diketahui';
            $this->M_logaktivitas->log_aktivitas($email_attempt, 'Akses Verifikasi Gagal (Sesi Tidak Valid)', 'Mencoba mengakses halaman verifikasi tanpa sesi yang valid');
            redirect('login');
        }
        $this->load->view('v_verifikasi');
    }

    public function proses_verifikasi() {
        $this->form_validation->set_rules('otp', 'OTP', 'required|numeric|exact_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('v_verifikasi');
        } else {
            $otp_input = $this->input->post('otp', TRUE);
            $admin_id = $this->session->userdata('temp_admin_id');
            $temp_admin_email = $this->session->userdata('temp_admin_email');

            if (!$admin_id) {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Sesi login tidak valid. Silakan login kembali.</div>');
                $email_for_log = $temp_admin_email ? $temp_admin_email : 'Tidak Diketahui';
                $this->M_logaktivitas->log_aktivitas($email_for_log, 'Verifikasi OTP Gagal (Sesi Tidak Valid)', 'Upaya verifikasi OTP tanpa sesi login yang valid');
                redirect('login');
            }

            // Ganti: Verifikasi OTP dari file teks
            $stored_otp_data = $this->_get_otp_from_file($admin_id);

            if ($stored_otp_data && $stored_otp_data['otp_code'] == $otp_input) {
                // Hapus OTP setelah berhasil diverifikasi
                $this->_delete_otp_file($admin_id);

                $user_data = $this->m_login->get_user_by_id($admin_id);

                if ($user_data) {
                    $sess_data = array(
                        'id' => $user_data->id,
                        'email' => $user_data->email,
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($sess_data);

                    $this->session->unset_userdata('temp_admin_id');
                    $this->session->unset_userdata('temp_admin_email');

                    $this->session->set_flashdata('info', '<div class="alert alert-success" role="alert">Verifikasi berhasil! Selamat datang.</div>');
                    log_message('info', 'Verifikasi berhasil untuk user ID: ' . $admin_id . ', mengarah ke dashboard');
                    $this->M_logaktivitas->log_aktivitas($user_data->email, 'Login Berhasil', 'Pengguna berhasil login sepenuhnya setelah verifikasi OTP');
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Terjadi kesalahan saat mengambil data user. Silakan login kembali.</div>');
                    $email_for_log = $temp_admin_email ? $temp_admin_email : 'Tidak Diketahui';
                    $this->M_logaktivitas->log_aktivitas($email_for_log, 'Verifikasi OTP Gagal (Data User Tidak Ditemukan)', 'Verifikasi OTP berhasil, tetapi data user tidak ditemukan');
                    redirect('login');
                }
            } else {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Kode Verifikasi Salah atau sudah Kadaluwarsa. Silakan Cek Kembali!</div>');
                $email_for_log = $temp_admin_email ? $temp_admin_email : 'Tidak Diketahui';
                $this->M_logaktivitas->log_aktivitas($email_for_log, 'Verifikasi OTP Gagal (Salah/Kadaluwarsa)', 'Pengguna memasukkan kode OTP yang salah atau kadaluwarsa');
                redirect('verifikasi');
            }
        }
    }

    public function logout() {
        $user_email = $this->session->userdata('email');
        if ($user_email) {
            $this->M_logaktivitas->log_aktivitas($user_email, 'Logout', 'Pengguna berhasil logout dari sistem');
        }
        $this->session->sess_destroy();
        redirect('login');
    }

    public function generate_hash() {
        $password = 'banksampah2021';
        echo password_hash($password, PASSWORD_DEFAULT);
    }

    public function forgot_password() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }
        $this->load->view('v_forgot_password');
    }

    public function send_reset_link() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">' . validation_errors() . '</div>');
            $user_email = $this->input->post('email', TRUE) ?? 'Tidak Diketahui';
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Permintaan Reset Password Gagal',
                'Permintaan reset password gagal: Validasi form tidak terpenuhi. Email: ' . $user_email
            );
            redirect('login/forgot_password');
        } else {
            $email = $this->input->post('email', TRUE);
            $user = $this->m_login->get_admin_by_email($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));

                if ($this->m_login->update_reset_token($user->id, $token, $expiry_time)) {
                    $reset_link = base_url('login/reset_password/' . $token);

                    $this->email->clear();
                    $this->email->from($this->email->smtp_user, 'Bank Sampah');
                    $this->email->to($email);
                    $this->email->subject('Reset Kata Sandi Akun Bank Sampah');
                    $message = "Halo,<br><br>";
                    $message .= "Kami menerima permintaan untuk mereset kata sandi akun Anda. ";
                    $message .= "Untuk melanjutkan, silakan klik tautan di bawah ini:<br><br>";
                    $message .= "<a href='" . $reset_link . "'>Reset Kata Sandi Anda</a><br><br>";
                    $message .= "Tautan ini akan kedaluwarsa dalam 1 jam.<br>";
                    $message .= "Jika Anda tidak meminta reset kata sandi ini, abaikan email ini.<br><br>";
                    $message .= "Terima kasih,<br>";
                    $message .= "Tim Bank Sampah";
                    $this->email->message($message);

                    if ($this->email->send()) {
                        $this->session->set_flashdata('info', '<div class="alert alert-success" role="alert">Tautan reset kata sandi telah dikirim ke email Anda. Silakan periksa inbox Anda.</div>');
                        $this->M_logaktivitas->log_aktivitas(
                            $email,
                            'Permintaan Reset Password Berhasil',
                            'Tautan reset password berhasil dikirim ke email: ' . $email
                        );
                        redirect('login');
                    } else {
                        $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Gagal mengirim email reset kata sandi. Silakan coba lagi nanti.</div>');
                        log_message('error', 'Gagal mengirim email reset: ' . $this->email->print_debugger());
                        $this->M_logaktivitas->log_aktivitas(
                            $email,
                            'Permintaan Reset Password Gagal',
                            'Gagal mengirim email reset password ke: ' . $email . '. Error: ' . $this->email->print_debugger()
                        );
                        redirect('login/forgot_password');
                    }
                } else {
                    $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Terjadi kesalahan saat membuat tautan reset. Silakan coba lagi.</div>');
                    $this->M_logaktivitas->log_aktivitas(
                        $email,
                        'Permintaan Reset Password Gagal',
                        'Gagal menyimpan token reset password untuk email: ' . $email . '. Masalah database.'
                    );
                    redirect('login/forgot_password');
                }
            } else {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Email tidak terdaftar.</div>');
                $this->M_logaktivitas->log_aktivitas(
                    $email,
                    'Permintaan Reset Password Gagal',
                    'Permintaan reset password untuk email tidak terdaftar: ' . $email
                );
                redirect('login/forgot_password');
            }
        }
    }

    public function reset_password($token = NULL) {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        if ($token === NULL) {
            $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Tautan reset kata sandi tidak valid.</div>');
            $this->M_logaktivitas->log_aktivitas(
                'Tidak Diketahui',
                'Akses Halaman Reset Password Gagal',
                'Akses ke halaman reset password gagal: Token kosong.'
            );
            redirect('login');
        }

        $user = $this->m_login->get_admin_by_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Tautan reset kata sandi tidak valid atau sudah kadaluwarsa.</div>');
            $this->M_logaktivitas->log_aktivitas(
                'Tidak Diketahui',
                'Akses Halaman Reset Password Gagal',
                'Akses ke halaman reset password gagal. Token tidak valid atau kadaluwarsa: ' . $token
            );
            redirect('login');
        }

        $this->M_logaktivitas->log_aktivitas(
            $user->email,
            'Akses Halaman Reset Password Berhasil',
            'Pengguna mengakses halaman reset password dengan token valid.'
        );

        $data['token'] = $token;
        $this->load->view('v_reset_password', $data);
    }

    public function proses_reset_password() {
        $this->form_validation->set_rules('token', 'Token', 'required');
        $this->form_validation->set_rules('new_password', 'Kata Sandi Baru', 'required|min_length[6]|callback_valid_password_criteria');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Kata Sandi', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            $token = $this->input->post('token', TRUE);
            $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">' . validation_errors() . '</div>');
            $this->M_logaktivitas->log_aktivitas(
                'Tidak Diketahui',
                'Proses Reset Password Gagal',
                'Proses reset password gagal: Validasi form tidak terpenuhi. Token: ' . $token . '. Kesalahan: ' . validation_errors()
            );
            redirect('login/reset_password/' . $token);
        } else {
            $token = $this->input->post('token', TRUE);
            $new_password = $this->input->post('new_password', TRUE);

            $user = $this->m_login->get_admin_by_reset_token($token);

            if (!$user) {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Tautan reset kata sandi tidak valid atau sudah kadaluwarsa.</div>');
                $this->M_logaktivitas->log_aktivitas(
                    'Tidak Diketahui',
                    'Proses Reset Password Gagal',
                    'Proses reset password gagal: Token tidak valid atau kadaluwarsa saat proses.'
                );
                redirect('login');
            }

            if ($this->m_login->update_admin_password($user->id, $new_password)) {
                $this->session->set_flashdata('info', '<div class="alert alert-success" role="alert">Kata sandi Anda berhasil diubah. Silakan login dengan kata sandi baru Anda.</div>');
                $this->M_logaktivitas->log_aktivitas(
                    $user->email,
                    'Reset Password Berhasil',
                    'Kata sandi pengguna berhasil diubah untuk email: ' . $user->email
                );
                redirect('login');
            } else {
                $this->session->set_flashdata('info', '<div class="alert alert-danger" role="alert">Terjadi kesalahan saat memperbarui kata sandi. Silakan coba lagi.</div>');
                $this->M_logaktivitas->log_aktivitas(
                    $user->email,
                    'Proses Reset Password Gagal',
                    'Gagal memperbarui kata sandi untuk email: ' . $user->email . '. Masalah database saat update.'
                );
                redirect('login/reset_password/' . $token);
            }
        }
    }

    public function valid_password_criteria($password)
    {
        $password_regex = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!_*-])(?=\S+$).*$/";

        $messages = [];

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