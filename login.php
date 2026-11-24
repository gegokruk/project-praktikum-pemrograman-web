<?php
session_start();
require "koneksi.php";

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: " . $_SESSION["role"] . ".php");
    exit;
}

$error = "";

if (isset($_COOKIE["remember_email"])){
    $simpanEmail = $_COOKIE["remember_email"];
} else {
    $simpanEmail = "";
}

if (isset($_POST["login"])) {
    $email_asli = $_POST["email"];
    $email = mysqli_real_escape_string($connection, $email_asli);
    $password = $_POST["password"];

    $query = mysqli_query($connection, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 0) {
        $error = "Email tidak ditemukan";
    } else {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["id_user"] = $user["id_user"];
            $_SESSION["username"] = $user["nama"];
            $_SESSION["role"] = $user["role"];

            // Remember Me (Cookie 7 hari)
            if (isset($_POST["remember"])) {
                setcookie("remember_email", $email_asli, time() + (86400 * 7), "/");
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }

            // Dashboard yang digunakan
            if ($user["role"] === "manager") {
                header("Location: manager.php");
            } else {
                header("Location: karyawan.php");
            }
            exit;
        } else {
            $error = "Password salah";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiLembur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="text-center mb-1">SiLembur</h4>
                <p class="text-center text-muted mb-4">Sistem Manajemen Lembur & Jam Kerja</p>

                <?php if ($error != ""): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($simpanEmail) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember" <?= $simpanEmail != "" ? "checked" : "" ?>>
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">
                        Login
                    </button>
                </form>

                <p class="text-center mt-3 mb-0 small">
                    Belum punya akun karyawan? <a href="register.php">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>