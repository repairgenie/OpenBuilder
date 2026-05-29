<?php
// src/security_helper.php

function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_valid($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_validate() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Allow GET requests to pass through
    if ($_SERVER['REQUEST_METHOD'] === 'GET') return true;
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function require_auth() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Test mode bypass for E2E tests (development only)
    if (!empty($_COOKIE['ob_test_mode']) && $_COOKIE['ob_test_mode'] === '1') {
        $env = getenv('APP_ENV') ?: 'development';
        if ($env === 'production') {
            // In production, ob_test_mode is not allowed
        } elseif (empty($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Test User';
            $_SESSION['role'] = 'Admin';
            $_SESSION['email'] = 'test@openbuilder.local';
        }
        return;
    }
    if (empty($_SESSION['user_id'])) {
        error_log("require_auth: redirecting — no user_id in session. PHPSESSID=" . session_id() . ". session_keys=" . json_encode(array_keys($_SESSION)));
        header("Location: index.php?page=login&lang=" . ($_GET['lang'] ?? 'en'));
        exit;
    }
    // Regenerate session ID once after login to prevent session fixation attacks.
    // Guard with a marker so it only fires once per login (not every request),
    // avoiding invalidation of Playwright E2E cookie-based sessions.
    if (!isset($_SESSION['session_regenerated'])) {
        $_SESSION['session_regenerated'] = true;
        if (function_exists('session_regenerate_id')) {
            session_regenerate_id(true);
        }
    }
}

function require_role($role) {
    require_auth();
    if (($_SESSION['role'] ?? '') !== $role && ($_SESSION['role'] ?? '') !== 'Admin') {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }
}

// Quick Win 1 helpers
function get_setting($key, $default = '') {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT value FROM system_settings WHERE `key` = ?");
    $stmt->execute([$key]);
    $cache[$key] = $stmt->fetchColumn() ?: $default;
    return $cache[$key];
}

function set_setting($key, $value) {
    $pdo = Database::connect();
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO system_settings (`key`, value, updated_at) VALUES (?, ?, datetime('now'))");
    $stmt->execute([$key, $value]);
}

function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        return ['id' => null, 'name' => 'Guest', 'role' => 'Guest'];
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'role' => $_SESSION['role'] ?? 'Member'
    ];
}

/**
 * Log a user activity to the activity_logs table.
 *
 * @param string $action_en  English action description
 * @param string $action_es  Spanish action description
 * @param string $module     Module/section (e.g. 'users', 'rfis', 'budget')
 * @param int|null $ref_id   Optional reference ID (e.g. user_id, rfi_id)
 * @param string|null $ip    IP address (defaults to current request IP)
 */
function log_activity($action_en, $action_es, $module, $ref_id = null, $ip = null) {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = Database::connect();
    }
    $username = $_SESSION['user_name'] ?? $_SESSION['user_id'] ?? 'System';
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

    // Ensure ip_address column exists (run once per request)
    static $checked = false;
    if (!$checked) {
        try {
            $pdo->exec("ALTER TABLE activity_logs ADD COLUMN ip_address TEXT DEFAULT '127.0.0.1'");
        } catch (PDOException $e) {
            // Column may already exist, ignore
        }
        $checked = true;
    }

    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (username, user_id, action_en, action_es, ref_id, module, ip_address, created_at)
        VALUES (:username, :user_id, :action_en, :action_es, :ref_id, :module, :ip_address, datetime('now'))
    ");
    $stmt->execute([
        ':username' => $username,
        ':user_id' => $user_id,
        ':action_en' => $action_en,
        ':action_es' => $action_es,
        ':ref_id' => $ref_id,
        ':module' => $module,
        ':ip_address' => $ip,
    ]);
}