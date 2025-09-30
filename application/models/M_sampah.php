<?php

class M_sampah extends CI_Model {

    // Menghasilkan ID sampah dalam format S001, S002, dst.
    public function idsampah() {
        $this->db->select('RIGHT(sampah.idsampah, 3) as kode', FALSE);
        $this->db->order_by('idsampah', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('sampah');
        
        if ($query->num_rows() <> 0) {
            $data = $query->row();
            $kode = intval($data->kode) + 1;
        } else {
            $kode = 1;
        }
        
        $kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT);
        $kodejadi = "S" . $kodemax;
    
        // Cek jika ID sudah ada
        while ($this->db->get_where('sampah', ['idsampah' => $kodejadi])->num_rows() > 0) {
            $kode++;
            $kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT);
            $kodejadi = "S" . $kodemax;
        }
    
        return $kodejadi;
    }

    // Mengambil semua data sampah
    public function get_data_sampah($limit = null, $start = null) {
        if ($limit && $start) {
            $this->db->limit($limit, $start);
        }
        return $this->db->get('sampah')->result();
    }

    public function insert($data) {
    $result = $this->db->insert('sampah', $data);
    log_message('info', 'Insert Result: ' . json_encode($result));
    return $result; // Pastikan ini mengembalikan true/false
}
    public function edit($id) {
        return $this->db->get_where('sampah', array('idsampah' => $id))->row_array();
    }

    public function update($id, $data) {
        $this->db->where('idsampah', $id);
        $this->db->update('sampah', $data);
    }

    public function hapus($id) {
        // === BARIS TAMBAHAN UNTUK MENGATASI FOREIGN KEY CONSTRAINT ===

        // 1. Hapus semua transaksi penabungan terkait dengan sampah ini terlebih dahulu
        //    Pastikan 'penabungan' adalah nama tabel yang benar di database kamu.
        $this->db->where('idsampah', $id); // Menggunakan idsampah
        $this->db->delete('penabungan');

        // 2. Jika ada tabel lain yang juga terkait dengan 'sampah' melalui 'idsampah',
        //    kamu perlu menghapusnya juga di sini sebelum menghapus dari tabel 'sampah'.
        //    Contoh: Jika ada tabel 'sampah_masuk' yang terhubung dengan 'sampah':
        //    $this->db->where('idsampah', $id);
        //    $this->db->delete('sampah_masuk');

        // === AKHIR DARI BARIS TAMBAHAN ===

        // 3. Kemudian, hapus data sampah itu sendiri
        $this->db->where('idsampah', $id);
        $this->db->delete('sampah');
    }

    public function get_last_id() {
        $this->db->select('idsampah');
        $this->db->order_by('idsampah', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('sampah');
    
        if ($query->num_rows() > 0) {
            return $query->row()->idsampah;
        } else {
            return 'S0'; // Jika tidak ada data, mulai dari S0
        }
    }

    public function get_all_sampah() {
        return $this->db->get('sampah')->result();
    }

    public function filter_sampah_by_id($idsampah) {
        $this->db->where('idsampah', $idsampah);
        return $this->db->get('sampah')->result();
    }

    public function get_sampah_by_range($id_min, $id_max) {
        $this->db->where('idsampah >=', $id_min);
        $this->db->where('idsampah <=', $id_max);
        return $this->db->get('sampah')->result();
    }
    public function get_all_harga_sampah() {
        return $this->db->get('sampah')->result();
    }

    // Mendapatkan harga satuan sampah berdasarkan ID
    public function get_harga_by_id($idsampah)
    {
        $this->db->select('harga');
        $this->db->where('idsampah', $idsampah);
        $query = $this->db->get('sampah'); // Menggunakan nama tabel 'sampah'

        if ($query->num_rows() == 1) {
            return $query->row()->harga;
        } else {
            return 0; // Mengembalikan 0 jika ID sampah tidak ditemukan atau tidak ada harga
        }
    }

    public function get_sampah_by_id($idsampah)
    {
        $this->db->where('idsampah', $idsampah);
        return $this->db->get('sampah')->row(); // Mengembalikan satu baris data sebagai objek
    }

}