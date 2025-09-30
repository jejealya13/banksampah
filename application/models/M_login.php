<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_login extends CI_Model {

    // Fungsi untuk registrasi user baru (untuk admin), tanpa nama
    public function register_user($data) {
        $this->db->where('email', $data['email']);
        $query = $this->db->get('admin');

        if ($query->num_rows() > 0) {
            return FALSE; // Email sudah terdaftar
        } else {
            // Hash password sebelum disimpan
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->db->insert('admin', $data);
            return $this->db->affected_rows() > 0;
        }
    }

    // Fungsi untuk proses login (memverifikasi password lokal)
    public function proses_login($email, $password_input) {
        $this->db->where('email', $email);
        $query = $this->db->get('admin');

        if ($query->num_rows() == 0) {
            return false; // Email tidak ditemukan
        }

        $user = $query->row();

        // Cek apakah sudah mencapai batas upaya login
        if ($user->login_attempts >= 3) {
            $last_attempt = new DateTime($user->last_attempt);
            $now = new DateTime();
            $diff = $now->diff($last_attempt);

            // Jika sudah lebih dari 1 menit, reset upaya login
            if ($diff->i >= 1 || $diff->h > 0 || $diff->d > 0) { // Menambahkan cek jam dan hari
                $this->db->update('admin', ['login_attempts' => 0, 'last_attempt' => null], ['email' => $email]);
                // Setelah reset, coba verifikasi ulang password
                if (password_verify($password_input, $user->password)) {
                    return $user; // Return data user
                } else {
                    // Password salah setelah reset attempts
                    $attempts = $user->login_attempts + 1;
                    $this->db->update('admin', ['login_attempts' => $attempts, 'last_attempt' => date('Y-m-d H:i:s')], ['email' => $email]);
                    return false;
                }
            } else {
                return 'locked'; // Akun terkunci
            }
        }

        // Verifikasi password
        if (password_verify($password_input, $user->password)) {
            // Reset login attempts jika berhasil
            $this->db->update('admin', ['login_attempts' => 0, 'last_attempt' => null], ['email' => $email]);
            return $user; // Return data user
        } else {
            // Tambah jumlah login attempts jika password salah
            $attempts = $user->login_attempts + 1;
            $this->db->update('admin', ['login_attempts' => $attempts, 'last_attempt' => date('Y-m-d H:i:s')], ['email' => $email]);
            return false; // Password salah
        }
    }

    // Fungsi untuk mendapatkan data user berdasarkan ID
    public function get_user_by_id($admin_id) {
        $this->db->where('id', $admin_id);
        $query = $this->db->get('admin');
        return $query->row();
    }

    // --- FUNGSI BARU UNTUK FORGOT PASSWORD ---

    // Fungsi untuk mencari admin berdasarkan email
    public function get_admin_by_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('admin');
        return $query->row(); // Mengembalikan satu baris objek jika ditemukan
    }

    // Fungsi untuk menyimpan token reset password ke database
    public function update_reset_token($admin_id, $token, $expiry) {
        $data = array(
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        );
        $this->db->where('id', $admin_id);
        return $this->db->update('admin', $data);
    }

    // Fungsi untuk mendapatkan admin berdasarkan token reset
    public function get_admin_by_reset_token($token) {
        $this->db->where('reset_token', $token);
        // Pastikan token belum kadaluarsa
        $this->db->where('reset_token_expiry >', date('Y-m-d H:i:s'));
        $query = $this->db->get('admin');
        return $query->row();
    }

    // Fungsi untuk memperbarui password admin
    public function update_admin_password($admin_id, $new_password) {
        $data = array(
            'password' => password_hash($new_password, PASSWORD_DEFAULT), // Hash password baru
            'reset_token' => NULL, // Hapus token setelah digunakan
            'reset_token_expiry' => NULL // Hapus waktu kadaluarsa token
        );
        $this->db->where('id', $admin_id);
        return $this->db->update('admin', $data);
    }
}