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
project-pemweb-kel2/
    ├── database.sql     # Skema 3 tabel + data awal
    ├── koneksi.php      # Koneksi DB & session_start()
    ├── login.php        # Login, cookie "Ingat Saya", redirect role
    ├── register.php     # Daftar akun karyawan
    ├── logout.php       # Hapus session & logout
    ├── manager.php      # CRUD slot lembur (manager)
    ├── edit_slot.php    # Edit slot & pindah jadwal karyawan
    ├── karyawan.php     # Pilih & batal slot lembur (karyawan)
    ├── README.md        # Dokumentasi proyek
    └── assets/
          ├── style.css  # Custom CSS & badge kuota
          └── script.js  # Konfirmasi aksi & validasi jam