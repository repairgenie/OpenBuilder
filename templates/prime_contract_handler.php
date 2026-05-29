<?php
// templates/prime_contract_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=prime_contracts&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=prime_contracts&lang=$lang";

switch ($action) {
    case 'create_contract':
        $co_value = floatval($_POST['change_order_value'] ?? 0);
        $contract_value = floatval($_POST['contract_value'] ?? 0);
        $revised = $contract_value + $co_value;
        $stmt = $pdo->prepare("INSERT INTO prime_contracts (contract_number, contractor_name, contract_value, start_date, end_date, status, change_order_value, revised_contract_value, retention_percent, billing_frequency, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['contract_number'],
            $_POST['contractor_name'],
            $contract_value,
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['status'] ?: 'Pending',
            $co_value,
            $revised,
            floatval($_POST['retention_percent'] ?? 0),
            $_POST['billing_frequency'] ?: 'Monthly',
            $_POST['notes'] ?: ''
        ]);

        // Create initial version record
        $contract_id = $pdo->lastInsertId();
        $version_stmt = $pdo->prepare("INSERT INTO prime_contract_versions (contract_id, version_number, contract_value, change_order_value, revised_contract_value, status, notes, created_at, created_by) VALUES (?, 1, ?, ?, ?, ?, ?, datetime('now'), ?)");
        $version_stmt->execute([
            $contract_id,
            $contract_value,
            $co_value,
            $revised,
            $_POST['status'] ?: 'Pending',
            $_POST['notes'] ?: '',
            $_SESSION['user_id'] ?? null
        ]);

        $_SESSION['flash_success'] = $lang === 'es' ? 'Contrato creado exitosamente.' : 'Contract created successfully.';
        break;

    case 'update_contract':
        $co_value = floatval($_POST['change_order_value'] ?? 0);
        $contract_value = floatval($_POST['contract_value'] ?? 0);
        $revised = $contract_value + $co_value;

        // Get current version number
        $ver_stmt = $pdo->prepare("SELECT MAX(version_number) FROM prime_contract_versions WHERE contract_id = ?");
        $ver_stmt->execute([$_POST['id']]);
        $last_version = (int)$ver_stmt->fetchColumn();
        $new_version = $last_version + 1;

        $stmt = $pdo->prepare("UPDATE prime_contracts SET contract_number=?, contractor_name=?, contract_value=?, start_date=?, end_date=?, status=?, change_order_value=?, revised_contract_value=?, retention_percent=?, billing_frequency=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['contract_number'],
            $_POST['contractor_name'],
            $contract_value,
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['status'] ?: 'Pending',
            $co_value,
            $revised,
            floatval($_POST['retention_percent'] ?? 0),
            $_POST['billing_frequency'] ?: 'Monthly',
            $_POST['notes'] ?: '',
            $_POST['id']
        ]);

        // Create new version record
        $version_stmt = $pdo->prepare("INSERT INTO prime_contract_versions (contract_id, version_number, contract_value, change_order_value, revised_contract_value, status, notes, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'), ?)");
        $version_stmt->execute([
            $_POST['id'],
            $new_version,
            $contract_value,
            $co_value,
            $revised,
            $_POST['status'] ?: 'Pending',
            $_POST['notes'] ?: '',
            $_SESSION['user_id'] ?? null
        ]);

        $_SESSION['flash_success'] = $lang === 'es' ? 'Contrato actualizado.' : 'Contract updated.';
        break;

    case 'new_version':
        $contract_id = intval($_POST['id'] ?? 0);
        if ($contract_id === 0) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'ID de contrato inválido.' : 'Invalid contract ID.';
            header($redirect);
            exit;
        }

        $co_value = floatval($_POST['change_order_value'] ?? 0);
        $contract_value = floatval($_POST['contract_value'] ?? 0);
        $revised = $contract_value + $co_value;

        // Get current version number
        $ver_stmt = $pdo->prepare("SELECT MAX(version_number) FROM prime_contract_versions WHERE contract_id = ?");
        $ver_stmt->execute([$contract_id]);
        $last_version = (int)$ver_stmt->fetchColumn();
        $new_version = $last_version + 1;

        // Update main contract record
        $stmt = $pdo->prepare("UPDATE prime_contracts SET contract_value=?, change_order_value=?, revised_contract_value=?, status=?, notes=? WHERE id=?");
        $stmt->execute([
            $contract_value,
            $co_value,
            $revised,
            $_POST['status'] ?: 'Pending',
            $_POST['notes'] ?: '',
            $contract_id
        ]);

        // Create new version record
        $version_stmt = $pdo->prepare("INSERT INTO prime_contract_versions (contract_id, version_number, contract_value, change_order_value, revised_contract_value, status, notes, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'), ?)");
        $version_stmt->execute([
            $contract_id,
            $new_version,
            $contract_value,
            $co_value,
            $revised,
            $_POST['status'] ?: 'Pending',
            $_POST['notes'] ?: '',
            $_SESSION['user_id'] ?? null
        ]);

        $_SESSION['flash_success'] = $lang === 'es' ? 'Nueva versión creada.' : 'New version created.';
        break;

    case 'delete_contract':
        $pdo->prepare("DELETE FROM prime_contract_versions WHERE contract_id = ?")->execute([$_POST['id']]);
        $pdo->prepare("DELETE FROM prime_contracts WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Contrato eliminado.' : 'Contract deleted.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;