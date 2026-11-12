<?php
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
require_once 'includes/helpers.php';

check_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nama_project = trim($_POST['nama_project'] ?? '');
    $deskripsi    = trim($_POST['deskripsi'] ?? '');
    $link_project = trim($_POST['link_project'] ?? '');

    if ($nama_project === '' || $deskripsi === '' || $link_project === '') {
        $error = "Semua kolom input wajib diisi!";
    } elseif (!valid_http_url($link_project)) {
        $error = "Format Link Project tidak valid (gunakan format https://...)!";
    } else {
        $gambar = save_project_image($_FILES['gambar'] ?? [], UPLOAD_DIR, $error);

        if ($gambar !== null) {
            try {
                $stmt = $pdo->prepare("INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project)
                                       VALUES (:user_id, :nama_project, :deskripsi, :gambar, :link_project)");
                $stmt->execute([
                    ':user_id'      => $_SESSION['user_id'],
                    ':nama_project' => $nama_project,
                    ':deskripsi'    => $deskripsi,
                    ':gambar'       => $gambar,
                    ':link_project' => $link_project,
                ]);
                set_flash('success', 'Project berhasil ditambahkan.');
                header('Location: index.php');
                exit();
            } catch (PDOException $ex) {
                delete_project_image(UPLOAD_DIR, $gambar);
                $error = "Terjadi kesalahan sistem saat menyimpan project.";
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
    <title>Tambah Project - Stella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-form p-4">
                <h3 class="fw-bold mb-4"><i class="bi bi-file-earmark-plus text-success"></i> Tambah Project Baru</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Project</label>
                        <input type="text" name="nama_project" class="form-control" maxlength="150" placeholder="Contoh: Aplikasi Kasir Cafe Stella" value="<?= e($_POST['nama_project'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Project</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan fitur utama dan teknologi yang digunakan..." required><?= e($_POST['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Gambar (Preview / Mockup, maks. 2MB)</label>
                        <input type="file" id="gambar" name="gambar" class="form-control" accept="image/png, image/jpeg, image/webp" required>
                        <div class="preview-box" id="previewBox">
                            <p class="small text-muted mb-1">Pratinjau Gambar:</p>
                            <img id="imagePreview" src="" alt="Preview Gambar">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Link Project (GitHub / Website Live)</label>
                        <input type="url" name="link_project" class="form-control" maxlength="255" placeholder="https://github.com/..." value="<?= e($_POST['link_project'] ?? '') ?>" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg"></i> Simpan Project</button>
                        <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('gambar').addEventListener('change', function () {
    var box = document.getElementById('previewBox');
    var img = document.getElementById('imagePreview');
    var file = this.files[0];
    if (!file) { img.removeAttribute('src'); box.style.display = 'none'; return; }
    var reader = new FileReader();
    reader.addEventListener('load', function () { img.src = reader.result; box.style.display = 'block'; });
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
