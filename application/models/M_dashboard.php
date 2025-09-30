<?php

class M_dashboard extends CI_Model {
    public function countNasabah() {
        return $this->db->count_all('nasabah');
    }

    public function countSampah() {
        return $this->db->count_all('sampah');
    }

    public function countTransaksiPenabungan() {
        return $this->db->count_all('penabungan');
    }

    public function countTransaksiPenarikan() {
        return $this->db->count_all('penarikan');
    }

    public function getJenisSampahTerbanyak() {
        $this->db->select('s.jenis AS jenis_sampah, COUNT(p.idsampah) AS jumlah');
        $this->db->from('penabungan p');
        $this->db->join('sampah s', 'p.idsampah = s.idsampah');
        $this->db->where('p.status', 'Terverifikasi'); // Hanya ambil yang terverifikasi
        $this->db->group_by('s.jenis');
        $this->db->order_by('jumlah', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getNasabahTerdaftarPerTanggal() {
        $this->db->select('DATE(tanggal_daftar) AS tanggal, COUNT(idnasabah) AS jumlah');
        $this->db->from('nasabah');
        $this->db->group_by('DATE(tanggal_daftar)');
        $this->db->order_by('tanggal_daftar');
        $query = $this->db->get();
        return $query->result_array();
    }
}