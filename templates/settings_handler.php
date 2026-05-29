<?php
// templates/settings_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=project_settings&lang=$lang";

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

$section = $_POST['section'] ?? 'general';

// ----- General -----
if ($section === 'general') {
    set_setting('project_name', trim($_POST['project_name'] ?? ''));
    set_setting('project_location', trim($_POST['project_location'] ?? ''));
    set_setting('project_start_date', trim($_POST['project_start_date'] ?? ''));
    set_setting('project_end_date', trim($_POST['project_end_date'] ?? ''));
    set_setting('project_client', trim($_POST['project_client'] ?? ''));
    set_setting('contract_value', trim($_POST['contract_value'] ?? ''));
    $_SESSION['flash_success'] = $lang === 'es' ? 'Configuración guardada' : 'Settings saved';
}

// ----- Notifications -----
if ($section === 'notifications') {
    set_setting('budget_alerts', $_POST['budget_alerts'] ?? '0');
    set_setting('new_rfi_notifications', $_POST['new_rfi_notifications'] ?? '0');
    set_setting('daily_log_reminders', $_POST['daily_log_reminders'] ?? '0');
    set_setting('inspection_reminders', $_POST['inspection_reminders'] ?? '0');
    $_SESSION['flash_success'] = $lang === 'es' ? 'Notificaciones actualizadas' : 'Notification preferences updated';
}

// ----- Regional -----
if ($section === 'regional') {
    set_setting('currency', trim($_POST['currency'] ?? 'USD'));
    set_setting('date_format', trim($_POST['date_format'] ?? 'Y-m-d'));
    set_setting('timezone', trim($_POST['timezone'] ?? 'America/Los_Angeles'));
    set_setting('default_region', trim($_POST['default_region'] ?? 'USA'));
    $_SESSION['flash_success'] = $lang === 'es' ? 'Configuración regional guardada' : 'Regional settings saved';
}

// ----- Danger Zone -----
if ($section === 'danger') {
    $confirm = trim($_POST['confirm_archive'] ?? '');
    if (strtolower($confirm) === strtolower($_POST['project_name'] ?? '')) {
        set_setting('project_archived', '1');
        $_SESSION['flash_success'] = $lang === 'es' ? 'Proyecto archivado' : 'Project archived';
    } else {
        $_SESSION['flash_error'] = $lang === 'es' ? 'Nombre del proyecto no coincide.' : 'Project name does not match.';
    }
}

header("Location: $base");
exit;
