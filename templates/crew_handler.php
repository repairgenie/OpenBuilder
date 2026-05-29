<?php
// templates/crew_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=crew_management&lang=$lang";

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

switch ($action) {
    case 'create_crew':
        $name = trim($_POST['name'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $status = trim($_POST['status'] ?? 'On Site');

        if (!$name || !$trade_en) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Nombre y oficio son requeridos.' : 'Name and trade are required.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO crews (name, trade_en, trade_es, status, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
            $stmt->execute([$name, $trade_en, $trade_es, $status]);
            $new_id = $pdo->lastInsertId();

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Created crew: $name", "Creó cuadrilla: $name", (int)$new_id, 'crews');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Cuadrilla creada.' : 'Crew created.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'update_crew':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $status = trim($_POST['status'] ?? 'On Site');

        if (!$id || !$name || !$trade_en) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE crews SET name=?, trade_en=?, trade_es=?, status=? WHERE id=?");
            $stmt->execute([$name, $trade_en, $trade_es, $status, $id]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Updated crew: $name", "Actualizó cuadrilla: $name", $id, 'crews');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Cuadrilla actualizada.' : 'Crew updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'delete_crew':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Delete crew members first
            $pdo->prepare("DELETE FROM crew_members WHERE crew_id=?")->execute([$id]);
            
            $stmt = $pdo->prepare("SELECT name FROM crews WHERE id=?");
            $stmt->execute([$id]);
            $crew_name = $stmt->fetchColumn() ?: "ID:$id";

            $pdo->prepare("DELETE FROM crews WHERE id=?")->execute([$id]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Deleted crew: $crew_name", "Eliminó cuadrilla: $crew_name", $id, 'crews');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Cuadrilla eliminada.' : 'Crew deleted.';
        }
        header("Location: $base");
        exit;

    case 'add_member':
        $crew_id = (int)($_POST['crew_id'] ?? 0);
        $member_name = trim($_POST['member_name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$crew_id || !$member_name) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Cuadrilla y nombre son requeridos.' : 'Crew and name are required.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO crew_members (crew_id, name, role, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$crew_id, $member_name, $role, $phone]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Added member $member_name to crew", "Añadió miembro $member_name a cuadrilla", $crew_id, 'crews');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Miembro añadido.' : 'Member added.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'remove_member':
        $member_id = (int)($_POST['member_id'] ?? 0);
        if ($member_id > 0) {
            $pdo->prepare("DELETE FROM crew_members WHERE id=?")->execute([$member_id]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Miembro eliminado.' : 'Member removed.';
        }
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}