<?php
// src/api/middleware.php

function api_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function api_error($message, $code = 400) {
    api_response(['error' => $message], $code);
}

function validate_api_key($pdo) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer\s+(.+)$/i', $auth_header, $m)) {
        api_error('Missing or invalid Authorization header', 401);
    }
    $key = $m[1];
    $hash = hash('sha256', $key);
    $stmt = $pdo->prepare("SELECT * FROM api_keys WHERE key_hash=? AND is_active=1");
    $stmt->execute([$hash]);
    $api_key = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$api_key) {
        api_error('Invalid API key', 401);
    }
    // Update last_used_at
    $pdo->prepare("UPDATE api_keys SET last_used_at=datetime('now') WHERE id=?")->execute([$api_key['id']]);
    return $api_key;
}

function check_rate_limit($pdo, $api_key) {
    $limit = $api_key['rate_limit'] ?? 100;
    $window = 60; // 1 minute window
    $key_name = 'ratelimit:' . $api_key['id'] . ':' . floor(time() / $window);
    // Simple in-memory rate limiting (use Redis in production)
    static $counts = [];
    if (!isset($counts[$key_name])) $counts[$key_name] = 0;
    $counts[$key_name]++;
    if ($counts[$key_name] > $limit) {
        api_error('Rate limit exceeded', 429);
    }
}

function log_api_request($pdo, $api_key_id, $endpoint, $method, $response_code) {
    $pdo->prepare("INSERT INTO api_logs (api_key_id, endpoint, method, response_code, created_at) VALUES (?, ?, ?, ?, datetime('now'))")
        ->execute([$api_key_id, $endpoint, $method, $response_code]);
}