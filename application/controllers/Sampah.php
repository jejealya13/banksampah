<?php

class Sampah extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_sampah');
        $this->load->model('M_logaktivitas'); // <<< PASTIKAN INI ADA

        // Tambahkan pengecekan login di sini
        if (!$this->session->userdata('logged_in')) {
            redirect('login'); // Redirect ke halaman login
        }
    }

    public function index() {
        $isi['content'] = 'sampah/v_sampah';
        $isi['judul']   = 'Daftar Data Sampah';
        $isi['data']    = $this->m_sampah->get_data_sampah();
        $this->load->view('v_dashboard', $isi);
    }

    public function tambah_sampah() {
        $isi['content'] = 'sampah/form_sampah';
        $isi['judul'] = 'Form Tambah Sampah';
        $this->load->view('v_dashboard', $isi);
    }

    public function simpan() {
        $newId = $this->m_sampah->idsampah();
        
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('gambar')) {
            $berat = $this->input->post('berat');
            
            // Ambil harga dan format sesuai
            $totalHarga = $this->input->post('harga');
            
            // Menghapus karakter yang tidak perlu
            $formattedHarga = preg_replace('/[Rp. ]/', '', $totalHarga); // Menghapus Rp dan spasi
            $formattedHarga = str_replace(',', '', $formattedHarga); // Menghapus koma jika ada
            $formattedHarga = number_format((float)$formattedHarga, 2, '.', ''); // Format menjadi desimal
    
            $data = array(
                'idsampah' => $newId,
                'jenis' => $this->input->post('jenis'),
                'gambar' => $this->upload->data('file_name'),
                'berat' => $berat,
                'harga' => $formattedHarga // Menyimpan harga dalam format desimal
            );
        
            if ($this->m_sampah->insert($data)) {
                $this->session->set_flashdata('info', 'Data berhasil disimpan');

                // --- LOG AKTIVITAS: Tambah Jenis Sampah (Berhasil) ---
                $user_email = $this->session->userdata('email');
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Tambah Jenis Sampah',
                    'Berhasil menambahkan jenis sampah: ' . $data['jenis'] . ' (ID: ' . $data['idsampah'] . ')'
                );
                // --- END LOG AKTIVITAS ---

                redirect('sampah');
            } else {
                $this->session->set_flashdata('info', 'Data gagal disimpan. Silakan coba lagi.');

                // --- LOG AKTIVITAS: Tambah Jenis Sampah (Gagal Insert DB) ---
                $user_email = $this->session->userdata('email');
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Tambah Jenis Sampah',
                    'Gagal menambahkan jenis sampah: ' . $data['jenis'] . '. Kemungkinan masalah pada insert database.'
                );
                // --- END LOG AKTIVITAS ---

                redirect('sampah/tambah_sampah');
            }
        } else {
            $this->session->set_flashdata('info', 'Upload gagal: ' . $this->upload->display_errors());

            // --- LOG AKTIVITAS: Tambah Jenis Sampah (Gagal Upload Gambar) ---
            $user_email = $this->session->userdata('email');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Tambah Jenis Sampah',
                'Gagal menambahkan jenis sampah: ' . $this->input->post('jenis') . ' karena upload gambar gagal. Pesan: ' . $this->upload->display_errors()
            );
            // --- END LOG AKTIVITAS ---

            redirect('sampah/tambah_sampah');
        }
    }

    public function edit($id) {
        $isi['content'] = 'sampah/edit_sampah';
        $isi['judul'] = 'Form Edit Sampah';
        $isi['data'] = $this->m_sampah->edit($id); // Ini akan mengembalikan array_row()
        $this->load->view('v_dashboard', $isi);
    }

    public function update() {
        $id = $this->input->post('idsampah');
        // Ambil data sampah lama untuk logging
        // Karena edit() mengembalikan array_row(), kita akses sebagai array
        $sampah_lama = $this->m_sampah->edit($id); 

        $berat = $this->input->post('berat');
        $totalHarga = $this->input->post('harga');
        
        // Menghapus karakter yang tidak perlu
        $formattedHarga = preg_replace('/[Rp. ]/', '', $totalHarga); // Menghapus Rp dan spasi
        $formattedHarga = str_replace(',', '', $formattedHarga); // Menghapus koma jika ada
        
        // Pastikan harga adalah angka
        if (!is_numeric($formattedHarga)) {
            $this->session->set_flashdata('info', 'Harga tidak valid.');

            // --- LOG AKTIVITAS: Update Jenis Sampah (Gagal Harga Tidak Valid) ---
            $user_email = $this->session->userdata('email');
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Gagal Update Jenis Sampah',
                'Gagal memperbarui jenis sampah (ID: ' . $id . ') karena harga tidak valid. Jenis: ' . ($sampah_lama['jenis'] ?? 'Tidak Diketahui') // Akses sebagai array
            );
            // --- END LOG AKTIVITAS ---

            redirect('sampah/edit/' . $id);
            return;
        }
        
        // Format menjadi desimal
        $formattedHarga = number_format((float)$formattedHarga, 2, '.', '');
        
        $data = array(
            'jenis' => $this->input->post('jenis'),
            'berat' => $berat,
            'harga' => $formattedHarga // Menyimpan harga dalam format desimal
        );
        
        // Cek jika gambar baru diupload
        if (!empty($_FILES['gambar']['name'])) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $this->load->library('upload', $config); 
            
            if ($this->upload->do_upload('gambar')) {
                // Ini adalah bagian dari fungsionalitas, bukan log aktivitas
                if ($sampah_lama && !empty($sampah_lama['gambar']) && file_exists('./uploads/' . $sampah_lama['gambar'])) { // Akses sebagai array
                    unlink('./uploads/' . $sampah_lama['gambar']);
                }
                $data['gambar'] = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('info', 'Upload gagal: ' . $this->upload->display_errors());

                // --- LOG AKTIVITAS: Update Jenis Sampah (Gagal Upload Gambar) ---
                $user_email = $this->session->userdata('email');
                $this->M_logaktivitas->log_aktivitas(
                    $user_email,
                    'Gagal Update Jenis Sampah',
                    'Gagal memperbarui jenis sampah (ID: ' . $id . ') karena upload gambar gagal. Pesan: ' . $this->upload->display_errors()
                );
                // --- END LOG AKTIVITAS ---

                redirect('sampah/edit/' . $id);
                return;
            }
        }
        
        $this->m_sampah->update($id, $data); // Ini tidak mengembalikan boolean di model Anda
        $this->session->set_flashdata('info', 'Data berhasil diupdate');

        // --- LOG AKTIVITAS: Update Jenis Sampah (Berhasil - Berdasarkan Notifikasi) ---
        // Log ini akan selalu dieksekusi karena redirect ada setelah set_flashdata
        $user_email = $this->session->userdata('email');
        $deskripsi_log = 'Berhasil memperbarui jenis sampah (ID: ' . $id . '). ';
        if ($sampah_lama) {
            $deskripsi_log .= 'Dari "' . $sampah_lama['jenis'] . '" menjadi "' . $data['jenis'] . '". '; // Akses sebagai array
            $deskripsi_log .= 'Berat: ' . $sampah_lama['berat'] . ' -> ' . $data['berat'] . '. '; // Akses sebagai array
            $deskripsi_log .= 'Harga: Rp' . number_format($sampah_lama['harga'], 0, ',', '.') . ' -> Rp' . number_format($data['harga'], 0, ',', '.'); // Akses sebagai array
        } else {
            $deskripsi_log .= 'Jenis: ' . $data['jenis'] . ', Berat: ' . $data['berat'] . ', Harga: Rp' . number_format($data['harga'], 0, ',', '.');
        }
        $this->M_logaktivitas->log_aktivitas(
            $user_email,
            'Update Jenis Sampah',
            $deskripsi_log
        );
        // --- END LOG AKTIVITAS ---

        redirect('sampah');
    }

    public function hapus($id) {
        // Ambil data sampah sebelum dihapus untuk logging
        $sampah_info = $this->m_sampah->edit($id); // Ini akan mengembalikan array_row()

        $this->m_sampah->hapus($id); // Ini tidak mengembalikan boolean di model Anda
        $this->session->set_flashdata('info', 'Data berhasil dihapus');

        // --- LOG AKTIVITAS: Hapus Jenis Sampah (Berhasil - Berdasarkan Notifikasi) ---
        // Log ini akan selalu dieksekusi karena redirect ada setelah set_flashdata
        $user_email = $this->session->userdata('email');
        $this->M_logaktivitas->log_aktivitas(
            $user_email,
            'Hapus Jenis Sampah',
            'Berhasil menghapus jenis sampah: ' . ($sampah_info['jenis'] ?? 'Tidak Diketahui') . ' (ID: ' . ($sampah_info['idsampah'] ?? $id) . ')' // Akses sebagai array
        );
        // --- END LOG AKTIVITAS ---

        redirect('sampah');
    }
}