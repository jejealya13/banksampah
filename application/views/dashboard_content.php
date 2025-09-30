<div class="row">

    <!-- Card for Nasabah -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple" style="border-radius: 15px;">
            <div class="inner">
                <h3 style="color: white;"><?= $total_nasabah; ?></h3>
                <p style="color: white;">Nasabah</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <a href="<?= base_url()?>nasabah" class="small-box-footer" style="color: white;">Info Selengkapnya <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Card for Sampah -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-darkblue" style="border-radius: 15px;">
            <div class="inner">
                <h3 style="color: white;"><?= $total_sampah; ?></h3>
                <p style="color: white;">Sampah</p>
            </div>
            <div class="icon">
                <i class="fa fa-leaf"></i>
            </div>
            <a href="<?= base_url()?>sampah" class="small-box-footer" style="color: white;">Info Selengkapnya <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Card for Penabungan -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-lightbrown" style="border-radius: 15px;">
            <div class="inner">
                <h3 style="color: white;"><?= isset($transaksi_penabungan) ? $transaksi_penabungan : 0; ?></h3>
                <p style="color: white;">Transaksi Penabungan</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <a href="<?= base_url()?>penabungan" class="small-box-footer" style="color: white;">Info Selengkapnya <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

     <!-- Card for Penarikan -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-darkgreen" style="border-radius: 15px;">
            <div class="inner">
                <h3 style="color: white;"><?= isset($transaksi_penarikan) ? $transaksi_penarikan : 0; ?></h3>
                <p style="color: white;">Transaksi Penarikan</p>
            </div>
            <div class="icon">
                <i class="fa fa-exchange"></i>
            </div>
            <a href="<?= base_url()?>penarikan" class="small-box-footer" style="color: white;">Info Selengkapnya <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

<!-- Card for Laporan -->
<div class="col-lg-3 col-xs-6">
    <div class="small-box bg-brown" style="border-radius: 15px;">
        <div class="inner">
            <h3 style="color: white;"><?= isset($total_laporan) ? $total_laporan : 0; ?></h3>
            <p style="color: white;">Laporan</p>
        </div>
        <div class="icon">
            <i class="fa fa-file"></i>
        </div>
        <a href="<?= base_url()?>laporan" class="small-box-footer" style="color: white;">Info Selengkapnya <i class="fa fa-arrow-circle-right"></i></a>
    </div>
</div>

</div>

<style>
    .bg-purple {
        background-color: #6a0dad !important; /* Ungu */
    }
    .bg-darkblue {
        background-color: #003366 !important; /* Biru tua */
    }
    .bg-lightbrown {
        background-color: #d2b48c !important; /* Coklat muda */
    }
    .bg-darkgreen {
        background-color: #005f00 !important; /* Hijau tua */
    }
    .bg-brown {
        background-color: #8B4513 !important; /* Coklat tua */
    }
</style>

<!-- chart dashboard -->

<!-- chart dashboard -->
<style>
    .chart-container {
        position: relative;
        width: 100%; /* Menggunakan 100% lebar kolom */
        height: 300px; /* Tinggi yang sama untuk kedua chart */
    }
</style>

<div class="row">
    <div class="col-lg-6">
        <div class="box" style="border-radius: 15px;">
            <div class="box-header with-border">
                <h3 class="box-title">Jenis Sampah Terbanyak Ditabung</h3>
            </div>
            <div class="box-body">
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="box" style="border-radius: 15px;">
            <div class="box-header with-border">
                <h3 class="box-title">Nasabah Terdaftar per Tanggal</h3>
            </div>
            <div class="box-body">
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // PIE CHART
        const pieChartCanvas = document.getElementById('pieChart').getContext('2d');
        const pieData = {
            labels: [
                <?php if (!empty($jenis_sampah)): ?>
                    <?php foreach ($jenis_sampah as $js): ?>
                        '<?= $js['jenis_sampah']; ?>',
                    <?php endforeach; ?>
                <?php else: ?>
                    'Tidak Ada Data',
                <?php endif; ?>
            ],
            datasets: [{
                data: [
                    <?php if (!empty($jenis_sampah)): ?>
                        <?php foreach ($jenis_sampah as $js): ?>
                            <?= $js['jumlah']; ?>,
                        <?php endforeach; ?>
                    <?php else: ?>
                        0,
                    <?php endif; ?>
                ],
                backgroundColor: [
                    <?php if (!empty($jenis_sampah)): ?>
                        <?php foreach ($jenis_sampah as $js): ?>
                            generateColor(),
                        <?php endforeach; ?>
                    <?php else: ?>
                        '#d2d6de',
                    <?php endif; ?>
                ],
            }]
        };

        const pieOptions = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                }
            }
        };

        new Chart(pieChartCanvas, {
            type: 'pie',
            data: pieData,
            options: pieOptions
        });

         // LINE CHART
        const barChartCanvas = document.getElementById('barChart').getContext('2d');
         const barData = {
            labels: [
                <?php if (!empty($nasabah_per_tanggal)): ?>
                    <?php foreach ($nasabah_per_tanggal as $npt): ?>
                        '<?= $npt['tanggal']; ?>',
                    <?php endforeach; ?>
                <?php else: ?>
                    'Tidak Ada Data',
                <?php endif; ?>
            ],
            datasets: [{
                label: 'Jumlah Nasabah',
                data: [
                    <?php if (!empty($nasabah_per_tanggal)): ?>
                        <?php foreach ($nasabah_per_tanggal as $npt): ?>
                            <?= $npt['jumlah']; ?>,
                        <?php endforeach; ?>
                    <?php else: ?>
                        0,
                    <?php endif; ?>
                ],
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)', // Tambahkan warna garis
                fill: false, // Penting agar grafik garis tidak diisi
                tension: 0.1 // Untuk membuat garis sedikit melengkung (opsional)
            }]
        };

        const barOptions = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        new Chart(barChartCanvas, {
            type: 'line', // Diubah dari 'bar' menjadi 'line'
            data: barData,
            options: barOptions
         });
    });

    function generateColor() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16);
    }
</script>