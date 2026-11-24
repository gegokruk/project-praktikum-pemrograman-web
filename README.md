<div align="center">

<img src="assets/logo/UPNYK.png" alt="Logo UPNYK" height="48" />
&nbsp;&nbsp;&nbsp;
<img src="assets/logo/LOGO_IF.png" alt="Logo Informatika" height="48" />
&nbsp;&nbsp;&nbsp;
<img src="assets/logo/LOGO_HIMA.png" alt="Logo Informatika" height="50" />

# Sistem Manajemen Lembur & Fleksibilitas Jam Kerja

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

</div>

Proyek Praktikum Pemrograman Web - Kelompok 2

## Anggota Kelompok

* **Gega Ramadhan** (NIM: 123250112)
* **Raihan Akbar Abdillah** (NIM: 123250140)

## Deskripsi Proyek

Aplikasi web berbasis PHP dan MySQL untuk mengelola penjadwalan lembur karyawan dengan sistem **Slot & Kuota**. Manager dapat membuat slot jadwal lembur beserta batas kuota karyawan per hari, sedangkan karyawan dapat memilih jadwal lembur yang tersedia atau membatalkannya sebelum batas waktu (H-1) berakhir

## Implementasi Materi Praktikum

1. **Autentikasi (Login & Register):** Registrasi akun mandiri untuk role `karyawan`, serta pengalihan halaman otomatis berdasarkan role (`manager` atau `karyawan`) saat login
2. **Session & Cookie:** `$_SESSION` digunakan untuk menyimpan data pengguna aktif dan membatasi hak akses halaman; `$_COOKIE` digunakan untuk fitur *"Ingat Saya"* pada halaman login
3. **CRUD Lengkap (Create, Read, Update, Delete):** Pengelolaan data slot lembur oleh manager dan pengambilan/pembatalan jadwal lembur oleh karyawan
4. **HTML, CSS & Bootstrap:** Antarmuka responsif menggunakan komponen Bootstrap (Navbar, Card, Table, Form, dan Badge indikator status kuota)
5. **JavaScript Sederhana:** Konfirmasi dialog sebelum membatalkan/menghapus jadwal serta validasi input jam pada form pembuatan slot

## Struktur Folder dan File

```text
project-praktikum-pemrograman-web/
    ├── assets/
    │   ├── img/                 # Aset gambar
    │   ├── logo/                # Logo 
    │   └── style.css            # CSS halaman utama
    ├── docs/                    # Dokumen laporan
    ├── database.sql             # Skema 3 tabel
    ├── index.php                # Halaman utama
    ├── koneksi.php              # Koneksi database MySQLi
    ├── penghubung.php           # Kumpulan fungsi pembantu
    ├── login.php                # Form autentikasi login 
    ├── register.php             # Form registrasi akun karyawan baru
    ├── logout.php               # Penghapusan session dan redirect
    ├── manager.php              # [UI] Dashboard manager
    ├── edit_slot.php            # [UI] Halaman edit slot & pindah jadwal karyawan
    ├── proses_manager.php       # [Logika] Tambah, edit, hapus & pindah karyawan
    ├── karyawan.php             # [UI] Dashboard karyawan
    ├── proses_karyawan.php      # [Logika] Proses pemilihan dan pembatalan slot karyawan
    ├── script.js                # Validasi dan interaksi JavaScript sisi klien
    └── README.md                # Dokumentasi proyek
```

## Rancangan Basis Data (`db_lembur`)

Aplikasi menggunakan tiga tabel utama yang saling berelasi:

* `users` : Menyimpan data akun pengguna (`id_user`, `nama`, `email`, `password`, `role`, `created_at`).
* `slot_lembur` : Menyimpan jadwal lembur yang dibuat oleh manager (`id_slot`, `tanggal`, `jam_mulai`, `jam_selesai`, `kuota`, `status`, `dibuat_oleh`, `created_at`).
* `pilihan_lembur` : Menyimpan data transaksi pengambilan slot oleh karyawan (`id_pilihan`, `id_user`, `id_slot`, `status`, `created_at`).

## Cara Menjalankan di Lokal

1. Pastikan **Apache** dan **MySQL** sudah aktif (melalui XAMPP atau Laragon)
2. Salin folder proyek ini ke dalam direktori `htdocs` (XAMPP) atau `www` (Laragon)
3. Buka `http://localhost/phpmyadmin`, kemudian import file `database.sql` untuk membuat database `db_lembur` beserta data awalnya
4. Pastikan pengaturan host, username, dan password pada `koneksi.php` sesuai dengan konfigurasi MySQL lokal (`localhost`, `root`, password kosong)
5. Buka browser dan akses `http://localhost/project-praktikum-pemrograman-web/`

## Akun Uji Coba (Default)

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| **Manager** | `manager@kantor.com` | `password` |
| **Karyawan** | `budi@kantor.com` | `password` |
| **Karyawan** | `siti@kantor.com` | `password` |
| **Karyawan** | `andi@kantor.com` | `password` |