<?php
// templates/change_order_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=change_orders&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=change_orders&lang=$lang";

switch ($action) {
    case 'create_change_order':
        $stmt = $pdo->prepare("INSERT INTO change_orders (type, amount, event_id, cost_code_id, status) VALUES (?, ?, ?, ?, 'Draft')");
        $stmt->execute([
            $_POST['type'],
            $_POST['amount'],
            $_POST['event_id'] ?: null,
            $_POST['cost_code_id'] ?: null
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Orden de cambio creada.' : 'Change order created.';
        break;

    case 'update_change_order':
        $stmt = $pdo->prepare("UPDATE change_orders SET type=?, amount=?, event_id=?, cost_code_id=?, status=? WHERE id=?");
        $stmt->execute([
            $_POST['type'],
            $_POST['amount'],
            $_POST['event_id'] ?: null,
            $_POST['cost_code_id'] ?: null,
            $_POST['status'],
            $_POST['id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Orden de cambio actualizada.' : 'Change order updated.';
        break;

    case 'delete_change_order':
        $pdo->prepare("DELETE FROM change_orders WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Orden de cambio eliminada.' : 'Change order deleted.';
        break;

    case 'commit_to_budget':
        // Mark as Issued and flag as committed
        $pdo->prepare("UPDATE change_orders SET status='Issued', budget_committed=1 WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'CO comprometidos al presupuesto.' : 'Change order committed to budget.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;