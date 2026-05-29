<?php
// templates/user_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=users&lang=$lang";

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
    case 'create_user':
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = trim($_POST['role'] ?? 'Viewer');
        $status = trim($_POST['status'] ?? 'Active');

        if (!$name || !$email) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Nombre y email son requeridos.' : 'Name and email are required.';
            header("Location: $base");
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Email inválido.' : 'Invalid email address.';
            header("Location: $base");
            exit;
        }
        // Default password: User123!
        $password_hash = password_hash('User123!', PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
            $stmt->execute([$name, $email, $password_hash, $role, $status]);
            $new_id = $pdo->lastInsertId();
            log_activity(
                "Created user: $name",
                "Creó usuario: $name",
                'users',
                (int)$new_id
            );
            $_SESSION['flash_success'] = $lang === 'es' ? 'Usuario creado.' : 'User created.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El email ya existe.' : 'Email already exists.';
            } else {
                $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
            }
        }
        header("Location: $base");
        exit;

    case 'update_user':
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = trim($_POST['role'] ?? 'Viewer');
        $status = trim($_POST['status'] ?? 'Active');

        if (!$id || !$name || !$email) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, status=? WHERE id=?");
            $stmt->execute([$name, $email, $role, $status, $id]);
            log_activity(
                "Updated user: $name",
                "Actualizó usuario: $name",
                'users',
                $id
            );
            $_SESSION['flash_success'] = $lang === 'es' ? 'Usuario actualizado.' : 'User updated.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $_SESSION['flash_error'] = $lang === 'es' ? 'El email ya existe.' : 'Email already exists.';
            } else {
                $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
            }
        }
        header("Location: $base");
        exit;

    case 'delete_user':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Get name before delete for log
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id=?");
            $stmt->execute([$id]);
            $user_name = $stmt->fetchColumn() ?: "ID:$id";
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            log_activity(
                "Deleted user: $user_name",
                "Eliminó usuario: $user_name",
                'users',
                $id
            );
            $_SESSION['flash_success'] = $lang === 'es' ? 'Usuario eliminado.' : 'User deleted.';
        }
        header("Location: $base");
        exit;

    case 'reset_password':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id=?");
            $stmt->execute([$id]);
            $user_name = $stmt->fetchColumn() ?: "ID:$id";
            $hash = password_hash('User123!', PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $id]);
            log_activity(
                "Reset password for: $user_name",
                "Restauró contraseña para: $user_name",
                'users',
                $id
            );
            $_SESSION['flash_success'] = $lang === 'es' ? 'Contraseña restaurada a User123!' : 'Password reset to User123!';
        }
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}
