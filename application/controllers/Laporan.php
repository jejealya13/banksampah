<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_nasabah');
        $this->load->model('m_sampah');
        $this->load->model('m_penarikan'); 
        $this->load->model('m_penabungan');
		$this->load->model('M_logaktivitas'); // <<< TAMBAH BARIS INI UNTUK MEMUAT MODEL LOG AKTIVITAS

        // Tambahkan pengecekan login di sini
        if (!$this->session->userdata('logged_in')) {
            redirect('login'); // Redirect ke halaman login
        }
    }

    // Metode untuk menampilkan menu laporan
    public function index() {
        $isi['content'] = 'laporan/v_laporan'; // Pastikan ini merujuk ke view laporan
        $isi['judul'] = 'Menu Laporan'; // Judul untuk halaman laporan
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard
    }

    public function nasabah() {
        // Ambil input dari query string
        $id_min = $this->input->get('id_min');
        $id_max = $this->input->get('id_max');

        $isi['data'] = [];
		
	// --- AWAL MODIFIKASI LOG AKTIVITAS ---
    $is_filtered_request = (!empty($id_min) || !empty($id_max));

    // Ambil status flashdata sebelum digunakan
    $has_logged_this_session = $this->session->flashdata('logged_nasabah_viewed_this_session');

    // Log aktivitas hanya akan dicatat jika:
    // 1. Ini BUKAN permintaan yang menggunakan filter (URL bersih tanpa parameter filter)
    // 2. DAN flashdata 'logged_nasabah_viewed_this_session' belum di-set.
    if (!$is_filtered_request && !$has_logged_this_session) {
        $user_email = $this->session->userdata('email');
        if ($user_email) {
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Melihat Laporan Nasabah',
                'Admin melihat laporan nasabah.'
            );
            // Set flashdata untuk mencegah log pada refresh berikutnya
            $this->session->set_flashdata('logged_nasabah_viewed_this_session', TRUE);
        }
    }

    // PENTING: Jika flashdata sudah ada, pertahankan untuk request berikutnya
    // Ini memastikan flashdata tidak hilang setelah refresh pertama
    if ($has_logged_this_session) {
        $this->session->keep_flashdata('logged_nasabah_viewed_this_session');
    }
    // --- AKHIR MODIFIKASI LOG AKTIVITAS ---

        // Cek apakah input ID min dan max tidak kosong
        if (!empty($id_min) && !empty($id_max)) {
            // Ambil data berdasarkan rentang ID (mencari angka di seluruh ID)
            $all_nasabah = $this->m_nasabah->get_all_nasabah();
            foreach ($all_nasabah as $nasabah) {
                $id_nasabah = $nasabah->idnasabah;
                // Hilangkan 'NS' jika ada untuk membandingkan angka
                $numeric_id = str_replace('NS', '', $id_nasabah);
                if (is_numeric($numeric_id) && $numeric_id >= $id_min && $numeric_id <= $id_max) {
                    $isi['data'][] = $nasabah;
                } elseif ($id_nasabah >= 'NS' . str_pad($id_min, 3, '0', STR_PAD_LEFT) && $id_nasabah <= 'NS' . str_pad($id_max, 3, '0', STR_PAD_LEFT)) {
                    $isi['data'][] = $nasabah;
                } elseif ($id_nasabah >= $id_min && $id_nasabah <= $id_max && !is_numeric(str_replace('NS', '', $id_nasabah))) {
                    // Kondisi untuk ID yang mungkin tidak berformat 'NS' tapi berupa angka
                    $isi['data'][] = $nasabah;
                }
            }
        } else {
            // Jika tidak ada filter, ambil semua data nasabah
            $isi['data'] = $this->m_nasabah->get_all_nasabah();
        }

        $isi['content'] = 'laporan/v_laporan_nasabah'; // View untuk laporan nasabah
        $isi['judul'] = 'Laporan Nasabah'; // Judul untuk halaman laporan nasabah
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard
    }

    public function sampah() {
        // Ambil input dari query string
        $id_min = $this->input->get('id_min');
        $id_max = $this->input->get('id_max');
		
	// --- AWAL MODIFIKASI LOG AKTIVITAS ---
    // Tentukan apakah ada parameter filter di URL
    $is_filtered_request = (!empty($id_min) || !empty($id_max));

    // Ambil status flashdata sebelum digunakan
    // PERHATIKAN: Nama flashdata diubah agar unik untuk halaman Sampah
    $has_logged_this_session = $this->session->flashdata('logged_sampah_viewed_this_session');

    // Log aktivitas hanya akan dicatat jika:
    // 1. Ini BUKAN permintaan yang menggunakan filter (URL bersih tanpa parameter filter)
    // 2. DAN flashdata 'logged_sampah_viewed_this_session' belum di-set.
    if (!$is_filtered_request && !$has_logged_this_session) {
        $user_email = $this->session->userdata('email');
        if ($user_email) {
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Melihat Laporan Sampah', // Deskripsi log untuk Laporan Sampah
                'Admin melihat laporan sampah.'
            );
            // Set flashdata untuk mencegah log pada refresh berikutnya
            $this->session->set_flashdata('logged_sampah_viewed_this_session', TRUE);
        }
    }

    // PENTING: Jika flashdata sudah ada, pertahankan untuk request berikutnya
    // Ini memastikan flashdata tidak hilang setelah refresh pertama
    if ($has_logged_this_session) {
        $this->session->keep_flashdata('logged_sampah_viewed_this_session');
    }
    // --- AKHIR MODIFIKASI LOG AKTIVITAS ---
    
        // Cek apakah input ID min dan max tidak kosong
        if (!empty($id_min) && !empty($id_max)) {
            // Tambahkan prefiks 'S' dan format dengan padding nol
            $id_min_formatted = 'S' . str_pad($id_min, 3, '0', STR_PAD_LEFT);
            $id_max_formatted = 'S' . str_pad($id_max, 3, '0', STR_PAD_LEFT);
            
            // Ambil data berdasarkan rentang ID
            $isi['data'] = $this->m_sampah->get_sampah_by_range($id_min_formatted, $id_max_formatted);
        } else {
            // Jika tidak ada filter, ambil semua data sampah
            $isi['data'] = $this->m_sampah->get_all_sampah();
        }
    
        $isi['content'] = 'laporan/v_laporan_sampah'; // View untuk laporan sampah
        $isi['judul'] = 'Laporan Sampah'; // Judul untuk halaman laporan sampah
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard
    }

    public function penarikan() {
        $start_date_input = $this->input->get('start_date');
        $end_date_input = $this->input->get('end_date');

        $start_date = null;
        $end_date = null;

        if (!empty($start_date_input)) {
            $start_date = date('Y-m-d', strtotime(str_replace('-', '/', $start_date_input)));
        }

        if (!empty($end_date_input)) {
            $end_date = date('Y-m-d', strtotime(str_replace('-', '/', $end_date_input)));
        }

        log_message('debug', 'Start Date (Converted): ' . $start_date);
        log_message('debug', 'End Date (Converted): ' . $end_date);

        $isi['data'] = $this->m_penarikan->get_penarikan_by_date_and_status($start_date, $end_date);
        log_message('debug', 'Data Penarikan (Filtered): ' . json_encode($isi['data']));

        $isi['content'] = 'laporan/v_laporan_penarikan';
        $isi['judul'] = 'Laporan Penarikan';
        $this->load->view('v_dashboard', $isi);
		
	// --- AWAL MODIFIKASI LOG AKTIVITAS ---
    $is_filtered_request = (!empty($start_date_input) || !empty($end_date_input));

    // Ambil status flashdata sebelum digunakan
    $has_logged_this_session = $this->session->flashdata('logged_penarikan_viewed_this_session');

    // Log aktivitas hanya akan dicatat jika:
    // 1. Ini BUKAN permintaan yang menggunakan filter (URL bersih tanpa parameter filter tanggal)
    // 2. DAN flashdata 'logged_penarikan_viewed_this_session' belum di-set.
    if (!$is_filtered_request && !$has_logged_this_session) {
        $user_email = $this->session->userdata('email');
        if ($user_email) {
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Melihat Laporan Penarikan',
                'Admin melihat laporan penarikan.'
            );
            // Set flashdata untuk mencegah log pada refresh berikutnya
            $this->session->set_flashdata('logged_penarikan_viewed_this_session', TRUE);
        }
    }

    // PENTING: Jika flashdata sudah ada, pertahankan untuk request berikutnya
    // Ini memastikan flashdata tidak hilang setelah refresh pertama
    if ($has_logged_this_session) {
        $this->session->keep_flashdata('logged_penarikan_viewed_this_session');
    }
    // --- AKHIR MODIFIKASI LOG AKTIVITAS ---
		
    }

    public function penabungan() {
        // Ambil input dari query string
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        // Cek apakah input tanggal tidak kosong
        if (!empty($start_date) && !empty($end_date)) {
            // Ambil data penabungan berdasarkan rentang tanggal dan status terverifikasi
            $isi['data'] = $this->m_penabungan->get_penabungan_by_date_and_status($start_date, $end_date);
        } else {
            // Jika tidak ada filter, ambil semua data penabungan yang terverifikasi
            $isi['data'] = $this->m_penabungan->get_penabungan_by_status('Terverifikasi');
        }
		
	// --- AWAL MODIFIKASI LOG AKTIVITAS ---
    $is_filtered_request = (!empty($start_date_input) || !empty($end_date_input));

    // Ambil status flashdata sebelum digunakan
    $has_logged_this_session = $this->session->flashdata('logged_penabungan_viewed_this_session');

    // Log aktivitas hanya akan dicatat jika:
    // 1. Ini BUKAN permintaan yang menggunakan filter (URL bersih tanpa parameter filter tanggal)
    // 2. DAN flashdata 'logged_penabungan_viewed_this_session' belum di-set.
    if (!$is_filtered_request && !$has_logged_this_session) {
        $user_email = $this->session->userdata('email');
        if ($user_email) {
            $this->M_logaktivitas->log_aktivitas(
                $user_email,
                'Melihat Laporan Penabungan',
                'Admin melihat laporan penabungan.'
            );
            // Set flashdata untuk mencegah log pada refresh berikutnya
            $this->session->set_flashdata('logged_penabungan_viewed_this_session', TRUE);
        }
    }

    // PENTING: Jika flashdata sudah ada, pertahankan untuk request berikutnya
    // Ini memastikan flashdata tidak hilang setelah refresh pertama
    if ($has_logged_this_session) {
        $this->session->keep_flashdata('logged_penabungan_viewed_this_session');
    }
    // --- AKHIR MODIFIKASI LOG AKTIVITAS ---

        $isi['content'] = 'laporan/v_laporan_penabungan'; // View untuk laporan penabungan
        $isi['judul'] = 'Laporan Penabungan'; // Judul untuk halaman laporan penabungan
        $this->load->view('v_dashboard', $isi); // Mengarahkan ke view dashboard
    }

    public function export_laporan_pdf($jenis_laporan) {
        // Pastikan path ini sesuai dengan struktur folder Anda
        if (!class_exists('TCPDF')) {
            require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
        }
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $id_min = $this->input->get('id_min');
        $id_max = $this->input->get('id_max');
        $data = [];
        $judul = '';
        $header_kolom = [];
        $cell_width = [];
    
        switch ($jenis_laporan) {

    case 'nasabah':
        $this->load->model('m_nasabah');
        $total_tabungan_per_nasabah = $this->m_nasabah->getTotalTabunganTerverifikasiPerNasabah();
        $data = [];
        if (!empty($id_min) && !empty($id_max)) {
            $all_nasabah = $this->m_nasabah->get_all_nasabah(); // Ambil semua data nasabah
            foreach ($all_nasabah as $nasabah) {
                $id_nasabah = $nasabah->idnasabah;
                $numeric_id = str_replace('NS', '', $id_nasabah);

                $is_within_range = false;
                if (is_numeric($numeric_id) && $numeric_id >= $id_min && $numeric_id <= $id_max) {
                    $is_within_range = true;
                } elseif ($id_nasabah >= 'NS' . str_pad($id_min, 3, '0', STR_PAD_LEFT) && $id_nasabah <= 'NS' . str_pad($id_max, 3, '0', STR_PAD_LEFT)) {
                    $is_within_range = true;
                } elseif ($id_nasabah == $id_min || $id_nasabah == $id_max) {
                    $is_within_range = true;
                }

                if ($is_within_range) {
                    // Tambahkan total tabungan ke dalam objek nasabah
                    $nasabah->total_tabungan = isset($total_tabungan_per_nasabah[$id_nasabah]) ? $total_tabungan_per_nasabah[$id_nasabah] : 0;
                    $data[] = $nasabah;
                }
            }
        } else {
            $all_nasabah = $this->m_nasabah->get_all_nasabah(); // Ambil semua data nasabah
            foreach ($all_nasabah as $nasabah) {
                $nasabah->total_tabungan = isset($total_tabungan_per_nasabah[$nasabah->idnasabah]) ? $total_tabungan_per_nasabah[$nasabah->idnasabah] : 0;
                $data[] = $nasabah;
            }
        }
        $judul = 'Laporan Data Nasabah';
        $header_kolom = ['ID Nasabah', 'Nama Nasabah', 'No Telp', 'Alamat', 'Tabungan'];
        $cell_width = [25, 40, 30, 45, 30];
        break;

            case 'sampah':
                $this->load->model('m_sampah');
                if (!empty($id_min) && !empty($id_max)) {
                    $id_min_formatted = 'S' . str_pad($id_min, 3, '0', STR_PAD_LEFT);
                    $id_max_formatted = 'S' . str_pad($id_max, 3, '0', STR_PAD_LEFT);
                    $data = $this->m_sampah->get_sampah_by_range($id_min_formatted, $id_max_formatted);
                } else {
                    $data = $this->m_sampah->get_all_sampah();
                }
                $judul = 'Laporan Data Sampah';
                $header_kolom = ['ID Sampah', 'Jenis', 'Gambar', 'Berat', 'Harga'];
                $cell_width = [30, 50, 30, 25, 45];
                break;

            case 'penabungan':
                $this->load->model('m_penabungan');
                if (!empty($start_date) && !empty($end_date)) {
                    $data = $this->m_penabungan->get_penabungan_by_date_and_status($start_date, $end_date);
                } else {
                    $data = $this->m_penabungan->get_penabungan_by_status('Terverifikasi');
                }
                $judul = 'Laporan Data Penabungan';
                $header_kolom = ['ID Penabungan', 'ID Nasabah', 'ID Sampah', 'Tanggal', 'Berat', 'Gambar', 'Harga', 'Status'];
                $cell_width = [30, 20, 20, 20, 15, 30, 25, 20]; // Sesuaikan lebar kolom
                break;

            case 'penarikan':
                $this->load->model('m_penarikan');
                $start_date = $this->input->get('start_date');
                $end_date = $this->input->get('end_date');

                if (!empty($start_date) && !empty($end_date)) {
                    $data = $this->m_penarikan->get_penarikan_by_date_and_status($start_date, $end_date);
                } else {
                    $data = $this->m_penarikan->getAllPengajuanPenarikanWithNamaNasabahByStatus('Selesai');
                }
                $judul = 'Laporan Data Penarikan';
                $header_kolom = ['ID Penarikan', 'ID Nasabah', 'Nama Nasabah', 'Metode', 'No Rek', 'Tanggal Pengajuan', 'Nominal'];
                $cell_width = [30, 20, 30, 20, 30, 35, 20];
                break;

                default:
                show_error('Jenis laporan tidak valid.');
                return;
            }
    
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Bank Sampah');
        $pdf->SetTitle($judul);
        $pdf->SetSubject($judul);
        $pdf->SetKeywords('laporan, ' . $jenis_laporan . ', bank sampah');
        $pdf->SetHeaderData('', 0, $judul, '');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage();
    
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');
    
        // Cetak header tabel
        $num_headers = count($header_kolom);
        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf->MultiCell($cell_width[$i], 7, $header_kolom[$i], 1, 'C', 1, 0);
        }
        $pdf->Ln();
        $pdf->SetFont('');
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetTextColor(0);
    
        $fill = 0;
        if (!empty($data)) {
            foreach ($data as $row) {
                $row_height = 0;
                switch ($jenis_laporan) {
                    case 'sampah':
                        $row_height = max(
                            $pdf->getStringHeight($cell_width[0], $row->idsampah),
                            $pdf->getStringHeight($cell_width[1], $row->jenis),
                            20, // Perkiraan tinggi gambar
                            $pdf->getStringHeight($cell_width[3], $row->berat),
                            $pdf->getStringHeight($cell_width[4], 'Rp ' . number_format($row->harga, 0, ',', '.') . '/' . strtolower(trim(str_replace('Per ', '', $row->berat))))
                        ) + 2;
    
                        if (($pdf->GetY() + $row_height) > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'])) {
                            $pdf->AddPage();
                            // Cetak ulang header di halaman baru
                            $pdf->SetFont('', 'B');
                            for ($i = 0; $i < $num_headers; ++$i) {
                                $pdf->MultiCell($cell_width[$i], 7, $header_kolom[$i], 1, 'C', 1, 0);
                            }
                            $pdf->Ln();
                            $pdf->SetFont('');
                        }
    
                        $pdf->MultiCell($cell_width[0], $row_height, $row->idsampah, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[1], $row_height, $row->jenis, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[2], $row_height, '', 1, 'C', $fill, 0);
                        if (!empty($row->gambar)) {
                            $pdf->Image(base_url('uploads/' . $row->gambar), $pdf->GetX() - $cell_width[2] + 5, $pdf->GetY() + ($row_height / 2) - 10, 20);
                        }
                        $pdf->MultiCell($cell_width[3], $row_height, $row->berat, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[4], $row_height, 'Rp ' . number_format($row->harga, 0, ',', '.') . '/' . strtolower(trim(str_replace('Per ', '', $row->berat))), 1, 'R', $fill, 1);
                        break;

                        case 'nasabah':
                            $row_height = max(
                                $pdf->getStringHeight($cell_width[0], $row->idnasabah),
                                $pdf->getStringHeight($cell_width[1], $row->username),
                                $pdf->getStringHeight($cell_width[2], $row->phone),
                                $pdf->getStringHeight($cell_width[3], $row->address),
                                $pdf->getStringHeight($cell_width[4], number_format($row->tabungan, 0, ',', '.')) // Ganti $row->tabungan menjadi $row->total_tabungan
                            ) + 2;
                            if (($pdf->GetY() + $row_height) > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'])) {
                                $pdf->AddPage();
                                $pdf->SetFont('', 'B');
                                for ($i = 0; $i < $num_headers; ++$i) {
                                    $pdf->MultiCell($cell_width[$i], 7, $header_kolom[$i], 1, 'C', 1, 0);
                                }
                                $pdf->Ln();
                                $pdf->SetFont('');
                            }
                            $pdf->MultiCell($cell_width[0], $row_height, $row->idnasabah, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[1], $row_height, $row->username, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[2], $row_height, $row->phone, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[3], $row_height, $row->address, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[4], $row_height, number_format($row->tabungan, 0, ',', '.'), 1, 'R', $fill, 1); // Ganti $row->tabungan menjadi $row->total_tabungan
                            break;

                        case 'penabungan':
                            $row_height = max(
                                $pdf->getStringHeight($cell_width[0], $row->idpenabungan),
                                $pdf->getStringHeight($cell_width[1], $row->idnasabah),
                                $pdf->getStringHeight($cell_width[2], $row->idsampah),
                                $pdf->getStringHeight($cell_width[3], date('d/m/Y', strtotime($row->tanggal))),
                                $pdf->getStringHeight($cell_width[4], $row->berat),
                                50,
                                $pdf->getStringHeight($cell_width[6], number_format($row->harga, 0, ',', '.')),
                                $pdf->getStringHeight($cell_width[7], $row->status)
                            ) + 2;
                            if (($pdf->GetY() + $row_height) > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'])) {
                                $pdf->AddPage();
                                $pdf->SetFont('', 'B');
                                for ($i = 0; $i < $num_headers; ++$i) {
                                    $pdf->MultiCell($cell_width[$i], 7, $header_kolom[$i], 1, 'C', 1, 0);
                                }
                                $pdf->Ln();
                                $pdf->SetFont('');
                            }
                            $pdf->MultiCell($cell_width[0], $row_height, $row->idpenabungan, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[1], $row_height, $row->idnasabah, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[2], $row_height, $row->idsampah, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[3], $row_height, date('d/m/Y', strtotime($row->tanggal)), 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[4], $row_height, $row->berat, 1, 'L', $fill, 0);
                            $pdf->MultiCell($cell_width[5], $row_height, '', 1, 'C', $fill, 0);
                            if (!empty($row->gambar)) {
                                $pdf->Image(base_url('uploads/gambar_penabungan/' . $row->gambar), $pdf->GetX() - $cell_width[5] + 5, $pdf->GetY() + ($row_height / 2) - 25, 20);
                            }
                            /*$pdf->MultiCell($cell_width[5], $row_height, '', 1, 'C', $fill, 0);
                            if (!empty($row->gambar)) {
                            $pdf->Image(base_url('uploads/gambar_penabungan/' . $row->gambar), $pdf->GetX() - $cell_width[5] + 5, $pdf->GetY() + ($row_height / 2) - 25, 20);
                            }*/
                            $pdf->MultiCell($cell_width[6], $row_height, number_format($row->harga, 0, ',', '.'), 1, 'R', $fill, 0);
                            $pdf->MultiCell($cell_width[7], $row_height, $row->status, 1, 'L', $fill, 1);
                            break;

                    case 'penarikan':
                        $row_height = max(
                            $pdf->getStringHeight($cell_width[0], $row->idpenarikan),
                            $pdf->getStringHeight($cell_width[1], $row->idnasabah),
                            $pdf->getStringHeight($cell_width[2], $row->nama_nasabah),
                            $pdf->getStringHeight($cell_width[3], $row->metode),
                            $pdf->getStringHeight($cell_width[4], $row->noRek),
                            $pdf->getStringHeight($cell_width[5], date('d/m/Y', strtotime($row->tanggal))),
                            $pdf->getStringHeight($cell_width[6], number_format($row->nominal, 0, ',', '.'))
                        ) + 2;
                        if (($pdf->GetY() + $row_height) > ($pdf->getPageHeight() - $pdf->getMargins()['bottom'])) {
                            $pdf->AddPage();
                            $pdf->SetFont('', 'B');
                            for ($i = 0; $i < $num_headers; ++$i) {
                                $pdf->MultiCell($cell_width[$i], 7, $header_kolom[$i], 1, 'C', 1, 0);
                            }
                            $pdf->Ln();
                            $pdf->SetFont('');
                        }
                        $pdf->MultiCell($cell_width[0], $row_height, $row->idpenarikan, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[1], $row_height, $row->idnasabah, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[2], $row_height, $row->nama_nasabah, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[3], $row_height, $row->metode, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[4], $row_height, $row->noRek, 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[5], $row_height, date('d/m/Y', strtotime($row->tanggal)), 1, 'L', $fill, 0);
                        $pdf->MultiCell($cell_width[6], $row_height, number_format($row->nominal, 0, ',', '.'), 1, 'R', $fill, 1);
                        break;
                }
                $fill = !$fill;
            }
        } else {
            $pdf->Cell(array_sum($cell_width), 10, 'Data tidak ditemukan.', 1, 0, 'C');
        }
    
        $pdf->Output('laporan_' . $jenis_laporan . '.pdf', 'I');
    }

    public function export_laporan_excel($jenis_laporan)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'laporan_' . $jenis_laporan . '.xlsx';
        
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $id_min = $this->input->get('id_min');
        $id_max = $this->input->get('id_max');
        $data = [];
        $header_kolom = [];
        $maxImageWidth = 100; // Lebar maksimal gambar (nilai awal, akan diubah di case 'sampah')

        // Set judul laporan
        $judulLaporan = '';
        switch ($jenis_laporan) {
            case 'nasabah':
                $judulLaporan = 'Laporan Data Nasabah';
                $this->load->model('m_nasabah');
                $total_tabungan_per_nasabah = $this->m_nasabah->getTotalTabunganTerverifikasiPerNasabah();
                $data = [];
                if (!empty($id_min) && !empty($id_max)) {
                    $all_nasabah = $this->m_nasabah->get_all_nasabah(); // Ambil semua data nasabah
                    foreach ($all_nasabah as $nasabah) {
                        $id_nasabah = $nasabah->idnasabah;
                        $numeric_id = str_replace('NS', '', $id_nasabah);

                        $is_within_range = false;
                        if (is_numeric($numeric_id) && $numeric_id >= $id_min && $numeric_id <= $id_max) {
                            $is_within_range = true;
                        } elseif ($id_nasabah >= 'NS' . str_pad($id_min, 3, '0', STR_PAD_LEFT) && $id_nasabah <= 'NS' . str_pad($id_max, 3, '0', STR_PAD_LEFT)) {
                            $is_within_range = true;
                        } elseif ($id_nasabah == $id_min || $id_nasabah == $id_max) {
                            $is_within_range = true;
                        }

                        if ($is_within_range) {
                        // Tambahkan total tabungan ke dalam objek nasabah (untuk ditampilkan di Excel)
                        $nasabah->total_tabungan = isset($total_tabungan_per_nasabah[$id_nasabah]) ? $total_tabungan_per_nasabah[$id_nasabah] : 0;
                        $data[] = $nasabah;
                        }
                    }
                } else {
                $all_nasabah = $this->m_nasabah->get_all_nasabah(); // Ambil semua data nasabah
                foreach ($all_nasabah as $nasabah) {
                $nasabah->total_tabungan = isset($total_tabungan_per_nasabah[$nasabah->idnasabah]) ? $total_tabungan_per_nasabah[$nasabah->idnasabah] : 0;
                $data[] = $nasabah;
            }
        }
            $header_kolom = ['ID Nasabah', 'Nama Nasabah', 'No Telp', 'Alamat', 'Tabungan'];
            break;

            case 'sampah':
                $judulLaporan = 'Laporan Data Sampah';
                $this->load->model('m_sampah');
                if (!empty($id_min) && !empty($id_max)) {
                    $id_min_formatted = 'S' . str_pad($id_min, 3, '0', STR_PAD_LEFT);
                    $id_max_formatted = 'S' . str_pad($id_max, 3, '0', STR_PAD_LEFT);
                    $data = $this->m_sampah->get_sampah_by_range($id_min_formatted, $id_max_formatted);
                } else {
                    $data = $this->m_sampah->get_all_sampah();
                }
                $header_kolom = ['ID Sampah', 'Jenis', 'Gambar', 'Berat', 'Harga'];
                $maxImageWidth = 60; // Set maxWidth khusus untuk laporan sampah
                break;

            case 'penabungan':
                $judulLaporan = 'Laporan Data Penabungan';
                $this->load->model('m_penabungan');
                if (!empty($start_date) && !empty($end_date)) {
                    $data = $this->m_penabungan->get_penabungan_by_date_and_status($start_date, $end_date);
                } else {
                    $data = $this->m_penabungan->get_penabungan_by_status('Terverifikasi');
                }
                $header_kolom = ['ID Penabungan', 'ID Nasabah', 'ID Sampah', 'Tanggal', 'Berat', 'Gambar', 'Harga', 'Status'];
                break;

            case 'penarikan':
                $judulLaporan = 'Laporan Data Penarikan';
                $this->load->model('m_penarikan');
                $start_date = $this->input->get('start_date');
                $end_date = $this->input->get('end_date');

                if (!empty($start_date) && !empty($end_date)) {
                    $data = $this->m_penarikan->get_penarikan_by_date_and_status($start_date, $end_date);
                } else {
                    $data = $this->m_penarikan->getAllPengajuanPenarikanWithNamaNasabahByStatus('Selesai');
                }
                    $header_kolom = ['ID Penarikan', 'ID Nasabah', 'Nama Nasabah', 'Metode', 'No Rek', 'Tanggal Pengajuan', 'Nominal'];
                    break;

                default:
                show_error('Jenis laporan tidak valid.');
                return;
            }

        // Set judul laporan
        $sheet->setCellValue('A1', $judulLaporan);
        // Gabungkan sel judul hanya jika ada kolom header
        if (!empty($header_kolom)) {
            $sheet->mergeCells('A1:' . chr(ord('A') + count($header_kolom) - 1) . '1');
            $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Styling untuk header
        $styleHeader = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'startColor' => ['argb' => 'FFA0A0A0'],
                'endColor' => ['argb' => 'FFD3D3D3'],
            ],
        ];

        // Styling untuk body tabel
    $styleBody = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'font' => [ // Tambahkan pengaturan font di sini
            'size' => 9, // Atur ukuran font menjadi 9 (nilai default biasanya 11)
        ],
    ];
        // Set judul kolom (header akan dimulai dari baris 2 setelah judul laporan)
        $kolom = 'A';
        $columnIndexGambar = array_search('Gambar', $header_kolom);
        $headerColumnIndex = 2;

        foreach ($header_kolom as $judul) {
            $sheet->setCellValue($kolom . $headerColumnIndex, $judul);
            $kolom++;
        }

        // Aplikasikan style header ke baris judul
        $lastColumnHeader = chr(ord('A') + count($header_kolom) - 1);
        if (!empty($header_kolom)) {
            $sheet->getStyle('A' . $headerColumnIndex . ':' . $lastColumnHeader . $headerColumnIndex)->applyFromArray($styleHeader);
            $sheet->getRowDimension($headerColumnIndex)->setRowHeight(25);
        }

        // Set data
        $baris = 3;
        if (!empty($data)) {
            foreach ($data as $row) {
                $kolom = 'A';
                switch ($jenis_laporan) {
            case 'nasabah':
                $sheet->setCellValue($kolom++ . $baris, $row->idnasabah)
                    ->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue($kolom++ . $baris, $row->username);
    
                // --- TAMBAHKAN BARIS INI UNTUK FORMAT SEL SEBAGAI TEKS ---
                $sheet->getCell($kolom . $baris)->setValueExplicit($row->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $kolom++; // Pindahkan ke kolom berikutnya setelah mengatur nilai dan tipe data
                // --- AKHIR TAMBAH ---

                $sheet->setCellValue($kolom++ . $baris, $row->address);

                // Format nilai tabungan sebagai angka dengan pemisah ribuan dan desimal yang sesuai
                $sheet->setCellValue($kolom++ . $baris, number_format($row->tabungan, 2, '.', ','))
                    ->getStyle(chr(ord('A') + count($header_kolom) - 1) . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                break;
                
              case 'sampah':
                $sheet->setCellValue($kolom++ . $baris, $row->idsampah)
                    ->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Batasi panjang karakter untuk kolom Jenis
                $jenisSampah = $row->jenis;
                $maxLengthJenis = 40; // Anda bisa menyesuaikan angka ini
                if (strlen($jenisSampah) > $maxLengthJenis) {
                    $jenisSampah = substr($jenisSampah, 0, $maxLengthJenis) . '...';
                }
                $sheet->setCellValue($kolom++ . $baris, $jenisSampah);

                $gambarColumnLetter = chr(ord('A') + $columnIndexGambar); // Kolom 'Gambar'

                // --- PENTING: Atur lebar kolom 'Gambar' secara eksplisit di sini ---
                // Sesuaikan nilai ini untuk mengontrol lebar kolom dan secara tidak langsung ukuran gambar
                $sheet->getColumnDimension($gambarColumnLetter)->setWidth(12); // Mengurangi lebar kolom menjadi 12 (unit karakter Excel)
                                                                 // Sesuaikan jika terlalu besar/kecil

                if (!empty($row->gambar) && file_exists(FCPATH . 'uploads/' . $row->gambar)) {
                    $imagePath = FCPATH . 'uploads/' . $row->gambar;
                    try {
                        list($width, $height) = getimagesize($imagePath);

                        $drawing = new Drawing();
                        $drawing->setName('Gambar Sampah');
                        $drawing->setDescription('Gambar Sampah');
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates($gambarColumnLetter . $baris); // Koordinat di kolom 'Gambar'

                        // --- Logika Penskalaan Gambar agar ukuran sedang ---
                        // Dapatkan lebar kolom dalam unit karakter Excel yang sudah kita set
                        $columnWidthExcelUnits = $sheet->getColumnDimension($gambarColumnLetter)->getWidth();
                        // Estimasi konversi unit Excel ke piksel. Nilai 7.5 adalah rata-rata yang baik.
                        $pixelsPerUnit = 7.5; 
                        $columnWidthPixels = $columnWidthExcelUnits * $pixelsPerUnit; 

                        // Tentukan lebar maksimal gambar yang diinginkan (sedikit lebih kecil dari lebar kolom untuk padding)
                        $maxWidthForImage = $columnWidthPixels - 10; // Kurangi 10 piksel untuk padding kiri dan kanan
            
                        // Juga tentukan tinggi maksimal gambar untuk menjaga agar tidak terlalu tinggi
                        $maxHeightForImage = 60; // Tinggi maksimal gambar dalam piksel. Sesuaikan nilai ini.

                        // Skala gambar berdasarkan maxWidthForImage DAN maxHeightForImage, sambil menjaga rasio aspek
                        $ratio = min($maxWidthForImage / $width, $maxHeightForImage / $height);
                        $drawing->setWidth($width * $ratio);
                        $drawing->setHeight($height * $ratio);
            
                        $drawing->setWorksheet($sheet);

                        // --- Perhitungan Offset X (Horizontal Centering) ---
                        $imageActualWidth = $drawing->getWidth();
                        $offsetX = ($columnWidthPixels - $imageActualWidth) / 2;
                        $drawing->setOffsetX(round(max(0, $offsetX)));

                        // --- Perhitungan Tinggi Baris dan Offset Y (Vertical Centering) ---
                        // Konversi tinggi gambar dari piksel ke point (1 point = 1.333 piksel pada 96 DPI, jadi pixel * 0.75 = point)
                        $desiredRowHeightPoints = ($drawing->getHeight() * 0.75) + 5; // +5 untuk padding vertikal
                        $sheet->getRowDimension($baris)->setRowHeight(max(45, $desiredRowHeightPoints)); // Minimum 45 point atau sesuai tinggi gambar

                        // Dapatkan tinggi baris aktual dalam point (setelah setRowHeight)
                        $actualRowHeightPoints = $sheet->getRowDimension($baris)->getRowHeight();
                        // Konversi tinggi baris aktual dari point ke piksel untuk perhitungan offsetY
                        $actualRowSpreadsheetDrawingShared = PhpOffice\PhpSpreadsheet\Shared\Drawing::pointsToPixels($actualRowHeightPoints);

                        $imageActualHeight = $drawing->getHeight(); // Tinggi gambar setelah diskalakan
                        $offsetY = ($actualRowSpreadsheetDrawingShared - $imageActualHeight) / 2;
                        $drawing->setOffsetY(round(max(0, $offsetY)));

                    } catch (\Exception $e) {
                        log_message('error', 'Gagal membaca gambar sampah: ' . $imagePath . ' - ' . $e->getMessage());
                        $sheet->setCellValue($gambarColumnLetter . $baris, 'Gagal menampilkan gambar');
                    }
                }
                $kolom++; // Pastikan kolom bergerak ke kolom berikutnya setelah kolom gambar
                $sheet->setCellValue($kolom++ . $baris, $row->berat)
                    ->getStyle(chr(ord('A') + count($header_kolom) - 2) . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue($kolom++ . $baris, 'Rp ' . number_format($row->harga, 0, ',', '.') . '/' . strtolower(trim(str_replace('Per ', '', $row->berat))))
                    ->getStyle(chr(ord('A') + count($header_kolom) - 1) . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                break;

                /*case 'penabungan':
                        $sheet->setCellValue($kolom++ . $baris, $row->idpenabungan)
                            ->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, $row->idnasabah)
                            ->getStyle('B' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, $row->idsampah)
                            ->getStyle('C' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, date('d/m/Y', strtotime($row->tanggal)))
                            ->getStyle('D' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, $row->berat);

                        $gambar = $row->gambar; // Ambil nama file gambar
                        $gambarColumnLetter = chr(ord('A') + $columnIndexGambar); // Kolom 'Gambar'

                        if (!empty($gambar) && file_exists(FCPATH . 'uploads/gambar_penabungan/' . $gambar)) {
                            $imagePath = FCPATH . 'uploads/gambar_penabungan/' . $gambar;
                            try {
                                list($width, $height) = getimagesize($imagePath);
                                $drawing = new Drawing();
                                $drawing->setName('Gambar Penabungan');
                                $drawing->setDescription('Gambar Penabungan');
                                $drawing->setPath($imagePath);
                                $drawing->setCoordinates($gambarColumnLetter . $baris);

                                $maxWidth = 90; // Lebar maksimal gambar (sesuaikan jika perlu)
                                if ($width > $maxWidth) {
                                    $scale = $maxWidth / $width;
                                    $drawing->setWidth($maxWidth);
                                    $drawing->setHeight($height * $scale);
                                } else {
                                    $drawing->setWidth($width);
                                    $drawing->setHeight($height);
                                }
                                $drawing->setWorksheet($sheet);

                                // Hitung posisi tengah
                                $columnWidthInPixels = $sheet->getColumnDimension($gambarColumnLetter)->getWidth() * 7; // Perkiraan lebar kolom dalam pixel
                                $offsetX = ($columnWidthInPixels - $drawing->getWidth()) / 2;
                                $offsetY = 2; // Sedikit offset dari atas

                                $drawing->setOffsetX(round(max(0, $offsetX)));
                                $drawing->setOffsetY(round(max(0, $offsetY)));

                                // Hitung tinggi baris berdasarkan tinggi gambar
                                $rowHeight = ($drawing->getHeight() * 0.75) + 5;
                                $sheet->getRowDimension($baris)->setRowHeight(max(45, $rowHeight));

                                $kolom++;
                            } catch (\Exception $e) {
                                // Tangani jika ada masalah membaca file gambar
                                log_message('error', 'Gagal membaca gambar penabungan: ' . $imagePath . ' - ' . $e->getMessage());
                                $sheet->setCellValue($gambarColumnLetter . $baris, 'Gagal menampilkan gambar');
                                $kolom++;
                            }
                        } else {
                            $kolom++;
                        }

                        $sheet->setCellValue($kolom++ . $baris, number_format($row->harga, 0, ',', '.'))
                            ->getStyle(chr(ord('A') + count($header_kolom) - 2) . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setCellValue($kolom++ . $baris, $row->status);
                        break; */

                case 'penabungan':
                    $sheet->setCellValue($kolom++ . $baris, $row->idpenabungan)
                        ->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue($kolom++ . $baris, $row->idnasabah)
                        ->getStyle('B' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue($kolom++ . $baris, $row->idsampah)
                        ->getStyle('C' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue($kolom++ . $baris, date('d/m/Y', strtotime($row->tanggal)))
                        ->getStyle('D' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->setCellValue($kolom++ . $baris, $row->berat);
                    $gambar = $row->gambar; // Ambil nama file gambar
                    $gambarColumnLetter = chr(ord('A') + $columnIndexGambar); // Kolom 'Gambar'

                    // --- PENTING: Atur lebar kolom 'Gambar' secara eksplisit di sini ---
                    // Sesuaikan nilai ini untuk mengontrol lebar kolom dan secara tidak langsung ukuran gambar
                    $sheet->getColumnDimension($gambarColumnLetter)->setWidth(12); // Mengurangi lebar kolom menjadi 12 (unit karakter Excel)

                    if (!empty($gambar) && file_exists(FCPATH . 'uploads/gambar_penabungan/' . $gambar)) {
                    $imagePath = FCPATH . 'uploads/gambar_penabungan/' . $gambar;
                    try {
                        list($width, $height) = getimagesize($imagePath);
                        $drawing = new Drawing();
                        $drawing->setName('Gambar Penabungan');
                        $drawing->setDescription('Gambar Penabungan');
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates($gambarColumnLetter . $baris); // Koordinat di kolom 'Gambar'

                        // --- Logika Penskalaan Gambar agar ukuran sedang ---
                        // Dapatkan lebar kolom dalam unit karakter Excel yang sudah kita set
                        $columnWidthExcelUnits = $sheet->getColumnDimension($gambarColumnLetter)->getWidth();
                        // Estimasi konversi unit Excel ke piksel. Nilai 7.5 adalah rata-rata yang baik.
                        $pixelsPerUnit = 7.5; 
                        $columnWidthPixels = $columnWidthExcelUnits * $pixelsPerUnit; 

                        // Tentukan lebar maksimal gambar yang diinginkan (sedikit lebih kecil dari lebar kolom untuk padding)
                        // Mengurangi padding menjadi 5 piksel di setiap sisi (total 10 piksel)
                        $maxWidthForImage = $columnWidthPixels - 10; 
            
                        // Juga tentukan tinggi maksimal gambar untuk menjaga agar tidak terlalu tinggi
                        $maxHeightForImage = 60; // Tinggi maksimal gambar dalam piksel. Sesuaikan nilai ini.

                        // Skala gambar berdasarkan maxWidthForImage DAN maxHeightForImage, sambil menjaga rasio aspek
                        $ratio = min($maxWidthForImage / $width, $maxHeightForImage / $height);
                        $drawing->setWidth($width * $ratio);
                        $drawing->setHeight($height * $ratio);
            
                        $drawing->setWorksheet($sheet);

                        // --- Perhitungan Offset X (Horizontal Centering) ---
                        $imageActualWidth = $drawing->getWidth();
                        $offsetX = ($columnWidthPixels - $imageActualWidth) / 2;
                        $drawing->setOffsetX(round(max(0, $offsetX)));

                        // --- Perhitungan Tinggi Baris dan Offset Y (Vertical Centering) ---
                        // Konversi tinggi gambar dari piksel ke point (1 point = 1.333 piksel pada 96 DPI, jadi pixel * 0.75 = point)
                        $desiredRowHeightPoints = ($drawing->getHeight() * 0.75) + 5; // +5 untuk padding vertikal
                        $sheet->getRowDimension($baris)->setRowHeight(max(45, $desiredRowHeightPoints)); // Minimum 45 point atau sesuai tinggi gambar

                        // Dapatkan tinggi baris aktual dalam point (setelah setRowHeight)
                        $actualRowHeightPoints = $sheet->getRowDimension($baris)->getRowHeight();
                        // Konversi tinggi baris aktual dari point ke piksel untuk perhitungan offsetY
                        $actualRowHeightPixels = PhpOffice\PhpSpreadsheet\Shared\Drawing::pointsToPixels($actualRowHeightPoints);

                        $imageActualHeight = $drawing->getHeight(); // Tinggi gambar setelah diskalakan
                        $offsetY = ($actualRowHeightPixels - $imageActualHeight) / 2;
                        $drawing->setOffsetY(round(max(0, $offsetY)));

                    } catch (\Exception $e) {
                        log_message('error', 'Gagal membaca gambar penabungan: ' . $imagePath . ' - ' . $e->getMessage());
                        $sheet->setCellValue($gambarColumnLetter . $baris, 'Gagal menampilkan gambar');
                    }
                }
                $kolom++; // Pastikan kolom bergerak ke kolom berikutnya setelah kolom gambar
                $sheet->setCellValue($kolom++ . $baris, number_format($row->harga, 2, ',', '.'))
                    ->getStyle(chr(ord('A') + count($header_kolom) - 1) . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue($kolom++ . $baris, $row->status);
                break;

                    case 'penarikan':
                        $sheet->setCellValue($kolom++ . $baris, $row->idpenarikan)
                            ->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, $row->idnasabah)
                            ->getStyle('B' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, $row->nama_nasabah);
                        $sheet->setCellValue($kolom++ . $baris, $row->metode);
                        $sheet->setCellValue($kolom++ . $baris, $row->noRek);
                        $sheet->setCellValue($kolom++ . $baris, date('d/m/Y', strtotime($row->tanggal)))
                            ->getStyle('F' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue($kolom++ . $baris, number_format($row->nominal, 2, ',', '.'))
                            ->getStyle('G' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        break;
                }
                $baris++;
            }

            // Aplikasikan style body ke seluruh data (dimulai dari baris setelah judul dan header)
            $lastColumn = chr(ord('A') + count($header_kolom) - 1);
            $lastRow = $baris - 1;
            if ($lastRow >= 3 && !empty($header_kolom)) {
                $sheet->getStyle('A3:' . $lastColumn . $lastRow)->applyFromArray($styleBody);
            }

            // Atur auto-size untuk semua kolom setelah data dimasukkan (kecuali kolom gambar yang sudah diatur manual)
           /*for ($i = 'A'; $i <= $lastColumn; $i++) {
                if ($i != chr(ord('A') + $columnIndexGambar)) {
                    $sheet->getColumnDimension($i)->setAutoSize(true);
                }
            } */
        }

                // Pengaturan lebar kolom manual (sesuaikan nilai lebar sesuai kebutuhan)
                if ($jenis_laporan === 'nasabah') {
                    $sheet->getColumnDimension('A')->setWidth(10); // ID Nasabah
                    $sheet->getColumnDimension('B')->setWidth(25); // Nama Nasabah
                    $sheet->getColumnDimension('C')->setWidth(15); // No Telp
                    $sheet->getColumnDimension('D')->setWidth(30); // Alamat
                    $sheet->getColumnDimension('E')->setWidth(10); // Tabungan
                } elseif ($jenis_laporan === 'sampah') {
                    $sheet->getColumnDimension('A')->setWidth(10); // ID Sampah
                    $sheet->getColumnDimension('B')->setWidth(30); // Jenis
                    if ($columnIndexGambar !== false) {
                        $sheet->getColumnDimension(chr(ord('A') + $columnIndexGambar))->setWidth(15); // Gambar
                    }
                    $sheet->getColumnDimension(chr(ord('A') + count($header_kolom) - 2))->setWidth(10); // Berat
                    $sheet->getColumnDimension(chr(ord('A') + count($header_kolom) - 1))->setWidth(15); // Harga
                } elseif ($jenis_laporan === 'penabungan') {
                    $sheet->getColumnDimension('A')->setWidth(15);   // ID Penabungan
                    $sheet->getColumnDimension('B')->setWidth(10);   // ID Nasabah
                    $sheet->getColumnDimension('C')->setWidth(10);   // ID Sampah
                    $sheet->getColumnDimension('D')->setWidth(10);   // Tanggal
                    $sheet->getColumnDimension('E')->setWidth(10);   // Berat
                    if ($columnIndexGambar !== false) {
                        $sheet->getColumnDimension(chr(ord('A') + $columnIndexGambar))->setWidth(15); // Gambar
                    }
                    $sheet->getColumnDimension('G')->setWidth(15);   // Harga
                    $sheet->getColumnDimension('H')->setWidth(15);   // Status
                } elseif ($jenis_laporan === 'penarikan') {
                    $sheet->getColumnDimension('A')->setWidth(13); // ID Penarikan
                    $sheet->getColumnDimension('B')->setWidth(10); // ID Nasabah
                    $sheet->getColumnDimension('C')->setWidth(30); // Nama Nasabah
                    $sheet->getColumnDimension('D')->setWidth(12); // Metode
                    $sheet->getColumnDimension('E')->setWidth(20); // No Rek
                    $sheet->getColumnDimension('F')->setWidth(20); // Tanggal Pengajuan
                    $sheet->getColumnDimension('G')->setWidth(15); // Nominal
                }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}