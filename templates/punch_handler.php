<?php
// templates/punch_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=punch_list_v2&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=punch_list_v2&lang=$lang";

switch ($action) {
    case 'create_punch':
        $stmt = $pdo->prepare("INSERT INTO punch_list_items (description, location, assigned_to, priority, due_date, created_by, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['description'],
            $_POST['location'] ?: '',
            $_POST['assigned_to'] ?: null,
            $_POST['priority'] ?: 'Medium',
            $_POST['due_date'] ?: null,
            $_POST['created_by'],
            $_POST['latitude'] ?: null,
            $_POST['longitude'] ?: null
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item creado.' : 'Punch item created.';
        break;

    case 'update_punch':
        $stmt = $pdo->prepare("UPDATE punch_list_items SET description=?, location=?, assigned_to=?, priority=?, due_date=?, latitude=?, longitude=? WHERE id=?");
        $stmt->execute([
            $_POST['description'],
            $_POST['location'] ?: '',
            $_POST['assigned_to'] ?: null,
            $_POST['priority'] ?: 'Medium',
            $_POST['due_date'] ?: null,
            $_POST['latitude'] ?: null,
            $_POST['longitude'] ?: null,
            $_POST['id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item actualizado.' : 'Punch item updated.';
        break;

    case 'verify_punch':
        $pdo->prepare("UPDATE punch_list_items SET status='Verified' WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item verificado.' : 'Punch item verified.';
        break;

    case 'batch_assign':
        $item_ids = explode(',', $_POST['item_ids'] ?? '');
        $item_ids = array_filter($item_ids, 'is_numeric');
        if (empty($item_ids)) break;
        $assigned_to = $_POST['assigned_to'];
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $stmt = $pdo->prepare("UPDATE punch_list_items SET assigned_to=? WHERE id IN ($placeholders)");
        $stmt->execute([$assigned_to, ...$item_ids]);
        $_SESSION['flash_success'] = count($item_ids) . ' ' . ($lang === 'es' ? 'items asignados.' : 'items assigned.');
        break;

    case 'batch_close':
        $item_ids = explode(',', $_POST['item_ids'] ?? '');
        $item_ids = array_filter($item_ids, 'is_numeric');
        if (empty($item_ids)) break;
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $pdo->prepare("UPDATE punch_list_items SET status='Closed' WHERE id IN ($placeholders)")->execute([...$item_ids]);
        $_SESSION['flash_success'] = count($item_ids) . ' ' . ($lang === 'es' ? 'items cerrados.' : 'items closed.');
        break;

    case 'export_punch_csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="punch_list_' . date('Y-m-d') . '.csv"');
        $items = $pdo->query("
            SELECT p.*, u.name as assignee_name
            FROM punch_list_items p
            LEFT JOIN users u ON p.assigned_to = u.id
            ORDER BY p.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['ID', 'Description', 'Location', 'Assigned To', 'Priority', 'Status', 'Due Date']);
        foreach ($items as $item) {
            fputcsv($fp, [
                $item['id'],
                $item['description'],
                $item['location'] ?: '',
                $item['assignee_name'] ?: '',
                $item['priority'] ?: '',
                $item['status'] ?: '',
                $item['due_date'] ?: ''
            ]);
        }
        fclose($fp);
        exit;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;