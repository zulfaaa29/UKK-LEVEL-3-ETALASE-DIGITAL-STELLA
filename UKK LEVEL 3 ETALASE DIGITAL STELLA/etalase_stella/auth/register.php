<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        try {
            // Pendaftaran publik selalu berperan 'siswa'
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, 'siswa')");
            $stmt->execute([
                ':nama'     => $nama,
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $success = "Pendaftaran berhasil! Silakan login.";
        } catch (PDOException $ex) {
            $error = is_duplicate_error($ex)
                ? "Email sudah terdaftar, silakan gunakan email lain atau login."
                : "Terjadi kesalahan saat mendaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Etalase Digital Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="card card-login p-4 my-4">
    <div class="text-center mb-4">
        <img src="../assets/img/logo.png" alt="Logo Telkom Schools" style="max-height: 60px; width: auto;" class="mb-2">
        <h3 class="fw-bold mt-2">Registrasi Siswa</h3>
        <p class="text-muted small">Buat akun siswa untuk memamerkan karya Anda</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= e($success) ?>
            <div class="mt-2">
                <a href="login.php" class="btn btn-sm btn-success w-100">Ke Halaman Login</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" value="<?= e($_POST['nama'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Buat password (min. 6 karakter)" minlength="6" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">Daftar Sekarang <i class="bi bi-arrow-right-short"></i></button>
    </form>
    <?php endif; ?>

    <div class="text-center pt-2 border-top">
        <span class="text-muted small">Sudah punya akun?</span>
        <a href="login.php" class="text-primary fw-semibold small text-decoration-none">Login di sini</a>
    </div>
</div>

</body>
</html>
