<?php
// templates/roles_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=roles&lang=$lang";

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

// Only allow CRUD on non-system roles (is_system = 0)
// System roles can only be viewed/edited by Admin
$current_user_role = $_SESSION['user_role'] ?? 'Viewer';
if ($current_user_role !== 'Admin') {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Sin permisos para modificar roles.' : 'Permission denied.';
    header("Location: $base");
    exit;
}

switch ($action) {
    case 'create_role':
        $role_name = trim($_POST['role_name'] ?? '');
        $description_en = trim($_POST['description_en'] ?? '');
        $description_es = trim($_POST['description_es'] ?? '');
        $permissions = $_POST['permissions'] ?? [];

        if (!$role_name) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'El nombre del rol es requerido.' : 'Role name is required.';
            header("Location: $base");
            exit;
        }

        $permissions_json = json_encode($permissions);
        try {
            $stmt = $pdo->prepare("INSERT INTO system_roles (role_name, permissions, description_en, description_es, is_system, created_at, updated_at) VALUES (?, ?, ?, ?, 0, datetime('now'), datetime('now'))");
            $stmt->execute([$role_name, $permissions_json, $description_en, $description_es]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Rol creado.' : 'Role created.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El nombre del rol ya existe.' : 'Role name already exists.';
            } else {
                $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
            }
        }
        header("Location: $base");
        exit;

    case 'update_role':
        $id = (int)($_POST['id'] ?? 0);
        $role_name = trim($_POST['role_name'] ?? '');
        $description_en = trim($_POST['description_en'] ?? '');
        $description_es = trim($_POST['description_es'] ?? '');
        $permissions = $_POST['permissions'] ?? [];

        if (!$id || !$role_name) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        // Cannot modify system roles
        $stmt = $pdo->prepare("SELECT is_system FROM system_roles WHERE id = ?");
        $stmt->execute([$id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$role) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Rol no encontrado.' : 'Role not found.';
            header("Location: $base");
            exit;
        }
        if ((int)$role['is_system'] === 1) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'No se pueden modificar roles del sistema.' : 'Cannot modify system roles.';
            header("Location: $base");
            exit;
        }

        $permissions_json = json_encode($permissions);
        try {
            $stmt = $pdo->prepare("UPDATE system_roles SET role_name=?, permissions=?, description_en=?, description_es=?, updated_at=datetime('now') WHERE id=?");
            $stmt->execute([$role_name, $permissions_json, $description_en, $description_es, $id]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Rol actualizado.' : 'Role updated.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El nombre del rol ya existe.' : 'Role name already exists.';
            } else {
                $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
            }
        }
        header("Location: $base");
        exit;

    case 'delete_role':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Cannot delete system roles
            $stmt = $pdo->prepare("SELECT is_system FROM system_roles WHERE id = ?");
            $stmt->execute([$id]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($role && (int)$role['is_system'] === 1) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'No se pueden eliminar roles del sistema.' : 'Cannot delete system roles.';
                header("Location: $base");
                exit;
            }
            $pdo->prepare("DELETE FROM system_roles WHERE id=?")->execute([$id]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Rol eliminado.' : 'Role deleted.';
        }
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}