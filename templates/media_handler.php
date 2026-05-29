<?php
// templates/media_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';
require_once __DIR__ . '/../src/Storage.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
$lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=media&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? ($_FILES['media_file'] ?? null ? 'upload_media' : '');
$redirect = "Location: ../index.php?page=media&lang=$lang";

switch ($action) {
    case 'upload_media':
        if (empty($_FILES['media_file']['tmp_name'])) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'No se seleccionó archivo.' : 'No file selected.';
            header($redirect);
            exit;
        }
        $file = $_FILES['media_file'];
        $filename = basename($file['name']);
        $upload_dir = __DIR__ . '/../uploads/media/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $dest_path = $upload_dir . uniqid() . '_' . $safe_filename;
        move_uploaded_file($file['tmp_name'], $dest_path);

        $stmt = $pdo->prepare("INSERT INTO media (filename, title, project_id, cost_code_id, date_taken, tags, file_path, file_size, mime_type, uploaded_by, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $filename,
            $_POST['title'] ?: $filename,
            $_POST['project_id'] ?: null,
            $_POST['cost_code_id'] ?: null,
            $_POST['date_taken'] ?: date('Y-m-d'),
            $_POST['tags'] ?: '',
            $dest_path,
            $file['size'],
            $file['type'],
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Medio subido exitosamente.' : 'Media uploaded successfully.';
        break;

    case 'delete_media':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header($redirect);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM media WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || ($row['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header($redirect);
            exit;
        }
        if ($row && file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        $pdo->prepare("DELETE FROM media WHERE id=?")->execute([$id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Medio eliminado.' : 'Media deleted.';
        break;

    case 'link_media':
        $linked_id_raw = $_POST['linked_id_raw'] ?? '';
        if (strpos($linked_id_raw, ':') === false) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'ID de vínculo inválido.' : 'Invalid link ID.';
            header($redirect);
            exit;
        }
        list($linked_type, $linked_id) = explode(':', $linked_id_raw, 2);
        $stmt = $pdo->prepare("INSERT INTO media_links (media_id, linked_type, linked_id, created_at) VALUES (?, ?, ?, datetime('now'))");
        $stmt->execute([$_POST['media_id'], $linked_type, $linked_id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Medio vinculado.' : 'Media linked.';
        header($redirect);
        exit;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;