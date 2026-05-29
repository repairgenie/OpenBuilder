<?php
// templates/login_handler.php
// Standalone login handler - no output buffering issues

require_once __DIR__ . '/../src/app.php';

$lang = $_GET['lang'] ?? 'en';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=login&lang=$lang");
    exit;
}

// Validate CSRF
$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    header("Location: ../index.php?page=login&lang=$lang&error=" . urlencode($lang === 'es' ? 'Sesion expirada. Intente de nuevo.' : 'Session expired. Please try again.'));
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Demo credentials (hardcoded for prototype - query users table in production)
$valid_users = [
    'admin@openbuilder.com' => [
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'name' => 'Admin User',
        'role' => 'Admin',
        'user_id' => 1,
    ],
    'manager@openbuilder.com' => [
        'password_hash' => password_hash('manager123', PASSWORD_DEFAULT),
        'name' => 'Project Manager',
        'role' => 'Manager',
        'user_id' => 2,
    ],
    'sub@openbuilder.com' => [
        'password_hash' => password_hash('sub123', PASSWORD_DEFAULT),
        'name' => 'Subcontractor',
        'role' => 'Subcontractor',
        'user_id' => 3,
    ],
];

if (!isset($valid_users[$email]) || !password_verify($password, $valid_users[$email]['password_hash'])) {
    header("Location: ../index.php?page=login&lang=$lang&error=" . urlencode($lang === 'es' ? 'Credenciales invalidas.' : 'Invalid credentials.'));
    exit;
}

// Start session and set user data
$_SESSION['user_id'] = $valid_users[$email]['user_id'];
$_SESSION['email'] = $email;
$_SESSION['name'] = $valid_users[$email]['name'];
$_SESSION['role'] = $valid_users[$email]['role'];
$_SESSION['logged_in'] = true;

// Generate and store MFA code
$mfa_code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$_SESSION['mfa_code'] = $mfa_code;

// Redirect to MFA verification (standalone mfa.php)
header("Location: ../mfa.php?lang=$lang&sent=1");
exit;
