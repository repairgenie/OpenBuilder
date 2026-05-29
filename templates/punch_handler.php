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
        if (!has_role(['Worker', 'Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO punch_list_items (description, location, assigned_to, priority, due_date, created_by, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['description'],
            $_POST['location'] ?: '',
            $_POST['assigned_to'] ?: null,
            $_POST['priority'] ?: 'Medium',
            $_POST['due_date'] ?: null,
            $_SESSION['user_id'],
            $_POST['latitude'] ?: null,
            $_POST['longitude'] ?: null
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item creado.' : 'Punch item created.';
        break;

    case 'update_punch':
        $stmt = $pdo->prepare("SELECT * FROM punch_list_items WHERE id=?");
        $stmt->execute([$_POST['id']]);
        $punch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$punch || !can_modify($punch['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
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
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $redirect");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM punch_list_items WHERE id=?");
        $stmt->execute([$id]);
        $punch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$punch || !can_modify($punch['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $pdo->prepare("UPDATE punch_list_items SET status='Verified', verified_by=?, verified_at=datetime('now') WHERE id=?")
            ->execute([$_SESSION['user_id'], $id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item verificado.' : 'Punch item verified.';
        break;

    case 'delete_punch':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header("Location: $redirect");
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $redirect");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM punch_list_items WHERE id=?");
        $stmt->execute([$id]);
        $punch = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$punch || !can_modify($punch['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $pdo->prepare("DELETE FROM punch_list_items WHERE id=?")->execute([$id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Punch item eliminado.' : 'Punch item deleted.';
        break;

    case 'batch_assign':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
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
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        $item_ids = explode(',', $_POST['item_ids'] ?? '');
        $item_ids = array_filter($item_ids, 'is_numeric');
        if (empty($item_ids)) break;
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $pdo->prepare("UPDATE punch_list_items SET status='Closed' WHERE id IN ($placeholders)")->execute([...$item_ids]);
        $_SESSION['flash_success'] = count($item_ids) . ' ' . ($lang === 'es' ? 'items cerrados.' : 'items closed.');
        break;

    case 'export_punch_csv':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $redirect");
            exit;
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="punch_list_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel UTF-8
        fputcsv($output, ['ID', 'Description', 'Location', 'Assigned To', 'Priority', 'Due Date', 'Status', 'Created By', 'Verified By', 'Verified At']);
        $rows = $pdo->query("
            SELECT p.id, p.description, p.location, p.assigned_to, p.priority, p.due_date, p.status,
                   u1.name as created_by_name, u2.name as verified_by_name, p.verified_at
            FROM punch_list_items p
            LEFT JOIN users u1 ON p.created_by = u1.id
            LEFT JOIN users u2 ON p.verified_by = u2.id
            ORDER BY p.id
        ");
        while ($row = $rows->fetch(PDO::FETCH_NUM)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;