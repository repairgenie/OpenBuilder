<?php
// templates/tasks_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_POST['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

require_auth();

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
            $_POST['created_by'] ?? null
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea creada exitosamente.' : 'Task created successfully.';
        break;

    case 'update_task':
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
            $_POST['id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea actualizada.' : 'Task updated.';
        break;

    case 'delete_task':
        $pdo->prepare("DELETE FROM tasks WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Tarea eliminada.' : 'Task deleted.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;