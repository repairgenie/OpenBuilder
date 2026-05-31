<?php
// src/api/project_api.php
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/middleware.php';

$pdo = Database::connect();
$api_key = validate_api_key($pdo);
check_rate_limit($pdo, $api_key);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $uri);

// Route matching
$routes = [
    'GET' => [
        'projects' => 'getProjects',
        'rfis' => 'getRFIs',
        'daily-logs' => 'getDailyLogs',
        'budget' => 'getBudget',
        'submittals' => 'getSubmittals',
        'commitments' => 'getCommitments',
        'equipment' => 'getEquipment',
        'tasks' => 'getTasks',
    ],
    'POST' => [
        'timesheets' => 'createTimesheet',
        'observations' => 'createObservation',
        'hazards' => 'createHazard',
    ],
];

$handled = false;
foreach ($routes[$method] ?? [] as $route => $handler) {
    if ($path === $route) {
        $result = $handler($pdo, $_REQUEST);
        $code = $result['code'] ?? 200;
        log_api_request($pdo, $api_key['id'], '/api/' . $path, $method, $code);
        api_response($result['data'] ?? $result, $code);
        $handled = true;
    }
}

if (!$handled) {
    log_api_request($pdo, $api_key['id'], '/api/' . $path, $method, 404);
    api_error('Endpoint not found', 404);
}

function getProjects($pdo, $req) {
    $limit = intval($req['limit'] ?? 50);
    $offset = intval($req['offset'] ?? 0);
    $projects = $pdo->prepare("SELECT * FROM projects ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $projects->bindValue(':limit', $limit, PDO::PARAM_INT);
    $projects->bindValue(':offset', $offset, PDO::PARAM_INT);
    $projects->execute();
    return ['data' => $projects->fetchAll(PDO::FETCH_ASSOC)];
}

function getRFIs($pdo, $req) {
    $stmt = $pdo->prepare("SELECT * FROM rfis ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', intval($req['limit'] ?? 50), PDO::PARAM_INT);
    $stmt->bindValue(':offset', intval($req['offset'] ?? 0), PDO::PARAM_INT);
    $stmt->execute();
    return ['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function getDailyLogs($pdo, $req) {
    $limit = intval($req['limit'] ?? 50);
    $stmt = $pdo->prepare("SELECT * FROM daily_logs ORDER BY log_date DESC, id DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return ['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function getBudget($pdo, $req) {
    // Join budget line items with cost codes
    $items = $pdo->query("
        SELECT b.*, cc.code as cost_code, cc.description as cost_description
        FROM budget_line_items b
        LEFT JOIN cost_codes cc ON b.cost_code_id = cc.id
        ORDER BY b.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $summary = $pdo->query("SELECT SUM(original_budget) as total_original, SUM(revised_budget) as total_revised, SUM(committed) as total_committed, SUM(expended) as total_expended FROM budget_line_items")->fetch(PDO::FETCH_ASSOC);
    return ['data' => ['items' => $items, 'summary' => $summary]];
}

function getSubmittals($pdo, $req) {
    $status = $req['status'] ?? '';
    $query = "SELECT s.*, u.name as assignee_name FROM submittals s LEFT JOIN users u ON s.ball_in_court = u.id";
    $params = [];
    if ($status) {
        $query .= " WHERE s.status = :status";
        $params[':status'] = $status;
    }
    $query .= " ORDER BY s.id DESC LIMIT :limit";
    $stmt = $pdo->prepare($query);
    if ($status) $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':limit', intval($req['limit'] ?? 50), PDO::PARAM_INT);
    $stmt->execute();
    return ['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function createTimesheet($pdo, $req) {
    $required = ['worker_name', 'trade_en', 'hours', 'date'];
    foreach ($required as $field) {
        if (empty($req[$field])) {
            return ['data' => ['error' => "Missing required field: $field"], 'code' => 400];
        }
    }
    $stmt = $pdo->prepare("INSERT INTO timesheets (worker_name, trade_en, trade_es, hours, date, cost_code_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Draft')");
    $stmt->execute([
        $req['worker_name'],
        $req['trade_en'],
        $req['trade_es'] ?? '',
        $req['hours'],
        $req['date'],
        $req['cost_code_id'] ?? null
    ]);
    return ['data' => ['id' => $pdo->lastInsertId(), 'status' => 'created'], 'code' => 201];
}

function createObservation($pdo, $req) {
    $required = ['project_id', 'observation_text', 'category'];
    foreach ($required as $field) {
        if (empty($req[$field])) {
            return ['data' => ['error' => "Missing required field: $field"], 'code' => 400];
        }
    }
    $stmt = $pdo->prepare("INSERT INTO observations (project_id, observer_id, observation_text, category, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'Open', datetime('now'))");
    $stmt->execute([
        $req['project_id'],
        $req['observer_id'] ?? null,
        $req['observation_text'],
        $req['category'],
        $req['priority'] ?? 'Medium'
    ]);
    return ['data' => ['id' => $pdo->lastInsertId(), 'status' => 'created'], 'code' => 201];
}

function createHazard($pdo, $req) {
    $required = ['description', 'severity', 'reported_date'];
    foreach ($required as $field) {
        if (empty($req[$field])) {
            return ['data' => ['error' => "Missing required field: $field"], 'code' => 400];
        }
    }
    $stmt = $pdo->prepare("INSERT INTO safety_hazards (description, location, severity, reported_date, reported_by, status, created_at) VALUES (?, ?, ?, ?, ?, 'Open', datetime('now'))");
    $stmt->execute([
        $req['description'],
        $req['location'] ?? '',
        $req['severity'],
        $req['reported_date'],
        $req['reported_by'] ?? null
    ]);
    return ['data' => ['id' => $pdo->lastInsertId(), 'status' => 'created'], 'code' => 201];
}

function getCommitments($pdo, $req) {
    return ['data' => $pdo->query("SELECT c.*, v.name as vendor_name FROM commitments c LEFT JOIN vendors v ON c.vendor_id = v.id ORDER BY c.id DESC")->fetchAll(PDO::FETCH_ASSOC)];
}

function getEquipment($pdo, $req) {
    return ['data' => $pdo->query("SELECT e.*, p.name as project_name, c.name as crew_name FROM equipment e LEFT JOIN projects p ON e.assigned_project = p.id LEFT JOIN crews c ON e.assigned_crew_id = c.id ORDER BY e.id DESC")->fetchAll(PDO::FETCH_ASSOC)];
}

function getTasks($pdo, $req) {
    return ['data' => $pdo->query("SELECT t.*, c.name as crew_name, cc.code as cost_code FROM tasks t LEFT JOIN crews c ON t.assigned_crew_id = c.id LEFT JOIN cost_codes cc ON t.cost_code_id = cc.id ORDER BY t.start_date ASC")->fetchAll(PDO::FETCH_ASSOC)];
}