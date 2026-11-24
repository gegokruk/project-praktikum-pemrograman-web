<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "manager") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: manager.php");
    exit;
}
$id_slot = (int) $_GET["id"];

$q_slot = mysqli_query($connection, "SELECT * FROM slot_lembur WHERE id_slot = $id_slot");
if (mysqli_num_rows($q_slot) == 0) {
    $_SESSION["error"] = "Slot lembur tidak ditemukan";
    header("Location: manager.php");
    exit;
}
$slot = mysqli_fetch_assoc($q_slot);

// Ambil daftar karyawan yang aktif di slot ini
$pemilih = mysqli_query($connection, "SELECT pilihan_lembur.id_pilihan, users.nama, users.email 
    FROM pilihan_lembur, users 
    WHERE pilihan_lembur.id_user = users.id_user 
    AND pilihan_lembur.id_slot = $id_slot 
    AND pilihan_lembur.status = 'aktif'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Slot Lembur - SiLembur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Sistem Lembur</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="manager.php">Kelola Slot Lembur</a>
            </div>
            <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION["username"]) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <a href="manager.php" class="btn btn-secondary btn-sm mb-3">Kembali ke Dashboard</a>

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
            <!-- Form edit slot -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Edit Detail Slot</h5>
                        <form method="POST" action="proses_manager.php">
                            <input type="hidden" name="id_slot" value="<?= $slot["id_slot"] ?>">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= $slot["tanggal"] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" value="<?= substr($slot["jam_mulai"], 0, 5) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" value="<?= substr($slot["jam_selesai"], 0, 5) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kuota Karyawan</label>
                                <input type="number" name="kuota" class="form-control" min="1" value="<?= $slot["kuota"] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Slot</label>
                                <select name="status" class="form-select">
                                    <option value="buka" <?php if ($slot["status"] == "buka") echo "selected"; ?>>Buka</option>
                                    <option value="penuh" <?php if ($slot["status"] == "penuh") echo "selected"; ?>>Penuh</option>
                                    <option value="tutup" <?php if ($slot["status"] == "tutup") echo "selected"; ?>>Tutup</option>
                                </select>
                            </div>
                            <button type="submit" name="edit_slot" class="btn btn-primary w-100">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel pindahkan karyawan -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Karyawan di Slot Ini</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Pindahkan ke Slot Lain</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php while ($p = mysqli_fetch_assoc($pemilih)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <?= htmlspecialchars($p["nama"]) ?><br>
                                                <?= htmlspecialchars($p["email"]) ?>
                                            </td>
                                            <td>
                                                <form method="POST" action="proses_manager.php" onsubmit="return confirm('Pindahkan karyawan ini?')">
                                                    <input type="hidden" name="id_pilihan" value="<?= $p["id_pilihan"] ?>">
                                                    <input type="hidden" name="id_slot_asal" value="<?= $id_slot ?>">

                                                    <select name="id_slot_tujuan" class="form-select form-select-sm mb-2" required>
                                                        <option value="">Pilih Slot Tujuan</option>
                                                        <?php
                                                        // Ambil slot lain yang masih buka
                                                        $q_tujuan = mysqli_query($connection, "SELECT * FROM slot_lembur WHERE id_slot != $id_slot AND status = 'buka' ORDER BY tanggal, jam_mulai");

                                                        while ($tujuan = mysqli_fetch_assoc($q_tujuan)) {
                                                            $id_tujuan = $tujuan["id_slot"];
                                                            $q_cek = mysqli_query($connection, "SELECT COUNT(*) AS jumlah FROM pilihan_lembur WHERE id_slot = $id_tujuan AND status = 'aktif'");
                                                            $data_cek = mysqli_fetch_assoc($q_cek);
                                                            $terisi_tujuan = $data_cek["jumlah"];
                                                            $sisa = $tujuan["kuota"] - $terisi_tujuan;

                                                            if ($sisa > 0) {
                                                                echo "<option value='$id_tujuan'>" . $tujuan["tanggal"] . " (" . substr($tujuan["jam_mulai"], 0, 5) . "-" . substr($tujuan["jam_selesai"], 0, 5) . ") - Sisa: $sisa</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" name="pindah_karyawan" class="btn btn-primary btn-sm">Pindah</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if ($no == 1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada karyawan yang mengambil slot ini</td>
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