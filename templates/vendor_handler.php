<?php
// templates/vendor_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=vendor_portal&lang=$lang";

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
    case 'create_vendor':
        $company_name = trim($_POST['company_name'] ?? '');
        $contact_name = trim($_POST['contact_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);

        if (!$company_name || !$contact_name || !$email) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Empresa, contacto y email son requeridos.' : 'Company, contact and email are required.';
            header("Location: $base");
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Email inválido.' : 'Invalid email address.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO vendors (company_name, contact_name, email, trade_en, trade_es, rating, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$company_name, $contact_name, $email, $trade_en, $trade_es, $rating, $_SESSION['user_id']]);
            $new_id = $pdo->lastInsertId();

            // Log activity
            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Created vendor: $company_name", "Creó proveedor: $company_name", (int)$new_id, 'vendors');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Proveedor creado.' : 'Vendor created.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'update_vendor':
        $id = (int)($_POST['id'] ?? 0);
        $company_name = trim($_POST['company_name'] ?? '');
        $contact_name = trim($_POST['contact_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $trade_en = trim($_POST['trade_en'] ?? '');
        $trade_es = trim($_POST['trade_es'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);

        if (!$id || !$company_name || !$contact_name || !$email) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id=?");
        $stmt->execute([$id]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vendor || ($vendor['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE vendors SET company_name=?, contact_name=?, email=?, trade_en=?, trade_es=?, rating=? WHERE id=?");
            $stmt->execute([$company_name, $contact_name, $email, $trade_en, $trade_es, $rating, $id]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Updated vendor: $company_name", "Actualizó proveedor: $company_name", $id, 'vendors');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Proveedor actualizado.' : 'Vendor updated.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
        }
        header("Location: $base");
        exit;

    case 'delete_vendor':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header("Location: $base");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id=?");
        $stmt->execute([$id]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vendor || ($vendor['created_by'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'Admin')) {
            $_SESSION['flash_error'] = 'Access denied.';
            header("Location: $base");
            exit;
        }
        $vendor_name = $vendor['company_name'] ?? "ID:$id";
        $pdo->prepare("DELETE FROM vendors WHERE id=?")->execute([$id]);

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Deleted vendor: $vendor_name", "Eliminó proveedor: $vendor_name", $id, 'vendors');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Proveedor eliminado.' : 'Vendor deleted.';
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}