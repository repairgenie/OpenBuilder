<?php
// templates/equipment_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_POST['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

require_auth();

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=equipment&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=equipment&lang=$lang";

switch ($action) {
    case 'create_equipment':
        if (!has_role(['Manager', 'Admin'])) {
            $_SESSION['flash_error'] = 'Access denied. Manager or Admin required.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO equipment (asset_tag, name, type, status, assigned_project, assigned_crew_id, last_service_date, next_service_date, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['asset_tag'],
            $_POST['name'],
            $_POST['type'],
            $_POST['status'] ?: 'active',
            $_POST['assigned_project'] ?: null,
            $_POST['assigned_crew_id'] ?: null,
            $_POST['last_service_date'] ?: null,
            $_POST['next_service_date'] ?: null,
            $_POST['notes'] ?: '',
            $_SESSION['user_id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Equipo agregado exitosamente.' : 'Equipment added successfully.';
        break;

    case 'update_equipment':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM equipment WHERE id=?");
        $stmt->execute([$id]);
        $eq = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$eq || !can_modify($eq['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE equipment SET asset_tag=?, name=?, type=?, status=?, assigned_project=?, assigned_crew_id=?, last_service_date=?, next_service_date=?, notes=? WHERE id=?");
        $stmt->execute([
            $_POST['asset_tag'],
            $_POST['name'],
            $_POST['type'],
            $_POST['status'] ?: 'active',
            $_POST['assigned_project'] ?: null,
            $_POST['assigned_crew_id'] ?: null,
            $_POST['last_service_date'] ?: null,
            $_POST['next_service_date'] ?: null,
            $_POST['notes'] ?: '',
            $id
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Equipo actualizado.' : 'Equipment updated.';
        break;

    case 'retire_equipment':
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
        $stmt = $pdo->prepare("SELECT * FROM equipment WHERE id=?");
        $stmt->execute([$id]);
        $eq = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$eq || !can_modify($eq['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $pdo->prepare("UPDATE equipment SET status='retired' WHERE id=?")->execute([$id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Equipo retirado.' : 'Equipment retired.';
        break;

    case 'add_service_log':
        $equipment_id = (int)($_POST['equipment_id'] ?? 0);
        if ($equipment_id <= 0) {
            $_SESSION['flash_error'] = 'Invalid equipment ID.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM equipment WHERE id=?");
        $stmt->execute([$equipment_id]);
        $eq = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$eq || !can_modify($eq['created_by'], ['Manager'])) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO equipment_service_log (equipment_id, service_date, description, cost, performed_by, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $equipment_id,
            $_POST['service_date'],
            $_POST['description'],
            $_POST['cost'] ?: null,
            $_POST['performed_by'] ?: ''
        ]);
        // Update last_service_date on the equipment record
        $pdo->prepare("UPDATE equipment SET last_service_date=? WHERE id=?")->execute([$_POST['service_date'], $equipment_id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Registro de servicio agregado.' : 'Service log added.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;