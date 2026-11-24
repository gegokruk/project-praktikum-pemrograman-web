<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "manager") {
    header("Location: login.php");
    exit;
}

$id_manager = (int) $_SESSION["id_user"];

// Proses tambah slot
if (isset($_POST["tambah_slot"])) {
    $tanggal     = mysqli_real_escape_string($connection, $_POST["tanggal"]);
    $jam_mulai   = mysqli_real_escape_string($connection, $_POST["jam_mulai"]);
    $jam_selesai = mysqli_real_escape_string($connection, $_POST["jam_selesai"]);
    $kuota       = (int) $_POST["kuota"];

    if ($jam_selesai <= $jam_mulai) {
        $_SESSION["error"] = "Jam selesai harus lebih akhir dari jam mulai";
    } elseif ($kuota < 1) {
        $_SESSION["error"] = "Kuota karyawan minimal 1 orang";
    } else {
        $query = "INSERT INTO slot_lembur (tanggal, jam_mulai, jam_selesai, kuota, status, dibuat_oleh) 
                    VALUES ('$tanggal', '$jam_mulai', '$jam_selesai', $kuota, 'buka', $id_manager)";
        if (mysqli_query($connection, $query)) {
            $_SESSION["success"] = "Slot jadwal lembur berhasil ditambahkan";
        } else {
            $_SESSION["error"] = "Gagal menambahkan slot: " . mysqli_error($connection);
        }
    }
    header("Location: manager.php");
    exit;
}

// Proses hapus slot
if (isset($_POST["hapus_slot"])) {
    $id_slot = (int) $_POST["id_slot"];
    $query = "DELETE FROM slot_lembur WHERE id_slot = $id_slot";

    if (mysqli_query($connection, $query)) {
        $_SESSION["success"] = "Slot lembur berhasil dihapus";
    } else {
        $_SESSION["error"] = "Gagal menghapus slot: " . mysqli_error($connection);
    }
    header("Location: manager.php");
    exit;
}

// Ambil semua data slot lembur beserta jumlah pemilih aktif
$slots = mysqli_query($connection, "SELECT slot_lembur.*, 
    (SELECT COUNT(*) FROM pilihan_lembur 
        WHERE pilihan_lembur.id_slot = slot_lembur.id_slot AND pilihan_lembur.status = 'aktif') AS terisi 
    FROM slot_lembur 
    ORDER BY tanggal ASC, jam_mulai ASC");
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
            <!-- Form slot baru -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">Buat Slot Lembur Baru</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lembur</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kuota Maksimal (Orang)</label>
                                <input type="number" name="kuota" class="form-control" min="1" value="3" required>
                            </div>
                            <button type="submit" name="tambah_slot" class="btn btn-primary w-100">
                                Simpan Slot
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel daftar slot & kuota -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Daftar Slot Lembur</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal & Jam</th>
                                        <th>Kuota</th>
                                        <th>Status</th>
                                        <th>Karyawan Terdaftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($slots)): ?>
                                        <?php
                                        $id_slot_ini = (int) $row["id_slot"];
                                        $kelebihan   = $row["terisi"] > $row["kuota"];
                                        $ditutup     = $row["status"] === "tutup";

                                        if ($kelebihan) {
                                            $warna_baris = "table-danger";
                                            $warna_badge = "bg-danger";
                                            $teks_status = "Kelebihan Kuota!";
                                        } elseif ($ditutup) {
                                            $warna_baris = "table-secondary";
                                            $warna_badge = "bg-secondary";
                                            $teks_status = "Ditutup";
                                        } elseif (status($row["terisi"], $row["kuota"]) === "penuh") {
                                            $warna_baris = "table-danger";
                                            $warna_badge = "bg-danger";
                                            $teks_status = "Penuh";
                                        } elseif (status($row["terisi"], $row["kuota"]) === "hampir penuh") {
                                            $warna_baris = "table-warning";
                                            $warna_badge = "bg-warning text-dark";
                                            $teks_status = "Hampir penuh";
                                        } else {
                                            $warna_baris = "";
                                            $warna_badge = "bg-success";
                                            $teks_status = "Tersedia";
                                        }

                                        // Ambil daftar nama karyawan yang aktif di slot ini
                                        $q_pemilih = mysqli_query($connection, "SELECT users.nama FROM pilihan_lembur 
                                            INNER JOIN users ON pilihan_lembur.id_user = users.id_user 
                                            WHERE pilihan_lembur.id_slot = $id_slot_ini AND pilihan_lembur.status = 'aktif'");
                                        $daftar_nama = [];
                                        while ($p = mysqli_fetch_assoc($q_pemilih)) {
                                            $daftar_nama[] = htmlspecialchars($p["nama"]);
                                        }
                                        ?>
                                        <tr class="<?= $warna_baris ?>">
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $row["tanggal"] ?></strong><br>
                                                <small class="text-muted"><?= substr($row["jam_mulai"], 0, 5) ?> - <?= substr($row["jam_selesai"], 0, 5) ?></small>
                                            </td>
                                            <td><?= $row["terisi"] ?> / <?= $row["kuota"] ?></td>
                                            <td><span class="badge <?= $warna_badge ?>"><?= $teks_status ?></span></td>
                                            <td>
                                                <?php if (count($daftar_nama) > 0): ?>
                                                    <small><?= implode(", ", $daftar_nama) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Belum ada</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="edit_slot.php?id=<?= $row["id_slot"] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus slot ini?')">
                                                        <input type="hidden" name="id_slot" value="<?= $row["id_slot"] ?>">
                                                        <button type="submit" name="hapus_slot" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if ($no === 1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada slot lembur yang dibuat.</td>
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
</body>
</html>