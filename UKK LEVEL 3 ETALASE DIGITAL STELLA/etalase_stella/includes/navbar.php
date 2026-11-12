<?php
// Variabel dari halaman pemanggil: $base ('' atau '../'), $active ('galeri' | 'siswa' | 'users')
$base   = $base ?? '';
$active = $active ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $base ?>index.php">
            <img src="<?= $base ?>assets/img/logo.png" alt="Logo" style="height: 35px; width: auto;">
            <span>Etalase Stella</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $active === 'galeri' ? 'active' : '' ?>" href="<?= $base ?>index.php"><i class="bi bi-grid-fill"></i> Galeri Karya</a>
                </li>
                <?php if (is_staff()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $active === 'siswa' ? 'active' : '' ?>" href="<?= $base ?>admin/siswa.php"><i class="bi bi-people-fill"></i> Kelola Siswa</a>
                    </li>
                <?php endif; ?>
                <?php if (is_super_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $active === 'users' ? 'active' : '' ?>" href="<?= $base ?>admin/users.php"><i class="bi bi-shield-lock-fill"></i> Kelola User</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center text-white gap-3">
                <span>Halo, <b><?= e($_SESSION['nama']) ?></b> <span class="badge bg-primary text-uppercase"><?= e(str_replace('_', ' ', $_SESSION['role'])) ?></span></span>
                <a href="<?= $base ?>auth/logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>
