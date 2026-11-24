document.addEventListener("DOMContentLoaded", function () {

    // Tanggal hari ini menggunakan format YYYY-MM-DD
    var d = new Date();
    var hariIni = d.getFullYear() + "-" +
        String(d.getMonth() + 1).padStart(2, "0") + "-" +
        String(d.getDate()).padStart(2, "0");

    // Validasi jam pada form buat slot dan edit slot 
    document.querySelectorAll("form").forEach(function (form) {
        var mulai = form.querySelector("input[name='jam_mulai']");
        var selesai = form.querySelector("input[name='jam_selesai']");
        if (!mulai || !selesai) return;

        form.addEventListener("submit", function (e) {
            if (selesai.value <= mulai.value) {
                e.preventDefault();
                alert("Jam selesai harus lebih akhir dari jam mulai");
                selesai.focus();
            }
        });

        // Tanggal untuk buat slot baru tidak boleh sebelum hari ini
        if (form.querySelector("[name='tambah_slot']")) {
            var tanggal = form.querySelector("input[name='tanggal']");
            if (tanggal) tanggal.min = hariIni;
        }
    });

    // Pengecekan konfirmasi password pada halaman register
    var password = document.querySelector("input[name='password']");
    var konfirmasi = document.querySelector("input[name='konfirmasi_password']");
    if (password && konfirmasi) {
        konfirmasi.form.addEventListener("submit", function (e) {
            if (password.value !== konfirmasi.value) {
                e.preventDefault();
                alert("Konfirmasi password tidak cocok");
                konfirmasi.focus();
            }
        });
    }

    // Penghapusan pesan sukses/error secara otomatis setelah 4 detik
    document.querySelectorAll(".alert").forEach(function (alertBox) {
        setTimeout(function () {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(function () { alertBox.remove(); }, 500);
        }, 4000);
    });
});