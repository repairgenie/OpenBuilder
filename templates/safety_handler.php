<?php
// templates/safety_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';
require_once __DIR__ . '/../src/GPSEngine.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_POST['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=safety_hazards&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=safety_hazards&lang=$lang";

switch ($action) {
    case 'create_hazard':
        $image_path = null;
        if (!empty($_FILES['hazard_image']['tmp_name'])) {
            $upload_dir = __DIR__ . '/../uploads/safety/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            // Validate file type
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['hazard_image']['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowed)) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'Tipo de archivo no permitido.' : 'File type not allowed.';
                header($redirect);
                exit;
            }
            // Validate file size (max 5MB)
            if ($_FILES['hazard_image']['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El archivo es demasiado grande (máx 5MB).' : 'File too large (max 5MB).';
                header($redirect);
                exit;
            }
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['hazard_image']['name']));
            $image_path = 'uploads/safety/' . $filename;
            $dest = __DIR__ . '/../' . $image_path;
            move_uploaded_file($_FILES['hazard_image']['tmp_name'], $dest);
        }

        try {
            $lat = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
            $lon = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
            $gps_stamp = GPSEngine::formatStamp($lat, $lon);
            $stmt = $pdo->prepare("
                INSERT INTO safety_hazards 
                (description, location, severity, reported_date, reported_by, reported_by_user, assigned_crew_id, corrective_action, status, image_path, latitude, longitude, gps_stamp, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
            ");
            $stmt->execute([
                trim($_POST['description']),
                trim($_POST['location']),
                $_POST['severity'],
                $_POST['reported_date'],
                (int)$_POST['reported_by'],
                $_SESSION['user_id'],
                !empty($_POST['assigned_crew_id']) ? (int)$_POST['assigned_crew_id'] : null,
                trim($_POST['corrective_action'] ?? ''),
                $_POST['status'] ?? 'Open',
                $image_path,
                $lat,
                $lon,
                $gps_stamp
            ]);

            $hazard_id = $pdo->lastInsertId();
            $user_name = $_SESSION['user_name'] ?? 'System';
            log_activity("Reported hazard: " . trim($_POST['description']), "Reportó peligro: " . trim($_POST['description']), 'safety_hazards', $hazard_id);

            $_SESSION['flash_success'] = $lang === 'es' ? 'Peligro reportado exitosamente.' : 'Hazard reported successfully.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Error al guardar: ' . $e->getMessage() : 'Error saving: ' . $e->getMessage();
        }
        break;

    case 'update_hazard':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'ID de peligro inválido.' : 'Invalid hazard ID.';
            header($redirect);
            exit;
        }

        // Ownership check
        $stmt = $pdo->prepare("SELECT * FROM safety_hazards WHERE id=?");
        $stmt->execute([$id]);
        $hazard = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$hazard || ($hazard['reported_by_user'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }

        // Handle image update if new file uploaded
        $image_path = !empty($_POST['existing_image_path']) ? $_POST['existing_image_path'] : null;
        if (!empty($_FILES['hazard_image']['tmp_name'])) {
            $upload_dir = __DIR__ . '/../uploads/safety/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            // Validate file type
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['hazard_image']['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowed)) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'Tipo de archivo no permitido.' : 'File type not allowed.';
                header($redirect);
                exit;
            }
            // Validate file size (max 5MB)
            if ($_FILES['hazard_image']['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El archivo es demasiado grande (máx 5MB).' : 'File too large (max 5MB).';
                header($redirect);
                exit;
            }
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['hazard_image']['name']));
            $image_path = 'uploads/safety/' . $filename;
            $dest = __DIR__ . '/../' . $image_path;
            move_uploaded_file($_FILES['hazard_image']['tmp_name'], $dest);
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE safety_hazards 
                SET description=?, location=?, severity=?, reported_date=?, assigned_crew_id=?, corrective_action=?, status=?, image_path=?, latitude=?, longitude=?
                WHERE id=?
            ");
            $stmt->execute([
                trim($_POST['description']),
                trim($_POST['location']),
                $_POST['severity'],
                $_POST['reported_date'],
                !empty($_POST['assigned_crew_id']) ? (int)$_POST['assigned_crew_id'] : null,
                trim($_POST['corrective_action'] ?? ''),
                $_POST['status'] ?? 'Open',
                $image_path,
                !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
                !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
                $id
            ]);

            $user_name = $_SESSION['user_name'] ?? 'System';
            log_activity("Updated hazard: " . trim($_POST['description']), "Actualizó peligro: " . trim($_POST['description']), 'safety_hazards', $id);

            $_SESSION['flash_success'] = $lang === 'es' ? 'Peligro actualizado.' : 'Hazard updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Error al actualizar: ' . $e->getMessage() : 'Error updating: ' . $e->getMessage();
        }
        break;

    case 'delete_hazard':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'ID de peligro inválido.' : 'Invalid hazard ID.';
            header($redirect);
            exit;
        }

        // Ownership check
        $stmt = $pdo->prepare("SELECT * FROM safety_hazards WHERE id=?");
        $stmt->execute([$id]);
        $hazard = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$hazard || ($hazard['reported_by_user'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }

        // Get image path before deleting
        $image_path = $hazard['image_path'] ?? null;

        // Delete the record
        $pdo->prepare("DELETE FROM safety_hazards WHERE id=?")->execute([$id]);

        // Delete image file if exists
        if (!empty($image_path)) {
            $img_file = __DIR__ . '/../' . $image_path;
            if (file_exists($img_file)) {
                unlink($img_file);
            }
        }

        $user_name = $_SESSION['user_name'] ?? 'System';
        log_activity("Deleted hazard ID: $id", "Eliminó peligro ID: $id", 'safety_hazards', $id);

        $_SESSION['flash_success'] = $lang === 'es' ? 'Peligro eliminado.' : 'Hazard deleted.';
        break;

    case 'close_hazard':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'ID de peligro inválido.' : 'Invalid hazard ID.';
            header($redirect);
            exit;
        }

        // Ownership check
        $stmt = $pdo->prepare("SELECT * FROM safety_hazards WHERE id=?");
        $stmt->execute([$id]);
        $hazard = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$hazard || ($hazard['reported_by_user'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }

        $pdo->prepare("UPDATE safety_hazards SET status='Closed' WHERE id=?")->execute([$id]);

        $user_name = $_SESSION['user_name'] ?? 'System';
        log_activity("Closed hazard ID: $id", "Cerró peligro ID: $id", 'safety_hazards', $id);

        $_SESSION['flash_success'] = $lang === 'es' ? 'Peligro cerrado.' : 'Hazard closed.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;