<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
        header('Location: ../index.php');
        exit();
    }
    $error = "Email atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Etalase Digital Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="card card-login p-4">
    <div class="text-center mb-4">
        <img src="../assets/img/logo.png" alt="Logo Telkom Schools" style="max-height: 60px; width: auto;" class="mb-2">
        <h3 class="fw-bold mt-2">Etalase Stella</h3>
        <p class="text-muted small">Silakan masuk ke akun Anda</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">Login <i class="bi bi-arrow-right-short"></i></button>
    </form>

    <div class="text-center pt-2 border-top">
        <span class="text-muted small">Belum punya akun?</span>
        <a href="register.php" class="text-primary fw-semibold small text-decoration-none">Daftar Siswa</a>
    </div>
</div>

</body>
</html>
