<?php
// templates/notification_prefs_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_auth();

$lang = $_GET['lang'] ?? 'en';
$base = "../index.php?page=notification_prefs&lang=$lang";

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
$user_id = (int)($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {
    header("Location: ../index.php?page=login&lang=$lang");
    exit;
}

if ($action === 'save_prefs') {
    $email_rfis        = isset($_POST['email_rfis']) ? 1 : 0;
    $email_daily_logs  = isset($_POST['email_daily_logs']) ? 1 : 0;
    $email_budget_alerts = isset($_POST['email_budget_alerts']) ? 1 : 0;
    $email_submittals  = isset($_POST['email_submittals']) ? 1 : 0;
    $email_inspections = isset($_POST['email_inspections']) ? 1 : 0;

    // Upsert preferences
    $stmt = $pdo->prepare("
        INSERT INTO user_notification_prefs
            (user_id, email_rfis, email_daily_logs, email_budget_alerts, email_submittals, email_inspections, updated_at)
        VALUES
            (:user_id, :email_rfis, :email_daily_logs, :email_budget_alerts, :email_submittals, :email_inspections, datetime('now'))
        ON CONFLICT(user_id) DO UPDATE SET
            email_rfis = :email_rfis2,
            email_daily_logs = :email_daily_logs2,
            email_budget_alerts = :email_budget_alerts2,
            email_submittals = :email_submittals2,
            email_inspections = :email_inspections2,
            updated_at = datetime('now')
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':email_rfis' => $email_rfis,
        ':email_rfis2' => $email_rfis,
        ':email_daily_logs' => $email_daily_logs,
        ':email_daily_logs2' => $email_daily_logs,
        ':email_budget_alerts' => $email_budget_alerts,
        ':email_budget_alerts2' => $email_budget_alerts,
        ':email_submittals' => $email_submittals,
        ':email_submittals2' => $email_submittals,
        ':email_inspections' => $email_inspections,
        ':email_inspections2' => $email_inspections,
    ]);

    $_SESSION['flash_success'] = $lang === 'es' ? 'Preferencias guardadas.' : 'Preferences saved.';
    header("Location: $base");
    exit;
}

header("Location: $base");
exit;
