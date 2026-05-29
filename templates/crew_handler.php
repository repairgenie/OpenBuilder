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
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $base");
            exit;
        }
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
            $stmt = $pdo->prepare("INSERT INTO crews (name, trade_en, trade_es, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
            $stmt->execute([$name, $trade_en, $trade_es, $status, $_SESSION['user_id']]);
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

        $stmt = $pdo->prepare("SELECT * FROM crews WHERE id=?");
        $stmt->execute([$id]);
        $crew = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$crew || !can_modify($crew['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
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
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM crews WHERE id=?");
        $stmt->execute([$id]);
        $crew = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$crew || !can_modify($crew['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $crew_name = $crew['name'] ?? "ID:$id";
        // Delete crew members first
        $pdo->prepare("DELETE FROM crew_members WHERE crew_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM crews WHERE id=?")->execute([$id]);

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Deleted crew: $crew_name", "Eliminó cuadrilla: $crew_name", $id, 'crews');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Cuadrilla eliminada.' : 'Crew deleted.';
        header("Location: $base");
        exit;

    case 'add_member':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $base");
            exit;
        }
        $crew_id = (int)($_POST['crew_id'] ?? 0);
        $member_name = trim($_POST['member_name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$crew_id || !$member_name) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Cuadrilla y nombre son requeridos.' : 'Crew and name are required.';
            header("Location: $base");
            exit;
        }

        // Validate crew ownership
        $stmt = $pdo->prepare("SELECT * FROM crews WHERE id=?");
        $stmt->execute([$crew_id]);
        $crew = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$crew || !can_modify($crew['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
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
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $base");
            exit;
        }
        $member_id = (int)($_POST['member_id'] ?? 0);
        if ($member_id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        // Validate member belongs to owned crew
        $stmt = $pdo->prepare("SELECT cm.*, c.created_by as crew_owner FROM crew_members cm JOIN crews c ON cm.crew_id = c.id WHERE cm.id=?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$member || !can_modify($member['crew_owner'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $pdo->prepare("DELETE FROM crew_members WHERE id=?")->execute([$member_id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Miembro eliminado.' : 'Member removed.';
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}