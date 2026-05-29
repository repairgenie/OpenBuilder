<?php
// src/api/webhooks.php
require_once __DIR__ . '/../Database.php';

function dispatch_webhook($pdo, $event, $payload) {
    $webhooks = $pdo->prepare("SELECT * FROM webhooks WHERE is_active=1")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($webhooks as $wh) {
        $events = json_decode($wh['events'] ?? '[]', true);
        if (!in_array($event, $events)) continue;
        deliver_webhook($wh['url'], $wh['secret'], $event, $payload);
    }
}

function deliver_webhook($url, $secret, $event, $payload) {
    $body = json_encode(['event' => $event, 'payload' => $payload, 'timestamp' => date('c')]);
    $signature = hash_hmac('sha256', $body, $secret);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-Webhook-Signature: ' . $signature,
            'X-Webhook-Event: ' . $event,
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // Log delivery attempt
    error_log("Webhook[$event] -> $url [HTTP $code]");
}

// Hook into key events — call from handlers:
function trigger_rfi_created($pdo, $rfi_id) {
    $stmt = $pdo->prepare("SELECT * FROM rfis WHERE id=?");
    $stmt->execute([$rfi_id]);
    $rfi = $stmt->fetch(PDO::FETCH_ASSOC);
    dispatch_webhook($pdo, 'rfi.created', $rfi);
}

function trigger_co_approved($pdo, $co_id) {
    $stmt = $pdo->prepare("SELECT * FROM change_orders WHERE id=?");
    $stmt->execute([$co_id]);
    $co = $stmt->fetch(PDO::FETCH_ASSOC);
    dispatch_webhook($pdo, 'change_order.approved', $co);
}

function trigger_inspection_completed($pdo, $inspection_id) {
    $stmt = $pdo->prepare("SELECT * FROM inspections WHERE id=?");
    $stmt->execute([$inspection_id]);
    $insp = $stmt->fetch(PDO::FETCH_ASSOC);
    dispatch_webhook($pdo, 'inspection.completed', $insp);
}