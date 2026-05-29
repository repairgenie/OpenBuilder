<?php
// templates/api_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=api_keys&lang=$lang";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $base");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token inválido.' : 'Invalid token.';
    header("Location: $base");
    exit;
}

$action = $_POST['action'] ?? '';
$pdo = Database::connect();

function generate_api_key() {
    return bin2hex(random_bytes(32));
}

switch ($action) {
    case 'create_api_key':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $base");
            exit;
        }
        $name = trim($_POST['name'] ?? '');
        $user_id = $_SESSION['user_id'];

        if (!$name || !$user_id) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Nombre y usuario son requeridos.' : 'Name and user are required.';
            header("Location: $base");
            exit;
        }

        $api_key = generate_api_key();
        try {
            $stmt = $pdo->prepare("INSERT INTO api_keys (user_id, name, api_key, created_at) VALUES (?, ?, ?, datetime('now'))");
            $stmt->execute([$user_id, $name, $api_key]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Clave API creada exitosamente.' : 'API key created successfully.';
            $_SESSION['new_api_key'] = $api_key;
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'delete_api_key':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $base");
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM api_keys WHERE id=?")->execute([$id]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Clave API eliminada.' : 'API key deleted.';
        }
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}