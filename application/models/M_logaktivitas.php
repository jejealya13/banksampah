<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_logaktivitas extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function log_aktivitas($email, $aktivitas, $deskripsi = null) {
        $data = array(
            'email' => $email,
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'timestamp' => date('Y-m-d H:i:s')
        );
        $this->db->insert('log_aktivitas', $data);
    }

    // --- TAMBAHKAN FUNGSI INI ---
    public function getLogAktivitas() {
        // Ambil semua data dari tabel log_aktivitas
        // Anda bisa menambahkan order_by, limit, dll. sesuai kebutuhan
        $this->db->order_by('timestamp', 'DESC'); // Urutkan dari yang terbaru
        $query = $this->db->get('log_aktivitas');
        return $query->result(); // Mengembalikan hasil dalam bentuk array of objects
    }

    // Anda juga bisa menambahkan fungsi untuk mendapatkan log berdasarkan filter, misalnya:
    public function get_logs_by_email($email) {
        $this->db->where('email', $email);
        $this->db->order_by('timestamp', 'DESC');
        $query = $this->db->get('log_aktivitas');
        return $query->result();
    }

    public function get_logs_paginated($limit, $offset) {
        $this->db->order_by('timestamp', 'DESC');
        $query = $this->db->get('log_aktivitas', $limit, $offset);
        return $query->result();
    }

    public function count_all_logs() {
        return $this->db->count_all('log_aktivitas');
    }
}