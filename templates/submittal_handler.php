<?php
// templates/submittal_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=submittals&lang=$lang";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $base");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token inválido.' : 'Invalid token.';
    header("Location: $base");
    exit;
}

$action = $_POST['action'] ?? '';
$pdo = Database::connect();

switch ($action) {
    case 'create_submittal':
        $title_en = trim($_POST['title_en'] ?? '');
        $title_es = trim($_POST['title_es'] ?? '');
        $spec_section = trim($_POST['spec_section'] ?? '');
        $ball_in_court = (int)($_POST['ball_in_court'] ?? 0) ?: null;
        $due_date = trim($_POST['due_date'] ?? '');
        $status = 'Draft';

        if (!$title_en || !$spec_section) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Título y sección de especificación son requeridos.' : 'Title and spec section are required.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO submittals (title_en, title_es, spec_section, status, ball_in_court, due_date, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'))");
            $stmt->execute([$title_en, $title_es, $spec_section, $status, $ball_in_court, $due_date, $_SESSION['user_id']]);
            $new_id = $pdo->lastInsertId();

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Created submittal: $title_en", "Creó submittal: $title_en", (int)$new_id, 'submittals');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Submittal creado.' : 'Submittal created.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'update_submittal':
        $id = (int)($_POST['id'] ?? 0);
        $title_en = trim($_POST['title_en'] ?? '');
        $title_es = trim($_POST['title_es'] ?? '');
        $spec_section = trim($_POST['spec_section'] ?? '');
        $ball_in_court = (int)($_POST['ball_in_court'] ?? 0) ?: null;
        $due_date = trim($_POST['due_date'] ?? '');
        $status = trim($_POST['status'] ?? 'Draft');

        if (!$id || !$title_en || !$spec_section) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM submittals WHERE id=?");
        $stmt->execute([$id]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sub || ($sub['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE submittals SET title_en=?, title_es=?, spec_section=?, status=?, ball_in_court=?, due_date=? WHERE id=?");
            $stmt->execute([$title_en, $title_es, $spec_section, $status, $ball_in_court, $due_date, $id]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Updated submittal: $title_en", "Actualizó submittal: $title_en", $id, 'submittals');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Submittal actualizado.' : 'Submittal updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'delete_submittal':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM submittals WHERE id=?");
        $stmt->execute([$id]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sub || ($sub['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $title = $sub['title_en'] ?? "ID:$id";
        $pdo->prepare("DELETE FROM submittals WHERE id=?")->execute([$id]);

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Deleted submittal: $title", "Eliminó submittal: $title", $id, 'submittals');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Submittal eliminado.' : 'Submittal deleted.';
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}