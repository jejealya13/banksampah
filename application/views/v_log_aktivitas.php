<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 970px;">
        <div class="card-header bg-primary text-white p-3 d-flex align-items-center">
            <i class="fa fa-history fa-lg mr-2"></i> <h4 class="mb-0 d-inline">Riwayat Aktivitas</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jam</th>
                            <th class="text-center">Aktivitas</th>
                            <th class="text-center">Deskripsi</th>
                            <!-- <th class="text-center">IP Address</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($log_aktivitas)) {
                            foreach ($log_aktivitas as $log):
                                $tanggal = 'N/A';
                                $jam = 'N/A';
                                if (isset($log->timestamp)) {
                                    $datetime = new DateTime($log->timestamp);
                                    $tanggal = $datetime->format('d F Y');
                                    $jam = $datetime->format('H:i:s');
                                }

                                // Gabungkan Aktivitas dengan Email
                                $aktivitas_dengan_email = $log->aktivitas;
                                if (isset($log->email) && !empty($log->email) && $log->email != 'Tidak Diketahui') {
                                    $aktivitas_dengan_email .= ' oleh ' . $log->email;
                                }
                        ?>
                        <tr>
                            <td class="text-center"><?= $tanggal ?></td>
                            <td class="text-center"><?= $jam ?></td>
                            <td class="text-center"><?= $aktivitas_dengan_email ?></td> <td class="text-center">
                            <?= $log->deskripsi ?? 'Tidak ada deskripsi' ?></td>
                            <!-- <td class="text-center"><?= $log->ip_address ?? 'Tidak Diketahui' ?></td> -->
                        </tr>
                        <?php
                            endforeach;
                        } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada riwayat aktivitas yang tersedia.</td> </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Gaya CSS Anda yang sudah ada */
    .card {
        border-radius: 8px; /* Membuat sudut card melengkung */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Menambahkan bayangan pada card */
    }
    .card-header {
        background-color: #051cf1 !important; /* Mengubah warna biru */
        display: flex; /* Mengaktifkan Flexbox */
        align-items: center; /* Mengatur item vertikal ke tengah */
        justify-content: center; /* Mengatur item horizontal ke tengah */
        padding: 15px; /* Tambahkan atau sesuaikan padding jika perlu */
        line-height: 1.75rem; /* Sesuaikan dengan perkiraan tinggi ikon/teks */
    }
    .card-header i {
        font-size: 1.75rem; /* Memperbesar ukuran ikon */
        margin-right: 10px; /* Memberikan jarak antara ikon dan teks */
        vertical-align: middle; /* Mencoba mensejajarkan vertikal ikon */
        position: static; /* Hapus properti position jika sebelumnya ditambahkan */
        top: auto; /* Hapus properti top jika sebelumnya ditambahkan */
        margin-top: auto; /* Hapus properti margin-top jika sebelumnya ditambahkan */
    }
    .card-header h4 {
        font-size: 1.75rem; /* Memperbesar ukuran teks */
        margin-bottom: 0 !important; /* Memastikan tidak ada margin bawah yang mengganggu */
        line-height: 1; /* Memastikan line-height tidak menyebabkan pergeseran vertikal */
        font-weight: bold; /* Membuat teks menjadi tebal */
        vertical-align: middle; /* Tambahkan ini pada h4 */
    }
    .table {
        background-color: #ffffff; /* Warna latar belakang tabel */
        border-radius: 0; /* Menghapus border radius tabel karena sudah ada di card */
        border-collapse: collapse; /* Menggabungkan border sel */
    }
    .table th, .table td {
        border: 1px solid #dee2e6; /* Menambahkan garis pada sel */
        vertical-align: middle; /* Menyelaraskan teks ke tengah */
        padding: 12px; /* Mengurangi sedikit padding pada sel tabel */
    }
    .thead-light th {
        background-color: #f8f9fa; /* Warna latar belakang header tabel */
        color: #343a40; /* Warna teks header tabel */
        border-bottom: 2px solid #dee2e6; /* Garis bawah header yang lebih tebal */
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05); /* Warna latar belakang baris ganjil yang lebih lembut */
    }
    .table-responsive {
        margin-top: 15px; /* Memberikan sedikit jarak antara header card dan tabel */
    }
</style>