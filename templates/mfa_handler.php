<?php
// templates/mfa_handler.php
// Standalone MFA handler - processes code and sets session

require_once __DIR__ . '/../src/app.php';

$lang = $_GET['lang'] ?? 'en';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=login&lang=$lang");
    exit;
}

// Validate CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    header("Location: ../index.php?page=login&lang=$lang&error=" . urlencode($lang === 'es' ? 'Sesion expirada.' : 'Session expired.'));
    exit;
}

session_start();

// Check session
if (empty($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login&lang=$lang&error=" . urlencode($lang === 'es' ? 'Sesion invalida.' : 'Invalid session.'));
    exit;
}

// Verify MFA code
$submitted = implode('', $_POST['code'] ?? []);
$stored = $_SESSION['mfa_code'] ?? '';

if ($submitted !== $stored) {
    header("Location: ../mfa.php?lang=$lang&invalid=1");
    exit;
}

// MFA verified - clear code and mark session
unset($_SESSION['mfa_code']);
$_SESSION['mfa_verified'] = true;

// Regenerate session ID (security best practice post-auth)
if (function_exists('session_regenerate_id')) {
    session_regenerate_id(true);
}

// Redirect to dashboard
header("Location: ../dashboard.php?lang=$lang");
exit;
