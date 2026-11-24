<?php 
// Fungsi bantu untuk menyimpan pilihan karyawan ke sebuah slot
function simpanPilihan($connection, $id_user, $id_slot) {
    $q = mysqli_query($connection, "SELECT id_pilihan FROM pilihan_lembur WHERE id_user = $id_user AND id_slot = $id_slot");
    if (mysqli_num_rows($q) > 0) {
        mysqli_query($connection, "UPDATE pilihan_lembur SET status = 'aktif' WHERE id_user = $id_user AND id_slot = $id_slot");
    } else {
        mysqli_query($connection, "INSERT INTO pilihan_lembur (id_user, id_slot, status) VALUES ($id_user, $id_slot, 'aktif')");
    }
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
    $terisi = mysqli_fetch_assoc($q_hitung)["jumlah"];

    if ($terisi >= $slot["kuota"]) {
        $status_baru = "penuh";
    } else {
        $status_baru = "buka";
    }

    mysqli_query($connection, "UPDATE slot_lembur SET status = '$status_baru' WHERE id_slot = $id_slot");
}
?>