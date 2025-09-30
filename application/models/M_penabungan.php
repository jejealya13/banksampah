<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_penabungan extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Load library database
    }

    // Fungsi untuk menghasilkan ID penabungan (PN001, PN002, dst.)
    //public function idpenabungan()
    //{
      //  $this->db->select('RIGHT(penabungan.idpenabungan,3) as kode', FALSE);
       // $this->db->order_by('idpenabungan', 'DESC');
        //$this->db->limit(1);
        //$query = $this->db->get('penabungan');
            //if ($query->num_rows() <> 0) {
                //$data = $query->row();
              //  $kode = intval($data->kode) + 1;
            //} else {
            //    $kode = 1;
          //  }
        //$kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT);
        //$kodejadi = "PN" . $kodemax;
        //return $kodejadi;
       // }
    
    // Mendapatkan semua data penabungan
    public function get_all_penabungan()
    {
        $this->db->select('*');
        $this->db->from('penabungan');
		$this->db->order_by('idpenabungan', 'DESC'); // Urutkan berdasarkan ID Penabungan secara DESC
        $query = $this->db->get();
        return $query->result_array(); // Ubah menjadi result_array()
    }

    // Mendapatkan data penabungan berdasarkan ID penabungan
    public function get_penabungan_by_id($idpenabungan)
    {
        $this->db->select('*');
        $this->db->from('penabungan');
        $this->db->where('idpenabungan', $idpenabungan);
        $query = $this->db->get();
        return $query->row();
    }

    // Mendapatkan detail lengkap data penabungan (mungkin termasuk username nasabah dan jenis sampah)
    public function get_penabungan_detail($idpenabungan)
    {
        $this->db->select('penabungan.*, nasabah.nama AS nama_nasabah, data_sampah.jenis_sampah');
        $this->db->from('penabungan');
        $this->db->join('nasabah', 'penabungan.idnasabah = nasabah.idnasabah', 'left');
        $this->db->join('data_sampah', 'penabungan.idsampah = data_sampah.idsampah', 'left');
        $this->db->where('penabungan.idpenabungan', $idpenabungan);
        $query = $this->db->get();
        return $query->row();
    }

    // Memperbarui status transaksi penabungan
    public function update_status($idpenabungan, $status)
    {
        $data = array(
            'status' => $status
        );
        $this->db->where('idpenabungan', $idpenabungan);
        return $this->db->update('penabungan', $data);
    }

    // Metode lain yang mungkin kamu butuhkan:
    // - Mendapatkan data penabungan berdasarkan kriteria tertentu (tanggal, status, dll.)
    // - Menghitung total penabungan
    // - Dan lain-lain

    // Contoh mendapatkan data penabungan berdasarkan status
    public function get_penabungan_by_status($status)
    {
        $this->db->select('*');
        $this->db->from('penabungan');
        $this->db->where('status', $status);
        $query = $this->db->get();
        return $query->result();
    }

    // m_penabungan.php

    public function get_harga_penabungan($idpenabungan)
    {
        $this->db->select('idnasabah, harga');
        $this->db->where('idpenabungan', $idpenabungan);
        return $this->db->get('penabungan')->row();
    }

    public function insert_penabungan($data)
    {
        log_message('debug', 'Data yang akan di-insert ke database: ' . json_encode($data));
        $this->db->insert('penabungan', $data);
    
        // Tambahkan kode pemeriksaan hasil insert di sini
        if ($this->db->affected_rows() > 0) {
            log_message('debug', 'Insert penabungan berhasil.');
            return true; // Insert penabungan berhasil
        } else {
            log_message('error', 'Insert penabungan gagal. Error database: ' . json_encode($this->db->error()));
            return false; // Insert penabungan gagal
        }
    }

    public function update_penabungan($idpenabungan, $data)
    {
        $this->db->where('idpenabungan', $idpenabungan);
        return $this->db->update('penabungan', $data);
    }

    public function delete_penabungan($idpenabungan)
    {
        $this->db->where('idpenabungan', $idpenabungan);
        return $this->db->delete('penabungan');
    }

    public function get_penabungan_by_nasabah_dan_status($idnasabah, $status)
    {
        $this->db->select('*');
        $this->db->from('penabungan');
        $this->db->where('idnasabah', $idnasabah);
        $this->db->where('status', $status);
        $this->db->order_by('tanggal', 'DESC'); // Urutkan berdasarkan tanggal terbaru
        $query = $this->db->get();
        return $query->result();
    }

    public function mark_as_deleted($idpenabungan)
{
    $this->db->where('idpenabungan', $idpenabungan);
    return $this->db->update('penabungan', ['is_deleted' => 1]);
}

    public function get_penabungan_by_date_and_status($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('penabungan');
        $this->db->where('status', 'Terverifikasi');
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $query = $this->db->get();
        return $query->result(); // Mengembalikan objek
    }
}