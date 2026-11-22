<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "karyawan") {
    header("Location: index.php");
    exit;
}

$id_user = (int) $_SESSION["id_user"];

$warna_baris = [
    "tersedia"     => "table-success",
    "hampir penuh" => "table-warning",
    "penuh"        => "table-danger"
];
$warna_badge = [
    "tersedia"     => "bg-success",
    "hampir penuh" => "bg-warning text-dark",
    "penuh"        => "bg-danger"
];
$teks_status = [
    "tersedia"     => "Tersedia",
    "hampir penuh" => "Hampir penuh",
    "penuh"        => "Penuh"
];

// semua jadwal lembur, jumlah karyawan yang memilih, jadwal yang sudah dipilih oleh karyawan
$slots = mysqli_query($connection, "SELECT slot_lembur.*, 
    (SELECT COUNT(*) FROM pilihan_lembur WHERE pilihan_lembur.id_slot = slot_lembur.id_slot AND pilihan_lembur.status = 'aktif') AS terisi, 
    (SELECT COUNT(*) FROM pilihan_lembur WHERE pilihan_lembur.id_slot = slot_lembur.id_slot AND pilihan_lembur.id_user = $id_user AND pilihan_lembur.status = 'aktif') AS saya FROM slot_lembur ORDER BY tanggal, jam_mulai");

// jadwal lembur karyawan
$jadwal = mysqli_query($connection, "SELECT pilihan_lembur.id_pilihan, slot_lembur.tanggal, slot_lembur.jam_mulai, slot_lembur.jam_selesai 
    FROM pilihan_lembur 
    INNER JOIN slot_lembur ON pilihan_lembur.id_slot = slot_lembur.id_slot 
    WHERE pilihan_lembur.id_user = $id_user AND pilihan_lembur.status = 'aktif' 
    ORDER BY slot_lembur.tanggal, slot_lembur.jam_mulai");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Lembur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Sistem Lembur</span>
            <div class="navbar-nav me-auto">
                <a class="nav-link active" href="karyawan.php">Jadwal Lembur</a>
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

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Slot Lembur dari Manager</h5>
                <div class="table-responsive">
                <table class="table table-bordered bg-white align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Terisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($slots)): ?>
                            <?php
                            $st = status($row["terisi"], $row["kuota"]);
                            if ($row["status"] === "tutup") {
                                $kelas_baris = "table-secondary";
                                $kelas_badge = "bg-secondary";
                                $label = "Ditutup";
                            } else {
                                $kelas_baris = $warna_baris[$st];
                                $kelas_badge = $warna_badge[$st];
                                $label = $teks_status[$st];
                            }
                            ?>
                            <tr class="<?= $kelas_baris ?>">
                                <td><?= $no++ ?></td>
                                <td><?= $row["tanggal"] ?></td>
                                <td><?= substr($row["jam_mulai"], 0, 5) ?> - <?= substr($row["jam_selesai"], 0, 5) ?></td>
                                <td><?= $row["terisi"] ?> / <?= $row["kuota"] ?></td>
                                <td><span class="badge <?= $kelas_badge ?>"><?= $label ?></span></td>
                                <td>
                                    <?php if ($row["saya"] > 0): ?>
                                        Sudah dipilih
                                    <?php elseif ($row["status"] === "tutup"): ?>
                                        -
                                    <?php elseif ($row["terisi"] >= $row["kuota"]): ?>
                                        -
                                    <?php else: ?>
                                        <form method="POST" action="proses.php" onsubmit="return confirm('Pilih slot lembur ini?')">
                                            <input type="hidden" name="id_slot" value="<?= $row["id_slot"] ?>">
                                            <button type="submit" name="pilih" class="btn btn-primary btn-sm">Pilih</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($no === 1): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada slot lembur dari manager</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <h5 class="mb-3">Jadwal Saya</h5>
        <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($jadwal)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row["tanggal"] ?></td>
                        <td><?= substr($row["jam_mulai"], 0, 5) ?> - <?= substr($row["jam_selesai"], 0, 5) ?></td>
                        <td>
                            <form method="POST" action="proses.php" onsubmit="return confirm('Batalkan lembur ini?')">
                                <input type="hidden" name="id_pilihan" value="<?= $row["id_pilihan"] ?>">
                                <button type="submit" name="batal" class="btn btn-danger btn-sm">Batal</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($no === 1): ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada slot yang dipilih</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
