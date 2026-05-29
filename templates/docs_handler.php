<?php
session_start();
require_once __DIR__ . '/../src/security_helper.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/PermissionHelper.php';
require_once __DIR__ . '/../src/Storage.php';

require_auth();

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF validation failed']);
    exit;
}

$pdo = Database::connect();
$perm = new PermissionHelper($pdo);
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'upload': {
        $title = trim($_POST['title'] ?? '');
        $doc_type = trim($_POST['doc_type'] ?? '');
        $discipline = trim($_POST['discipline'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $lang = $_SESSION['lang'] ?? 'en';

        if (empty($title) || empty($doc_type)) {
            echo json_encode(['success' => false, 'error' => 'Title and type are required']);
            exit;
        }

        // Insert doc record - use bilingual title
        $title_key = $lang === 'es' ? 'title_es' : 'title_en';
        $stmt = $pdo->prepare("INSERT INTO closeout_documents (title_en, title_es, type, status, file_path, notes, created_at) VALUES (?, ?, ?, 'Active', '', ?, datetime('now'))");
        $stmt->execute([$title, $title, $doc_type, $notes]);
        $doc_id = $pdo->lastInsertId();

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $revision = 'A';
            $file = $_FILES['file'];
            $stored = Storage::store($file['tmp_name'], $file['name'], 'documents');
            if ($stored) {
                $stmt2 = $pdo->prepare("INSERT INTO doc_versions (doc_id, revision, file_path, file_size, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
                $stmt2->execute([$doc_id, $revision, $stored['path'], $file['size'], $_SESSION['user_id'] ?? null]);
            }
        }

        echo json_encode(['success' => true, 'doc_id' => $doc_id]);
        break;
    }

    case 'new_revision': {
        $doc_id = intval($_POST['doc_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        // Get current highest revision
        $last = $pdo->prepare("SELECT revision FROM doc_versions WHERE doc_id = ? ORDER BY id DESC LIMIT 1");
        $last->execute([$doc_id]);
        $last_rev = $last->fetchColumn() ?: '@';
        $new_rev = chr(ord($last_rev[0]) + 1);

        if (!empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            $stored = Storage::store($file['tmp_name'], $file['name'], 'documents');
            if ($stored) {
                $stmt = $pdo->prepare("INSERT INTO doc_versions (doc_id, revision, file_path, file_size, uploaded_by, created_at, notes) VALUES (?, ?, ?, ?, ?, datetime('now'), ?)");
                $stmt->execute([$doc_id, $new_rev, $stored['path'], $file['size'], $_SESSION['user_id'] ?? null, $notes]);
            }
        }

        echo json_encode(['success' => true, 'revision' => $new_rev]);
        break;
    }

    case 'check_out': {
        $doc_id = intval($_POST['doc_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE closeout_documents SET checked_out_by = ?, checked_out_at = datetime('now') WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'] ?? 'guest', $doc_id]);
        echo json_encode(['success' => true]);
        break;
    }

    case 'check_in': {
        $doc_id = intval($_POST['doc_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE closeout_documents SET checked_out_by = NULL, checked_out_at = NULL WHERE id = ?");
        $stmt->execute([$doc_id]);
        echo json_encode(['success' => true]);
        break;
    }

    case 'delete': {
        $doc_id = intval($_POST['doc_id'] ?? 0);
        $perm->requirePermission('delete_docs');
        // Remove versions and links
        $pdo->prepare("DELETE FROM doc_versions WHERE doc_id = ?")->execute([$doc_id]);
        $pdo->prepare("DELETE FROM doc_links WHERE doc_id = ?")->execute([$doc_id]);
        $pdo->prepare("DELETE FROM closeout_documents WHERE id = ?")->execute([$doc_id]);
        echo json_encode(['success' => true]);
        break;
    }

    case 'link': {
        $doc_id = intval($_POST['doc_id'] ?? 0);
        $link_type = trim($_POST['link_type'] ?? '');
        $link_id = intval($_POST['link_id'] ?? 0);
        $stmt = $pdo->prepare("INSERT INTO doc_links (doc_id, link_type, link_id, created_at) VALUES (?, ?, ?, datetime('now'))");
        $stmt->execute([$doc_id, $link_type, $link_id]);
        echo json_encode(['success' => true]);
        break;
    }

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}