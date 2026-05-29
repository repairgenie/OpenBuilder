<?php
// templates/observations_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=observations&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=observations&lang=$lang";

switch ($action) {
    case 'create_observation':
        if (!has_role(['Worker', 'Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO observations (project_id, observer_id, observation_text, category, assigned_to, priority, status, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['project_id'] ?: 1,
            $_SESSION['user_id'],
            $_POST['observation_text'],
            $_POST['category'],
            $_POST['assigned_to'] ?: null,
            $_POST['priority'] ?: 'Medium',
            $_POST['status'] ?: 'Open',
            $_POST['latitude'] ?: null,
            $_POST['longitude'] ?: null
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Observación creada exitosamente.' : 'Observation created successfully.';
        break;

    case 'update_observation':
        $stmt = $pdo->prepare("SELECT * FROM observations WHERE id=?");
        $stmt->execute([$_POST['id']]);
        $obs = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$obs || ($obs['observer_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $stmt = $pdo->prepare("UPDATE observations SET project_id=?, observation_text=?, category=?, assigned_to=?, priority=?, status=?, latitude=?, longitude=? WHERE id=?");
        $stmt->execute([
            $_POST['project_id'] ?: $obs['project_id'],
            $_POST['observation_text'],
            $_POST['category'],
            $_POST['assigned_to'] ?: null,
            $_POST['priority'] ?: 'Medium',
            $_POST['status'] ?: 'Open',
            $_POST['latitude'] ?: null,
            $_POST['longitude'] ?: null,
            $_POST['id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Observación actualizada.' : 'Observation updated.';
        break;

    case 'delete_observation':
        $stmt = $pdo->prepare("SELECT * FROM observations WHERE id=?");
        $stmt->execute([$_POST['id']]);
        $obs = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$obs || ($obs['observer_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $pdo->prepare("DELETE FROM observations WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Observación eliminada.' : 'Observation deleted.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;