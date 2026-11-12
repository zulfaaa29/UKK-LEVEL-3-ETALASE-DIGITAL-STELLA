<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';
require_once '../includes/helpers.php';

// Admin & Super Admin boleh mengelola akun siswa
check_role(['admin', 'super_admin'], '../');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($nama === '' || $email === '' || $password === '') {
            $error = "Semua kolom wajib diisi!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid!";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal 6 karakter!";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :pass, 'siswa')");
                $stmt->execute([
                    ':nama'  => $nama,
                    ':email' => $email,
                    ':pass'  => password_hash($password, PASSWORD_DEFAULT),
                ]);
                set_flash('success', 'Akun siswa berhasil ditambahkan.');
                header('Location: siswa.php');
                exit();
            } catch (PDOException $ex) {
                $error = is_duplicate_error($ex) ? "Email sudah terdaftar!" : "Gagal menyimpan data siswa.";
            }
        }
    } elseif ($action === 'reset') {
        $stmt = $pdo->prepare("UPDATE users SET password = :pass WHERE id = :id AND role = 'siswa'");
        $stmt->execute([
            ':pass' => password_hash(DEFAULT_RESET_PASSWORD, PASSWORD_DEFAULT),
            ':id'   => (int) ($_POST['id'] ?? 0),
        ]);
        set_flash('success', 'Password siswa direset menjadi ' . DEFAULT_RESET_PASSWORD . '.');
        header('Location: siswa.php');
        exit();
    } elseif ($action === 'hapus') {
        if (delete_user_with_files($pdo, (int) ($_POST['id'] ?? 0), ['siswa'])) {
            set_flash('success', 'Akun siswa beserta karyanya berhasil dihapus.');
        }
        header('Location: siswa.php');
        exit();
    }
}

$siswa = $pdo->query("SELECT * FROM users WHERE role = 'siswa' ORDER BY id DESC")->fetchAll();

$base   = '../';
$active = 'siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa - Etalase Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/kelola.css">
</head>
<body>

<?php require __DIR__ . '/../includes/navbar.php'; ?>

<div class="container my-4">
    <?= render_flash() ?>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-person-plus-fill text-primary"></i> Tambah Siswa</h4>
                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Siswa" value="<?= e($_POST['nama'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="email@siswa.com" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-check-fill"></i> Simpan Data Siswa</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-people-fill text-dark"></i> Daftar Akun Siswa</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($siswa) === 0): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada akun siswa.</td></tr>
                            <?php endif; ?>
                            <?php $no = 1; foreach ($siswa as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><b><?= e($row['nama']) ?></b></td>
                                <td><code><?= e($row['email']) ?></code></td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <form method="POST" onsubmit="return confirm('Reset password siswa ini menjadi <?= e(DEFAULT_RESET_PASSWORD) ?>?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="reset">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-key"></i> Reset Password</button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Hapus akun siswa ini beserta karya yang diunggah?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="hapus">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
