<?php
session_start();
require "koneksi.php";

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: " . $_SESSION["role"] . ".php");
    exit;
}

$error = "";
$sukses = "";

if (isset($_POST["register"])) {
    $nama = mysqli_real_escape_string($connection, $_POST["nama"]);
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $password = $_POST["password"];
    $konfirmasi = $_POST["konfirmasi_password"];

    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok";
    } else {
        $cek_email = mysqli_query($connection, "SELECT id_user FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar, gunakan email lain";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password_hash', 'karyawan')";
            
            if (mysqli_query($connection, $query)) {
                $sukses = "Registrasi berhasil! Silakan login";
            } else {
                $error = "Gagal mendaftarkan akun: " . mysqli_error($connection);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Karyawan - SiLembur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 420px;">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="text-center mb-1">Daftar Akun Karyawan</h4>
                <p class="text-center text-muted mb-4">Buat akun untuk memilih jadwal lembur</p>

                <?php if ($error != ""): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($sukses != ""): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($sukses) ?> <a href="login.php">Login sekarang</a>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="konfirmasi_password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-success w-100">
                        Daftar
                    </button>
                </form>

                <p class="text-center mt-3 mb-0 small">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>