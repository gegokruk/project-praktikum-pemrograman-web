<?php
session_start();
require "koneksi.php";
require "penghubung.php";

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["role"] !== "karyawan") {
    header("Location: login.php");
    exit;
}

$id_user = (int) $_SESSION["id_user"];

function kembali($jenis, $pesan) {
    $_SESSION[$jenis] = $pesan;
    header("Location: karyawan.php");
    exit;
}

// Memilih slot lembur
if (isset($_POST["pilih"])) {
    $id_slot = (int) $_POST["id_slot"];

    // Mengambil data slot lembur dari database
    $q = mysqli_query($connection, "SELECT kuota, status FROM slot_lembur WHERE id_slot = $id_slot");
    if (mysqli_num_rows($q) === 0) {
        kembali("error", "Slot tidak ditemukan");
    }
    $slot = mysqli_fetch_assoc($q);

    // Banyak orang yang sudah memilih slot lembur
    $q = mysqli_query($connection, "SELECT COUNT(*) AS jumlah FROM pilihan_lembur 
        WHERE id_slot = $id_slot AND status = 'aktif'");
    $terisi = mysqli_fetch_assoc($q)["jumlah"];

    // Mengecek apakah karyawan memilih slot yang sudah pernah dia pilih
    $q = mysqli_query($connection, "SELECT id_pilihan FROM pilihan_lembur 
        WHERE id_user = $id_user AND id_slot = $id_slot AND status = 'aktif'");
    $sudah_memilih = mysqli_num_rows($q) > 0;

    if ($sudah_memilih) {
        kembali("error", "Kamu sudah memilih slot ini");
    }
    if ($slot["status"] === "tutup") {
        kembali("error", "Slot ini sedang ditutup oleh manager");
    }
    if ($terisi >= $slot["kuota"]) {
        kembali("error", "Slot sudah penuh");
    }

    simpanPilihan($connection, $id_user, $id_slot);
    perbaruiStatusSlot($connection, $id_slot);
    kembali("success", "Slot lembur berhasil dipilih");
}

// Pembatalan slot lembur
if (isset($_POST["batal"])) {
    $id_pilihan = (int) $_POST["id_pilihan"];

    // Pilihan milik karyawan yang login dan masih aktif
    $q = mysqli_query($connection, "SELECT id_slot FROM pilihan_lembur 
        WHERE id_pilihan = $id_pilihan AND id_user = $id_user AND status = 'aktif'");
    if (mysqli_num_rows($q) === 0) {
        kembali("error", "Pilihan lembur tidak ditemukan");
    }
    $id_slot = (int) mysqli_fetch_assoc($q)["id_slot"];

    // Ubah status jadi dibatalkan
    mysqli_query($connection, "UPDATE pilihan_lembur SET status = 'dibatalkan' WHERE id_pilihan = $id_pilihan");

    // Pengurangan kapasitas slot
    perbaruiStatusSlot($connection, $id_slot);
    kembali("success", "Pilihan lembur dibatalkan");
}

header("Location: karyawan.php");
exit;

?>