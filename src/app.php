<?php
// src/app.php - Core Logic and Bootstrap

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AIProvider.php';
require_once __DIR__ . '/ModuleRegistry.php';
require_once __DIR__ . '/security_helper.php';
require_once __DIR__ . '/GPSEngine.php';

// Initialize System
ModuleRegistry::init();
$pdo = Database::connect();

$page = $_GET['page'] ?? 'dashboard';

// Protected pages require auth
$public_pages = ['dashboard', 'login', 'login_handler', 'mfa', 'mfa_handler', 'docs', 'health', 'api_handler'];
if (!in_array($page, $public_pages)) {
    require_auth();
}

$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'es'])) $lang = 'en';

$region = $_GET['region'] ?? 'USA';
if (!in_array($region, ['USA', 'MEX', 'ESP'])) $region = 'USA';

$page_titles = [
    'dashboard' => ['en' => 'Dashboard', 'es' => 'Panel'],
    'rfis' => ['en' => 'RFIs', 'es' => 'Solicitudes'],
    'create_rfi' => ['en' => 'Create RFI', 'es' => 'Crear RFI'],
    'daily_logs' => ['en' => 'Daily Logs', 'es' => 'Diarios'],
    'create_daily_log' => ['en' => 'Create Daily Log', 'es' => 'Crear Diario'],
    'view_daily_log' => ['en' => 'View Daily Log', 'es' => 'Ver Diario'],
    'budget' => ['en' => 'Budget', 'es' => 'Presupuesto'],
    'docs' => ['en' => 'Docs', 'es' => 'Docs'],
    'project_settings' => ['en' => 'Settings', 'es' => 'Ajustes'],
    'notification_prefs' => ['en' => 'Notification Preferences', 'es' => 'Preferencias de Notificación'],
    'audit_logs' => ['en' => 'Audit Logs', 'es' => 'Auditoría'],
    'users' => ['en' => 'User Management', 'es' => 'Gestión de Usuarios'],
    'roles' => ['en' => 'Roles & Permissions', 'es' => 'Roles y Permisos'],
    'api_keys' => ['en' => 'API Keys', 'es' => 'Claves API'],
    'api_handler' => ['en' => 'API Handler', 'es' => 'Gestor API'],
    'mfa' => ['en' => 'MFA', 'es' => 'MFA'],
];

define('ASSET_VERSION', '1.1.0');

// RBAC Helpers
function has_permission($user_role, $action) {
    $matrix = [
        'Admin' => ['view_budget', 'edit_budget', 'close_rfi', 'manage_users'],
        'Manager' => ['view_budget', 'close_rfi'],
        'Subcontractor' => ['view_rfis']
    ];
    return in_array($action, $matrix[$user_role] ?? []);
}

// Global Helpers
function get_optimized_image($src) {
    return $src; 
}

function calculate_budget_metrics($code) {
    $revised_budget = $code['original_budget'] + $code['change_orders'];
    $percentage_spent = $revised_budget > 0 ? ($code['committed_costs'] / $revised_budget) * 100 : 0;
    return [
        'revised_budget' => $revised_budget,
        'percentage_spent' => min(100, max(0, $percentage_spent)),
        'variance' => $revised_budget - $code['committed_costs']
    ];
}

function paginate_results($pdo, $query, $params = [], $per_page = 5) {
    $p = max(1, (int)($_GET['p'] ?? 1));
    $offset = ($p - 1) * $per_page;
    $count_query = preg_replace('/SELECT (.*) FROM/i', 'SELECT COUNT(*) FROM', $query);
    $count_query = preg_replace('/ORDER BY (.*)/i', '', $count_query);
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $per_page);
    $paged_query = $query . " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($paged_query);
    foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return [
        'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'total_pages' => $total_pages,
        'current_page' => $p,
        'total_items' => $total_items
    ];
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_auth();
    $csrf = $_POST['csrf_token'] ?? '';
    if (!csrf_valid($csrf)) {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(['success' => false, 'error' => 'CSRF validation failed']);
        exit;
    }
    switch ($_POST['action']) {
        case 'create_rfi':
            if (empty(trim($_POST['subject'] ?? ''))) { header("Location: index.php?page=create_rfi&error=required"); die(); }
            if (!has_role(['Worker', 'Manager', 'Admin'])) {
                header("HTTP/1.1 403 Forbidden");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO rfis (ref_number, subject, status, priority, due_date, created_by) VALUES (:ref_number, :subject, :status, :priority, :due_date, :created_by)");
            $stmt->execute([
                ':ref_number' => $_POST['ref_number'] ?? '',
                ':subject' => trim($_POST['subject'] ?? ''),
                ':status' => $_POST['status'] ?? 'Open',
                ':priority' => $_POST['priority'] ?? 'Medium',
                ':due_date' => $_POST['due_date'] ?? '',
                ':created_by' => $_SESSION['user_id']
            ]);
            header("Location: index.php?page=rfis&lang=$lang");
            exit;

        case 'create_daily_log':
            if (!has_role(['Worker', 'Manager', 'Admin'])) {
                header("HTTP/1.1 403 Forbidden");
                exit;
            }
            if (!isset($_SESSION['user_id'])) {
                header("HTTP/1.1 403 Forbidden");
                exit;
            }
            $lat = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
            $lon = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
            $gps_stamp = GPSEngine::formatStamp($lat, $lon);
            $ai = new AIProvider(getenv('GEMINI_API_KEY'));
            $ai_report = $ai->generateReport($_POST['work_performed'] ?? '', $_POST['weather'] ?? '', $lang);
            $stmt = $pdo->prepare("INSERT INTO daily_logs (log_date, weather, manpower, work_performed, ai_report, latitude, longitude, gps_stamp, created_by) VALUES (:log_date, :weather, :manpower, :work_performed, :ai_report, :latitude, :longitude, :gps_stamp, :created_by)");
            $stmt->execute([
                ':log_date' => $_POST['log_date'] ?? '',
                ':weather' => $_POST['weather'] ?? '',
                ':manpower' => $_POST['manpower'] ?? 0,
                ':work_performed' => $_POST['work_performed'] ?? '',
                ':ai_report' => $ai_report,
                ':latitude' => $lat,
                ':longitude' => $lon,
                ':gps_stamp' => $gps_stamp,
                ':created_by' => $_SESSION['user_id']
            ]);
            header("Location: index.php?page=view_daily_log&id=" . $pdo->lastInsertId() . "&lang=$lang");
            exit;

        case 'create_cost_code':
            if (!has_role(['Manager', 'Admin'])) {
                header("HTTP/1.1 403 Forbidden");
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO cost_codes (code, name, original_budget, created_by) VALUES (:code, :name, :original_budget, :created_by)");
            $stmt->execute([
                ':code' => $_POST['code'] ?? '',
                ':name' => $_POST['name'] ?? '',
                ':original_budget' => $_POST['original_budget'] ?? 0,
                ':created_by' => $_SESSION['user_id']
            ]);
            header("Location: index.php?page=budget&lang=$lang");
            exit;
    }
}

// Handle Exports
if (isset($_GET['action'])) {
    require_auth();
    require_once __DIR__ . '/Exporter.php';
    if ($_GET['action'] === 'export_rfis') {
        if (!has_role(['Manager', 'Admin'])) {
            header("HTTP/1.1 403 Forbidden");
            exit;
        }
        $stmt = $pdo->query("SELECT * FROM rfis ORDER BY id DESC");
        Exporter::exportRFIs($stmt->fetchAll(PDO::FETCH_ASSOC), $lang);
    }
    if ($_GET['action'] === 'export_budget') {
        if (!has_role(['Manager', 'Admin'])) {
            header("HTTP/1.1 403 Forbidden");
            exit;
        }
        $stmt = $pdo->query("SELECT * FROM cost_codes ORDER BY code ASC");
        Exporter::exportBudget($stmt->fetchAll(PDO::FETCH_ASSOC), $lang);
    }
}
