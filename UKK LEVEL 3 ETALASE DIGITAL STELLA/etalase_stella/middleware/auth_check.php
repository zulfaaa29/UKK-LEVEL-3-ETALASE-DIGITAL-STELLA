<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Folder upload gambar project & password default saat admin mereset password siswa
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
const DEFAULT_RESET_PASSWORD = '654321';

/** Escape output HTML. */
function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * $base = awal path relatif ke root aplikasi ('' untuk halaman di root,
 * '../' untuk halaman di dalam folder admin/ atau auth/).
 */
function check_login(string $base = ''): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $base . 'auth/login.php');
        exit();
    }
}

function check_role(array $roles, string $base = ''): void {
    check_login($base);
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die("Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.");
    }
}

function is_super_admin(): bool {
    return ($_SESSION['role'] ?? '') === 'super_admin';
}

/** Admin atau Super Admin. */
function is_staff(): bool {
    return in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'], true);
}

/* ---------- CSRF ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        die("Sesi tidak valid. Silakan muat ulang halaman lalu coba lagi.");
    }
}

/* ---------- Flash message ---------- */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function render_flash(): string {
    if (!isset($_SESSION['flash'])) {
        return '';
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $icon = $f['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    return '<div class="alert alert-' . e($f['type']) . ' py-2"><i class="bi bi-' . $icon . ' me-2"></i>'
         . e($f['message']) . '</div>';
}
