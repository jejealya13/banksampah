<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_pengantaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_jadwal_pengantaran');
        $this->load->helper('url');
    }

    public function index() {
        $isi['content'] = 'jadwal_pengantaran/v_jadwal_pengantaran';
        $isi['judul'] = 'Jadwal Pengantaran';
        $isi['jadwal'] = $this->M_jadwal_pengantaran->get_all();
        $this->load->view('v_dashboard', $isi);
    }

    public function edit($id) {
        if ($this->input->post()) {
            $update_data = [
                'description' => $this->input->post('description'),
                'schedule_date' => $this->input->post('schedule_date'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->M_jadwal_pengantaran->update($id, $update_data);
            redirect('jadwal_pengantaran');
        } else {
            $isi['content'] = 'jadwal_pengantaran/edit_jadwal';
            $isi['judul'] = 'Edit Jadwal Pengantaran';
            $isi['jadwal'] = $this->M_jadwal_pengantaran->get_by_id($id);
            $this->load->view('v_dashboard', $isi);
        }
    }

    public function add() {
        if ($this->input->post()) {
            $insert_data = [
                'description' => $this->input->post('description'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->M_jadwal_pengantaran->insert($insert_data);
            redirect('jadwal_pengantaran');
        } else {
            $isi['content'] = 'jadwal_pengantaran/add_jadwal';
            $isi['judul'] = 'Tambah Jadwal Pengantaran';
            $this->load->view('v_dashboard', $isi);
        }
    }

    public function delete($id) {
        $this->M_jadwal_pengantaran->delete($id);
        redirect('jadwal_pengantaran');
    }
}
