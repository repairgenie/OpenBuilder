<?php
// templates/config/region_handler.php
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../../index.php?page=region_config&lang=$lang";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $base");
    exit;
}

if (!csrf_validate()) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token inválido.' : 'Invalid token.';
    header("Location: $base");
    exit;
}

$action = $_POST['action'] ?? '';
$pdo = Database::connect();

switch ($action) {
    case 'save_regions':
        $regions_json = $_POST['regions'] ?? '[]';
        $regions = json_decode($regions_json, true);
        
        if ($regions === null) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Datos inválidos.' : 'Invalid data.';
            header("Location: $base");
            exit;
        }

        // Clear existing regions and re-insert
        $pdo->exec("DELETE FROM regions");
        
        foreach ($regions as $r) {
            if (empty($r['name_en'])) continue;
            $stmt = $pdo->prepare("INSERT INTO regions (name_en, name_es, color, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                trim($r['name_en']),
                trim($r['name_es'] ?? ''),
                trim($r['color'] ?? '#3B82F6'),
                intval($r['is_active'] ?? 1)
            ]);
        }

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Updated region configuration", "Actualizó configuración de regiones", null, 'settings');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Regiones actualizadas.' : 'Regions updated.';
        header("Location: $base");
        exit;

    case 'add_region':
        $name_en = trim($_POST['name_en'] ?? '');
        $name_es = trim($_POST['name_es'] ?? '');
        $color = trim($_POST['color'] ?? '#3B82F6');

        if (!$name_en) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'Nombre en inglés es requerido.' : 'English name is required.';
            header("Location: $base");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO regions (name_en, name_es, color, is_active) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name_en, $name_es, $color]);

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Added region: $name_en", "Añadió región: $name_en", $pdo->lastInsertId(), 'settings');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Región añadida.' : 'Region added.';
        header("Location: $base");
        exit;

    case 'toggle_region':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("UPDATE regions SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
            $_SESSION['flash_success'] = $lang === 'es' ? 'Estado de región actualizado.' : 'Region status toggled.';
        }
        header("Location: $base");
        exit;

    case 'delete_region':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT name_en FROM regions WHERE id=?");
            $stmt->execute([$id]);
            $name = $stmt->fetchColumn() ?: "ID:$id";

            $pdo->prepare("DELETE FROM regions WHERE id=?")->execute([$id]);

            $user = $_SESSION['user_name'] ?? 'System';
            ActivityLog::log($user, "Deleted region: $name", "Eliminó región: $name", $id, 'settings');

            $_SESSION['flash_success'] = $lang === 'es' ? 'Región eliminada.' : 'Region deleted.';
        }
        header("Location: $base");
        exit;

    case 'save_settings':
        $project_name = trim($_POST['project_name'] ?? '');
        $project_location = trim($_POST['project_location'] ?? '');
        $currency = trim($_POST['currency'] ?? 'USD');
        $timezone = trim($_POST['timezone'] ?? 'America/Los_Angeles');
        $date_format = trim($_POST['date_format'] ?? 'Y-m-d');

        $settings_to_save = [
            'project_name' => $project_name,
            'project_location' => $project_location,
            'currency' => $currency,
            'timezone' => $timezone,
            'date_format' => $date_format,
        ];

        $stmt = $pdo->prepare("INSERT OR REPLACE INTO system_settings (key, value, updated_at) VALUES (?, ?, datetime('now'))");
        foreach ($settings_to_save as $k => $v) {
            $stmt->execute([$k, $v]);
        }

        $user = $_SESSION['user_name'] ?? 'System';
        ActivityLog::log($user, "Updated system settings", "Actualizó configuración del sistema", null, 'settings');

        $_SESSION['flash_success'] = $lang === 'es' ? 'Configuración guardada.' : 'Settings saved.';
        header("Location: $base");
        exit;

    default:
        header("Location: $base");
        exit;
}