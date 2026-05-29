<?php
// templates/tasks_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=tasks&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=tasks&lang=$lang";

switch ($action) {
    case 'create_task':
        if (!has_role(['Worker', 'Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO tasks (task_name, start_date, end_date, assigned_crew_id, cost_code_id, status, is_critical, predecessor_task_id, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['task_name'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['assigned_crew_id'] ?: null,
            $_POST['cost_code_id'] ?: null,
            $_POST['status'] ?: 'Not Started',
            $_POST['is_critical'] ?? 0,
            $_POST['predecessor_task_id'] ?: null,
            $_SESSION['user_id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea creada exitosamente.' : 'Task created successfully.';
        break;

    case 'update_task':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id=?");
        $stmt->execute([$id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task || !can_modify($task['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE tasks SET task_name=?, start_date=?, end_date=?, assigned_crew_id=?, cost_code_id=?, status=?, is_critical=?, predecessor_task_id=? WHERE id=?");
        $stmt->execute([
            $_POST['task_name'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['assigned_crew_id'] ?: null,
            $_POST['cost_code_id'] ?: null,
            $_POST['status'] ?: 'Not Started',
            $_POST['is_critical'] ?? 0,
            $_POST['predecessor_task_id'] ?: null,
            $id
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea actualizada.' : 'Task updated.';
        break;

    case 'delete_task':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header($redirect);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id=?");
        $stmt->execute([$id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task || !can_modify($task['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $pdo->prepare("DELETE FROM tasks WHERE id=?")->execute([$id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea eliminada.' : 'Task deleted.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;