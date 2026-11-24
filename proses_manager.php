<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "manager") {
    header("Location: login.php");
    exit;
}

$id_manager = (int) $_SESSION["id_user"];

// Function helper kembali ke dashboard utama
function kembali($jenis, $pesan, $halaman = "manager.php") {
    $_SESSION[$jenis] = $pesan;
    header("Location: " . $halaman);
    exit;
}

// Fungsi bantu untuk memperbarui status slot jadi 'penuh' atau 'buka' otomatis
function perbaruiStatusSlot($connection, $id_slot) {
    $q_slot = mysqli_query($connection, "SELECT kuota, status FROM slot_lembur WHERE id_slot = $id_slot");
    $slot = mysqli_fetch_assoc($q_slot);

    // Kalau ditutup manual oleh manager, biarkan tetap tutup
    if ($slot["status"] == "tutup") {
        return;
    }

    $q_hitung = mysqli_query($connection, "SELECT COUNT(*) AS jumlah FROM pilihan_lembur WHERE id_slot = $id_slot AND status = 'aktif'");
    $data_hitung = mysqli_fetch_assoc($q_hitung);
    $terisi = $data_hitung["jumlah"];

    if ($terisi >= $slot["kuota"]) {
        $status_baru = "penuh";
    } else {
        $status_baru = "buka";
    }

    mysqli_query($connection, "UPDATE slot_lembur SET status = '$status_baru' WHERE id_slot = $id_slot");
}

// tambah Slot
if (isset($_POST["tambah_slot"])) {
    $tanggal     = mysqli_real_escape_string($connection, $_POST["tanggal"]);
    $jam_mulai   = mysqli_real_escape_string($connection, $_POST["jam_mulai"]);
    $jam_selesai = mysqli_real_escape_string($connection, $_POST["jam_selesai"]);
    $kuota       = (int) $_POST["kuota"];

    if ($jam_selesai <= $jam_mulai) {
        kembali("error", "Jam selesai harus lebih akhir dari jam mulai");
    }
    if ($kuota < 1) {
        kembali("error", "Kuota karyawan minimal 1 orang");
    }

    $query = "INSERT INTO slot_lembur (tanggal, jam_mulai, jam_selesai, kuota, status, dibuat_oleh) 
                VALUES ('$tanggal', '$jam_mulai', '$jam_selesai', $kuota, 'buka', $id_manager)";

    if (mysqli_query($connection, $query)) {
        kembali("success", "Slot jadwal lembur berhasil ditambahkan");
    } else {
        kembali("error", "Gagal menambahkan slot");
    }
}

// Edit slot
if (isset($_POST["edit_slot"])) {
    $id_slot     = (int) $_POST["id_slot"];
    $tanggal     = mysqli_real_escape_string($connection, $_POST["tanggal"]);
    $jam_mulai   = mysqli_real_escape_string($connection, $_POST["jam_mulai"]);
    $jam_selesai = mysqli_real_escape_string($connection, $_POST["jam_selesai"]);
    $kuota       = (int) $_POST["kuota"];
    $status      = mysqli_real_escape_string($connection, $_POST["status"]);

    if ($jam_selesai <= $jam_mulai) {
        kembali("error", "Jam selesai harus lebih akhir dari jam mulai", "edit_slot.php?id=" . $id_slot);
    }
    if ($kuota < 1) {
        kembali("error", "Kuota karyawan minimal 1 orang", "edit_slot.php?id=" . $id_slot);
    }

    $query = "UPDATE slot_lembur 
                SET tanggal = '$tanggal', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai', kuota = $kuota, status = '$status' 
                WHERE id_slot = $id_slot";

    if (mysqli_query($connection, $query)) {
        if ($status != "tutup") {
            perbaruiStatusSlot($connection, $id_slot);
        }
        kembali("success", "Perubahan slot berhasil disimpan", "manager.php");
    } else {
        kembali("error", "Gagal menyimpan perubahan", "edit_slot.php?id=" . $id_slot);
    }
}

//  Hapus slot
if (isset($_POST["hapus_slot"])) {
    $id_slot = (int) $_POST["id_slot"];
    $query = "DELETE FROM slot_lembur WHERE id_slot = $id_slot";

    if (mysqli_query($connection, $query)) {
        kembali("success", "Slot lembur berhasil dihapus");
    } else {
        kembali("error", "Gagal menghapus slot");
    }
}

// Pindah Karyawan ke slot lain
if (isset($_POST["pindah_karyawan"])) {
    $id_pilihan     = (int) $_POST["id_pilihan"];
    $id_slot_asal   = (int) $_POST["id_slot_asal"];
    $id_slot_tujuan = (int) $_POST["id_slot_tujuan"];
    $halaman_balik  = "edit_slot.php?id=" . $id_slot_asal;

    // Cari id_user yang mau dipindahkan
    $q_pilihan = mysqli_query($connection, "SELECT id_user FROM pilihan_lembur WHERE id_pilihan = $id_pilihan AND status = 'aktif'");
    if (mysqli_num_rows($q_pilihan) == 0) {
        kembali("error", "Data pilihan tidak ditemukan", $halaman_balik);
    }
    $data_pilihan = mysqli_fetch_assoc($q_pilihan);
    $id_user = $data_pilihan["id_user"];

    // Cek apakah karyawan sudah aktif di slot tujuan
    $q_duplikat = mysqli_query($connection, "SELECT id_pilihan FROM pilihan_lembur WHERE id_user = $id_user AND id_slot = $id_slot_tujuan AND status = 'aktif'");
    if (mysqli_num_rows($q_duplikat) > 0) {
        kembali("error", "Karyawan ini sudah terdaftar di slot tujuan", $halaman_balik);
    }

    // Ubah status jadwal lama jadi 'dipindahkan'
    mysqli_query($connection, "UPDATE pilihan_lembur SET status = 'dipindahkan' WHERE id_pilihan = $id_pilihan");

    // Cek apakah karyawan pernah punya riwayat di slot tujuan sebelumnya
    $q_riwayat = mysqli_query($connection, "SELECT id_pilihan FROM pilihan_lembur WHERE id_user = $id_user AND id_slot = $id_slot_tujuan");
    if (mysqli_num_rows($q_riwayat) > 0) {
        mysqli_query($connection, "UPDATE pilihan_lembur SET status = 'aktif' WHERE id_user = $id_user AND id_slot = $id_slot_tujuan");
    } else {
        mysqli_query($connection, "INSERT INTO pilihan_lembur (id_user, id_slot, status) VALUES ($id_user, $id_slot_tujuan, 'aktif')");
    }

    // Update status penuh/buka untuk kedua slot
    perbaruiStatusSlot($connection, $id_slot_asal);
    perbaruiStatusSlot($connection, $id_slot_tujuan);

    kembali("success", "Karyawan berhasil dipindahkan", $halaman_balik);
}

header("Location: manager.php");
exit;
?>