<?php

class M_nasabah extends CI_Model{
    public function idnasabah() // Menggunakan nama fungsi lama
    {
        $this->db->trans_start(); // Mulai transaksi
        $this->db->select('RIGHT(nasabah.idnasabah,3) as kode', FALSE);
        $this->db->order_by('idnasabah', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('nasabah');
        $kode = 1; // Default jika belum ada data
        if ($query->num_rows() > 0) {
            $data = $query->row();
            $last_id_num = (int) $data->kode; // $data->kode sudah berupa angka dari RIGHT(idnasabah,3)
            $kode = $last_id_num + 1;
        }
        
        $kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT);
        $kodejadi = "NS" . $kodemax;
        $this->db->trans_complete(); // Selesaikan transaksi
        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Gagal generate ID nasabah: ' . $this->db->error()['message']);
            return null; // Kembalikan null atau tangani error lebih lanjut
            }
            return $kodejadi;
        }
        public function register_nasabah($data)
        {
            if ($this->is_phone_exists($data['phone'])) {
                return FALSE; // Nomor telepon sudah ada
            }
            // Jika 'namanasabah' kosong atau tidak ada, isi dengan 'username'
            if (empty($data['namanasabah']) && !empty($data['username'])) {
                $data['namanasabah'] = $data['username'];
            }
            return $this->db->insert('nasabah', $data);
        }
    
    public function is_phone_exists($phone)
    {
        $this->db->where('phone', $phone);
        $query = $this->db->get('nasabah');
        return $query->num_rows() > 0;
    }
    public function get_nasabah_by_phone($phone) // FUNGSI BARU UNTUK LOGIN API
    {
        $this->db->where('phone', $phone);
        $query = $this->db->get('nasabah');
        return $query->row(); 
    }
        
    public function get_nasabah_by_username_and_phone($username, $phone)
    {
        $this->db->where('username', $username); // Filter berdasarkan username
        $this->db->where('phone', $phone);// Filter berdasarkan p
        $query = $this->db->get('nasabah');
        return $query->row(); // Mengembalikan satu baris data nasabah atau null jika tidak ditemukan
    }
        
    public function login_nasabah($phone, $password)
    {
        $this->db->where('phone', $phone);
        $this->db->where('password', $password); // Asumsi password di database sudah di-hash
        $query = $this->db->get('nasabah');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return NULL;
    }
    
    public function edit($id)
    {
        $this->db->where('idnasabah', $id);
        return $this->db->get('nasabah')->row_array();
    }



 public function update($idnasabah, $data)

 {

 $this->db->where('idnasabah', $idnasabah);

 return $this->db->update('nasabah', $data); // Perhatikan nilai kembalian

 }



 public function hapus($id)

 {
    $this->db->where('idnasabah', $id);
    $this->db->delete('penabungan');


 $this->db->where('idnasabah', $id);

 $this->db->delete('penarikan');


 $this->db->where('idnasabah', $id);

 $this->db->delete('nasabah');
 }



 public function getAllNasabahWithTotalTabungan()

 {

 $this->db->select('nasabah.idnasabah, nasabah.username, nasabah.phone, nasabah.address, IFNULL(SUM(penabungan.harga), 0) AS total_tabungan');

 $this->db->from('nasabah');

 $this->db->join('penabungan', 'nasabah.idnasabah = penabungan.idnasabah', 'left');

 $this->db->where('penabungan.status', 'Terverifikasi'); // Tambahkan kondisi status terverifikasi

 $this->db->group_by('nasabah.idnasabah');

 return $this->db->get()->result();

 }



 public function getNasabahById($idnasabah)

{

 return $this->db->get_where('nasabah', ['idnasabah' => $idnasabah])->row();

 }



 public function get_all_nasabah()

 {

 return $this->db->get('nasabah')->result();

 }



 public function filter_nasabah_by_id($idnasabah)

{

 $this->db->where('idnasabah', $idnasabah);

 return $this->db->get('nasabah')->result();

 }



public function get_nasabah_by_range($id_min, $id_max)

 {

 $this->db->where('idnasabah >=', $id_min);

 $this->db->where('idnasabah <=', $id_max);

 return $this->db->get('nasabah')->result();

 }



 public function tambah_saldo($idnasabah, $jumlah)

 {

 $this->db->where('idnasabah', $idnasabah);

 $this->db->set('tabungan', 'tabungan + ' . $jumlah, FALSE); // Menambahkan tanpa menginterpretasi sebagai string

return $this->db->update('nasabah');

 }



 public function getTotalTabunganTerverifikasiPerNasabah()

{

 $this->db->select('idnasabah, IFNULL(SUM(harga), 0) AS total_tabungan_terverifikasi');

 $this->db->from('penabungan');

 $this->db->where('status', 'Terverifikasi');

 $this->db->group_by('idnasabah');

 $query = $this->db->get();

 $result = $query->result_array();

 $data = [];

 foreach ($result as $row) {

 $data[$row['idnasabah']] = $row['total_tabungan_terverifikasi'];

 }

 return $data;

}



public function getSaldoTabunganById($idnasabah) {

 $this->db->select('IFNULL(SUM(harga), 0) AS total_tabungan_terverifikasi');
 $this->db->from('penabungan');

 $this->db->where('idnasabah', $idnasabah);

 $this->db->where('status', 'Terverifikasi');

 $query = $this->db->get();

 if ($query->num_rows() > 0) {

 return $query->row()->total_tabungan_terverifikasi;

 } else {

 return 0;

 }

}



public function kurangi_saldo($idnasabah, $jumlah)

{

 log_message('debug', 'Mencoba mengurangi saldo nasabah ID: ' . $idnasabah . ' sebesar: ' . $jumlah);

 $this->db->where('idnasabah', $idnasabah);

 $this->db->set('tabungan', 'tabungan - ' . $jumlah, FALSE);

 $result = $this->db->update('nasabah');

 log_message('debug', 'Hasil update saldo: ' . $this->db->affected_rows());

 return $result;

}



public function getDirectSaldoTabunganById($idnasabah) {

 $this->db->select('tabungan');

 $this->db->where('idnasabah', $idnasabah);

 $query = $this->db->get('nasabah');

 if ($query->num_rows() == 1) {

 return $query->row()->tabungan;

 } else {

 return 0;

 }

}




 public function get_nasabah_by_id($idnasabah) {
	 $this->db->select('idnasabah, username, phone, address, birthdate, tabungan, profile_pic_uri'); // <--- TAMBAHKAN KOLOM INI
	 $this->db->where('idnasabah', $idnasabah);
	 $query = $this->db->get('nasabah');
	 return $query->row(); // Mengembalikan satu baris objek
 }


 public function update_nasabah_profile($idnasabah, $data) {

 $this->db->where('idnasabah', $idnasabah);


 return $this->db->update('nasabah', $data);

}
	
public function update_profile_pic_uri($idnasabah, $profile_pic_uri) {
        $this->db->where('idnasabah', $idnasabah);
        $data = array('profile_pic_uri' => $profile_pic_uri); // Pastikan nama kolom di DB adalah 'profile_pic_uri'
        return $this->db->update('nasabah', $data); // Ganti 'nasabah' jika nama tabel Anda berbeda
    }


}