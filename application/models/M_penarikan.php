<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penarikan extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Memastikan database terhubung
    }

    public function idpenarikan()
    {
        $this->db->select('RIGHT(penarikan.idpenarikan,3) as kode', FALSE);
        $this->db->order_by('idpenarikan', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('penarikan');
        if ($query->num_rows() <> 0) {
            $data = $query->row();
            $kode = intval($data->kode) + 1;
        } else {
            $kode = 1;
        }
        $kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT);
        $kodejadi = "TS" . $kodemax;
        return $kodejadi;
    }

    public function getAllPengajuanPenarikanWithNamaNasabah()
    {
        $this->db->select('penarikan.*, nasabah.username AS nama_nasabah');
        $this->db->from('penarikan');
        $this->db->join('nasabah', 'penarikan.idnasabah = nasabah.idnasabah', 'left'); // Menggunakan LEFT JOIN agar tetap menampilkan penarikan meskipun nasabah tidak ditemukan
        $this->db->order_by('penarikan.idpenarikan', 'DESC');
        return $this->db->get()->result();
    }

    public function getPengajuanPenarikanById($idpenarikan)
    {
        // Pastikan kolom-kolom ini ada di tabel 'penarikan' dan 'nasabah'
        $this->db->select('penarikan.*, nasabah.username AS nama_nasabah, penarikan.metode AS metode, penarikan.noRek AS noRek'); // Sesuaikan 'metode_pembayaran' dan 'nomor_rekening' jika berbeda
        $this->db->from('penarikan');
        $this->db->join('nasabah', 'penarikan.idnasabah = nasabah.idnasabah', 'left');
        $this->db->where('penarikan.idpenarikan', $idpenarikan);
        return $this->db->get()->row();
    }

    public function updateStatusPenarikan($idpenarikan, $data)
    {
        $this->db->where('idpenarikan', $idpenarikan);
        return $this->db->update('penarikan', $data); // Mengembalikan TRUE/FALSE
    }

    public function insertPengajuanPenarikan($data)
    {
        return $this->db->insert('penarikan', $data);
    }

    //untuk filter laporan berdasarkan tanggal
    public function get_penarikan_by_date_and_status($start_date = null, $end_date = null) {
        $this->db->select('penarikan.*, nasabah.username AS nama_nasabah');
        $this->db->from('penarikan');
        $this->db->join('nasabah', 'penarikan.idnasabah = nasabah.idnasabah');
        $this->db->where('penarikan.status', 'Selesai');

        if ($start_date !== null) {
            $this->db->where('DATE(penarikan.tanggal) >=', $start_date);
        }

        if ($end_date !== null) {
            $this->db->where('DATE(penarikan.tanggal) <=', $end_date);
        }

        return $this->db->get()->result();
    }

    // Metode ini seharusnya sudah tidak diperlukan langsung di view, tapi kalau ada kebutuhan lain biarkan saja
    public function getAllPengajuanPenarikanWithNamaNasabahByStatus($status) {
        $this->db->select('penarikan.*, nasabah.username AS nama_nasabah');
        $this->db->from('penarikan');
        $this->db->join('nasabah', 'penarikan.idnasabah = nasabah.idnasabah', 'left');
        $this->db->where('penarikan.status', $status);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    // --- Opsional: Tambahkan method delete jika diperlukan ---
    // public function deletePenarikan($idpenarikan)
    // {
    //     $this->db->where('idpenarikan', $idpenarikan);
    //     return $this->db->delete('penarikan');
    // }
}