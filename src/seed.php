<?php
// src/seed.php

function seed_database($pdo) {
    // Clear existing data
    $pdo->exec("DELETE FROM rfis");
    $pdo->exec("DELETE FROM daily_logs");
    $pdo->exec("DELETE FROM cost_codes");

    // Seed Cost Codes
    $cost_codes = [
        ['03-3000', 'Cast-in-Place Concrete', 50000, 5000, 25000],
        ['05-1200', 'Structural Steel', 120000, 0, 80000],
        ['06-1100', 'Wood Framing', 45000, 2500, 15000],
        ['08-1100', 'Metal Doors & Frames', 12000, 500, 2000],
        ['09-2900', 'Gypsum Board', 35000, 1500, 5000],
    ];

    foreach ($cost_codes as $code) {
        $stmt = $pdo->prepare("INSERT INTO cost_codes (code, name, original_budget, change_orders, committed_costs) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($code);
    }

    // Seed RFIs
    $rfis = [
        ['#101', 'Foundation Rebar Spacing', 'High', 'Open', '2023-10-15'],
        ['#102', 'Beam Connection Detail', 'Medium', 'Open', '2023-10-18'],
        ['#103', 'HVAC Duct Clearance', 'Low', 'Closed', '2023-10-10'],
    ];

    foreach ($rfis as $rfi) {
        $stmt = $pdo->prepare("INSERT INTO rfis (ref_number, subject, priority, status, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($rfi);
    }

    // Seed Daily Logs
    $logs = [
        ['2023-10-24', 'Sunny', 12, 'Poured foundation wall for Sector A. Concrete arrived on time. 12 trucks total.', 'Professional AI report summary for foundation pour.'],
        ['2023-10-23', 'Light Rain', 8, 'Set up structural steel columns. Rain delayed afternoon welding.', 'AI summary noting weather delays in steel erection.'],
    ];

    foreach ($logs as $log) {
        $stmt = $pdo->prepare("INSERT INTO daily_logs (log_date, weather, manpower, work_performed, ai_report) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($log);
    }

    return "Database seeded successfully.";
}

if (php_sapi_name() === 'cli') {
    $db_file = __DIR__ . '/../database.sqlite';
    try {
        $pdo = new PDO("sqlite:$db_file");
        echo seed_database($pdo) . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
