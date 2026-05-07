<?php
// src/app.php - Core Logic and Database Setup

$page = $_GET['page'] ?? 'dashboard';
$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'es'])) $lang = 'en';

$page_titles = [
    'dashboard' => ['en' => 'Dashboard', 'es' => 'Panel'],
    'rfis' => ['en' => 'RFIs', 'es' => 'Solicitudes'],
    'create_rfi' => ['en' => 'Create RFI', 'es' => 'Crear RFI'],
    'daily_logs' => ['en' => 'Daily Logs', 'es' => 'Diarios'],
    'create_daily_log' => ['en' => 'Create Daily Log', 'es' => 'Crear Diario'],
    'view_daily_log' => ['en' => 'View Daily Log', 'es' => 'Ver Diario'],
    'budget' => ['en' => 'Budget', 'es' => 'Presupuesto'],
    'docs' => ['en' => 'Docs', 'es' => 'Docs'],
];

define('ASSET_VERSION', '1.0.1');

$db_file = __DIR__ . '/../database.sqlite';
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure tables exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rfis (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ref_number TEXT NOT NULL,
            subject TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Open',
            priority TEXT NOT NULL DEFAULT 'Medium',
            due_date TEXT NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS daily_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            log_date TEXT NOT NULL,
            weather TEXT,
            manpower INTEGER,
            work_performed TEXT,
            ai_report TEXT
        )
    ");
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

function get_optimized_image($src) {
    // In a real app, this would check for .webp support and serve the correct version
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
    $page = $_GET['p'] ?? 1;
    $offset = ($page - 1) * $per_page;

    // Get total count
    $count_query = preg_replace('/SELECT (.*) FROM/i', 'SELECT COUNT(*) FROM', $query);
    $count_query = preg_replace('/ORDER BY (.*)/i', '', $count_query);
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $per_page);

    // Get limited items
    $paged_query = $query . " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($paged_query);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'items' => $items,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_items' => $total_items
    ];
}

// Mock Data
$recent_activities = [
    ['id' => 1, 'event' => $lang === 'es' ? 'Vaciado de Concreto - Cimentación' : 'Concrete Pour - Foundation', 'date' => 'Oct 24, 2023', 'status' => 'Completed'],
    ['id' => 2, 'event' => $lang === 'es' ? 'Montaje de Acero Estructural' : 'Structural Steel Erection', 'date' => 'Oct 23, 2023', 'status' => 'In Progress'],
    ['id' => 3, 'event' => $lang === 'es' ? 'Entrega de Paneles de Yeso' : 'Drywall Delivery - Sector B', 'date' => 'Oct 22, 2023', 'status' => 'Pending'],
    ['id' => 4, 'event' => $lang === 'es' ? 'Inspección de Plomería' : 'Plumbing Rough-in Inspection', 'date' => 'Oct 21, 2023', 'status' => 'Delayed'],
];

$cost_codes = [
    ['code' => '03-300', 'name' => 'Concrete', 'original_budget' => 150000, 'change_orders' => 12000, 'committed_costs' => 145000],
    ['code' => '05-100', 'name' => 'Structural Steel', 'original_budget' => 200000, 'change_orders' => 0, 'committed_costs' => 180000],
    ['code' => '09-200', 'name' => 'Drywall', 'original_budget' => 85000, 'change_orders' => -5000, 'committed_costs' => 40000],
    ['code' => '26-000', 'name' => 'Electrical', 'original_budget' => 120000, 'change_orders' => 25000, 'committed_costs' => 135000],
    ['code' => '22-000', 'name' => 'Plumbing', 'original_budget' => 95000, 'change_orders' => 5000, 'committed_costs' => 98000],
];

// Handle RFI Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_rfi') {
    $stmt = $pdo->prepare("INSERT INTO rfis (ref_number, subject, status, priority, due_date) VALUES (:ref_number, :subject, :status, :priority, :due_date)");
    $stmt->execute([
        ':ref_number' => $_POST['ref_number'] ?? '',
        ':subject' => $_POST['subject'] ?? '',
        ':status' => $_POST['status'] ?? 'Open',
        ':priority' => $_POST['priority'] ?? 'Medium',
        ':due_date' => $_POST['due_date'] ?? ''
    ]);
    header("Location: index.php?page=rfis");
    exit;
}

// Handle Daily Log Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_daily_log') {
    require_once __DIR__ . '/AIProvider.php';
    
    $log_date = $_POST['log_date'] ?? '';
    $weather = $_POST['weather'] ?? '';
    $manpower = $_POST['manpower'] ?? 0;
    $work_performed = $_POST['work_performed'] ?? '';

    $ai = new AIProvider(getenv('GEMINI_API_KEY'));
    $ai_report = $ai->generateReport($work_performed, $weather, $lang);

    $stmt = $pdo->prepare("INSERT INTO daily_logs (log_date, weather, manpower, work_performed, ai_report) VALUES (:log_date, :weather, :manpower, :work_performed, :ai_report)");
    $stmt->execute([
        ':log_date' => $log_date,
        ':weather' => $weather,
        ':manpower' => $manpower,
        ':work_performed' => $work_performed,
        ':ai_report' => $ai_report
    ]);

    $log_id = $pdo->lastInsertId();
    header("Location: index.php?page=view_daily_log&id=" . $log_id . "&lang=" . $lang);
    exit;
}

// Handle Cost Code Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_cost_code') {
    $code = $_POST['code'] ?? '';
    $name = $_POST['name'] ?? '';
    $original_budget = $_POST['original_budget'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO cost_codes (code, name, original_budget, change_orders, committed_costs) VALUES (:code, :name, :original_budget, 0, 0)");
    $stmt->execute([
        ':code' => $code,
        ':name' => $name,
        ':original_budget' => $original_budget
    ]);

    header("Location: index.php?page=budget&lang=" . $lang);
    exit;
}

// Handle Exports
if (isset($_GET['action'])) {
    require_once __DIR__ . '/Exporter.php';
    if ($_GET['action'] === 'export_rfis') {
        $stmt = $pdo->query("SELECT * FROM rfis ORDER BY id DESC");
        Exporter::exportRFIs($stmt->fetchAll(PDO::FETCH_ASSOC), $lang);
    }
    if ($_GET['action'] === 'export_budget') {
        Exporter::exportBudget($cost_codes, $lang);
    }
}
