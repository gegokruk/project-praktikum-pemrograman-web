<?php
session_start();

// Cek apakah user sudah login atau belum
$sudah_login = false;
$halaman_dashboard = "login.php";

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    $sudah_login = true;
    $halaman_dashboard = $_SESSION["role"] . ".php";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SiLembur - Sistem Manajemen Lembur</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Buatan Sendiri -->
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">SiLembur</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="#tentang">Tentang Kami</a>
                <a class="nav-link" href="#fitur">Keunggulan</a>
                <a class="nav-link" href="#alur">Cara Kerja</a>
            </div>
            <div>
                <?php if ($sudah_login): ?>
                    <a href="<?= $halaman_dashboard ?>" class="btn btn-primary btn-sm">Dashboard</a>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Bagian Pembuka (Banner Utama) -->
    <header class="banner-utama">
        <div class="container">
            <h1>Sistem Manajemen Lembur & Fleksibilitas Jam Kerja</h1>
            <p class="mt-3 mb-4">
                Kelola jadwal dan kuota lembur karyawan secara terstruktur, transparan, dan fleksibel dalam satu portal web.
            </p>
            <?php if ($sudah_login): ?>
                <a href="<?= $halaman_dashboard ?>" class="btn btn-primary">Buka Dashboard Saya</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light me-2">Masuk Sekarang</a>
                <a href="register.php" class="btn btn-outline-light">Daftar Akun Karyawan</a>
            <?php endif; ?>
        </div>
    </header>

    <section id="tentang" class="bagian-putih">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-md-6">
                    <h6 class="text-primary fw-bold">TENTANG KAMI</h6>
                    <h3 class="mb-3">PT Nusantara Digital Solusi</h3>
                    <p>
                        Kami adalah perusahaan layanan teknologi yang gokil keren mengutamakan keseimbangan antara target kerja 
                        dan kenyamanan waktu karyawan
                    </p>
                    <p>
                        Melalui aplikasi <strong>SiLembur</strong>, pembagian jam lembur diatur menggunakan sistem slot kuota 
                        agar tidak terjadi penumpukan karyawan di hari yang sama
                    </p>
                    <ul>
                        <li><strong>Jam Reguler:</strong> Senin - Jumat (08.00 - 17.00 WIB)</li>
                        <li><strong>Sesi Lembur:</strong> Sesuai jadwal slot dari Manager</li>
                        <li><strong>Lokasi:</strong> Condongcatur, Sleman, Yogyakarta</li>
                    </ul>
                </div>
                <div class="col-md-6 text-center">
                    <img src="assets/img/kantor.jpg" alt="Suasana Kantor" class="foto-kantor">
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Keunggulan Sistem -->
    <section id="fitur" class="bagian-abu">
        <div class="container">
            <h3 class="text-center mb-4">Keunggulan Sistem</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card kartu-fitur h-100">
                        <div class="card-body">
                            <h5>1. Kuota Slot Jelas</h5>
                            <p class="text-muted mb-0">
                                Setiap jadwal lembur memiliki batas maksimal karyawan lengkap dengan status warna hijau, kuning, dan merah.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card kartu-fitur h-100">
                        <div class="card-body">
                            <h5>2. Pilih & Batal Mandiri</h5>
                            <p class="text-muted mb-0">
                                Karyawan dapat memilih hari lembur yang masih tersedia atau membatalkannya jika berhalangan hadir.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card kartu-fitur h-100">
                        <div class="card-body">
                            <h5>3. Mudah Dikelola Manager</h5>
                            <p class="text-muted mb-0">
                                Manager bisa menambah jadwal baru, mengubah kuota, maupun memindahkan karyawan ke slot lain.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Cara Kerja -->
    <section id="alur" class="bagian-putih">
        <div class="container text-center">
            <h3 class="mb-4">Cara Menggunakan</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="lingkaran-angka">1</div>
                    <h5>Login atau Daftar</h5>
                    <p class="text-muted">Masuk menggunakan akun yang sudah ada atau daftar akun karyawan baru.</p>
                </div>
                <div class="col-md-4">
                    <div class="lingkaran-angka">2</div>
                    <h5>Pilih Jadwal Lembur</h5>
                    <p class="text-muted">Lihat daftar slot lembur yang masih buka dan klik tombol Pilih.</p>
                </div>
                <div class="col-md-4">
                    <div class="lingkaran-angka">3</div>
                    <h5>Cek Jadwal Saya</h5>
                    <p class="text-muted">Pantau jadwal lembur yang sudah diambil pada halaman dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-bawah">
        <div class="container">
            <p class="mb-0">&copy; 2026 SiLembur - Praktikum Pemrograman Web Kelompok 2</p>
        </div>
    </footer>

</body>
</html>