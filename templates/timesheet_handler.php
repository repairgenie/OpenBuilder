<?php
// templates/timesheet_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';
require_once __DIR__ . '/../src/GPSEngine.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=timesheets&lang=$lang";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $base");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: $base");
    exit;
}

$action = $_POST['action'] ?? '';
$pdo = Database::connect();

switch ($action) {
    case 'create_timesheet':
        $worker_name = trim($_POST['worker_name'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $hours = (float)($_POST['hours'] ?? 0);
        $date = trim($_POST['date'] ?? '');
        $cost_code_id = !empty($_POST['cost_code_id']) ? (int)$_POST['cost_code_id'] : null;
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $gps_stamp = GPSEngine::formatStamp($latitude, $longitude);
        $created_by = $_SESSION['user_id'];

        if (!$worker_name || !$trade_en || !$trade_es || !$hours || !$date) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Todos los campos requeridos deben completarse.' : 'All required fields must be completed.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO timesheets (worker_name, trade_en, trade_es, hours, date, cost_code_id, latitude, longitude, gps_stamp, created_by, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Draft')");
            $stmt->execute([$worker_name, $trade_en, $trade_es, $hours, $date, $cost_code_id, $latitude, $longitude, $gps_stamp, $created_by]);

            $user_name = $_SESSION['user_name'] ?? 'System';
            log_activity("Created timesheet for $worker_name", "Creó parte de horas para $worker_name", 'timesheets', $pdo->lastInsertId());

            $_SESSION['flash_success'] = $lang === 'es' ? 'Parte creado exitosamente.' : 'Timesheet created successfully.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'update_timesheet':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM timesheets WHERE id=?");
        $stmt->execute([$id]);
        $ts = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ts || ($ts['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $worker_name = trim($_POST['worker_name'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $hours = (float)($_POST['hours'] ?? 0);
        $date = trim($_POST['date'] ?? '');
        $cost_code_id = !empty($_POST['cost_code_id']) ? (int)$_POST['cost_code_id'] : null;
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $gps_stamp = GPSEngine::formatStamp($latitude, $longitude);

        if (!$id || !$worker_name || !$trade_en || !$trade_es || !$hours || !$date) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE timesheets SET worker_name=?, trade_en=?, trade_es=?, hours=?, date=?, cost_code_id=?, latitude=?, longitude=?, gps_stamp=? WHERE id=?");
            $stmt->execute([$worker_name, $trade_en, $trade_es, $hours, $date, $cost_code_id, $latitude, $longitude, $gps_stamp, $id]);

            $user_name = $_SESSION['user_name'] ?? 'System';
            log_activity("Updated timesheet for $worker_name", "Actualizó parte de horas para $worker_name", 'timesheets', $id);

            $_SESSION['flash_success'] = $lang === 'es' ? 'Parte actualizado.' : 'Timesheet updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'delete_timesheet':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM timesheets WHERE id=?");
        $stmt->execute([$id]);
        $timesheet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$timesheet || ($timesheet['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $pdo->prepare("DELETE FROM timesheets WHERE id=?")->execute([$id]);
        log_activity("Deleted timesheet", "Eliminó parte de horas", 'timesheets', $id);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Parte eliminado.' : 'Timesheet deleted.';
        header("Location: $base");
        exit;

    case 'approve_timesheet':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        // Role check: only Manager or Admin can approve timesheets
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin role required.';
            header("Location: $base");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM timesheets WHERE id=?");
        $stmt->execute([$id]);
        $ts = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ts) {
            $_SESSION['flash_error'] = 'Timesheet not found.';
            header("Location: $base");
            exit;
        }
        $pdo->prepare("UPDATE timesheets SET status='Approved' WHERE id=?")->execute([$id]);
        log_activity("Approved timesheet", "Aprobó parte de horas", 'timesheets', $id);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Parte aprobado.' : 'Timesheet approved.';
        header("Location: $base");
        exit;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
        header("Location: $base");
        exit;
}
