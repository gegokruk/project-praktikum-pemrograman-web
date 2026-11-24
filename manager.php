<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "manager") {
    header("Location: login.php");
    exit;
}

// mengambil semua data slot urut berdasarkan tanggal dan jam mulai
$slots = mysqli_query($connection, "SELECT * FROM slot_lembur ORDER BY tanggal, jam_mulai");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Manager - SiLembur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Sistem Lembur</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link active" href="manager.php">Kelola Slot Lembur</a>
            </div>
            <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION["username"]) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION["success"])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Form untuk membuat slot lembur -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Buat Slot Lembur</h5>
                        <form method="POST" action="proses_manager.php">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kuota Karyawan</label>
                                <input type="number" name="kuota" class="form-control" min="1" value="3" required>
                            </div>
                            <button type="submit" name="tambah_slot" class="btn btn-primary w-100">
                                Simpan Slot
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- menampilkan daftar slot lembur -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Daftar Slot Lembur</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Kuota</th>
                                        <th>Status</th>
                                        <th>Pemilih</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($slots)): ?>
                                        <?php
                                        $id_slot = $row["id_slot"];

                                        // Menghitung karyawan yang memilih slot
                                        $q_hitung = mysqli_query($connection, "SELECT COUNT(*) AS jumlah FROM pilihan_lembur WHERE id_slot = $id_slot AND status = 'aktif'");
                                        $data_hitung = mysqli_fetch_assoc($q_hitung);
                                        $terisi = $data_hitung["jumlah"];

                                        $kondisi = status($terisi, $row["kuota"]);
                                        if ($terisi > $row["kuota"]) {
                                            $warna_baris = "table-danger";
                                            $warna_badge = "bg-danger";
                                            $teks_status = "Kelebihan Kuota";
                                        } elseif ($row["status"] == "tutup") {
                                            $warna_baris = "table-secondary";
                                            $warna_badge = "bg-secondary";
                                            $teks_status = "Ditutup";
                                        } elseif ($kondisi == "penuh") {
                                            $warna_baris = "table-danger";
                                            $warna_badge = "bg-danger";
                                            $teks_status = "Penuh";
                                        } elseif ($kondisi == "hampir penuh") {
                                            $warna_baris = "table-warning";
                                            $warna_badge = "bg-warning text-dark";
                                            $teks_status = "Hampir penuh";
                                        } else {
                                            $warna_baris = "table-success";
                                            $warna_badge = "bg-success";
                                            $teks_status = "Tersedia";
                                        }
                                        ?>
                                        <tr class="<?= $warna_baris ?>">
                                            <td><?= $no++ ?></td>
                                            <td><?= $row["tanggal"] ?></td>
                                            <td><?= substr($row["jam_mulai"], 0, 5) ?> - <?= substr($row["jam_selesai"], 0, 5) ?></td>
                                            <td><?= $terisi ?> / <?= $row["kuota"] ?></td>
                                            <td><span class="badge <?= $warna_badge ?>"><?= $teks_status ?></span></td>
                                            <td>
                                                <?php
                                                // Ambil nama karyawan yang aktif di slot ini
                                                $q_pemilih = mysqli_query($connection, "SELECT users.nama FROM pilihan_lembur, users WHERE pilihan_lembur.id_user = users.id_user AND pilihan_lembur.id_slot = $id_slot AND pilihan_lembur.status = 'aktif'");

                                                if (mysqli_num_rows($q_pemilih) > 0) {
                                                    while ($p = mysqli_fetch_assoc($q_pemilih)) {
                                                        echo "- " . htmlspecialchars($p["nama"]) . "<br>";
                                                    }
                                                } else {
                                                    echo "Belum ada";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="edit_slot.php?id=<?= $row["id_slot"] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
                                                <form method="POST" action="proses_manager.php" onsubmit="return confirm('Hapus slot lembur ini?')">
                                                    <input type="hidden" name="id_slot" value="<?= $row["id_slot"] ?>">
                                                    <button type="submit" name="hapus_slot" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if ($no == 1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada slot lembur yang dibuat</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>