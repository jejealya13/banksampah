<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_squrity extends CI_Model{

    public function getSqurity()
    {
        // Mengecek apakah sesi 'logged_in' ada dan bernilai TRUE
        if ($this->session->userdata('logged_in') !== TRUE) {
            $this->session->set_flashdata('info', '<div class="alert alert-warning" role="alert">Anda harus login untuk mengakses halaman ini.</div>');
            $this->session->sess_destroy(); // Hancurkan sesi jika tidak valid
            redirect('login');
            exit(); // Penting untuk menghentikan eksekusi setelah redirect
        }
    }
}