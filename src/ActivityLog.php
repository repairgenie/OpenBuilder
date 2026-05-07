<?php
// src/ActivityLog.php

class ActivityLog {
    public static function log($user, $action_en, $action_es, $ref_id = null, $module = 'system') {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO activity_logs (username, action_en, action_es, ref_id, module, created_at) VALUES (:u, :en, :es, :id, :m, :t)");
        $stmt->execute([
            ':u'  => $user,
            ':en' => $action_en,
            ':es' => $action_es,
            ':id' => $ref_id,
            ':m'  => $module,
            ':t'  => date('Y-m-d H:i:s')
        ]);
    }

    public static function initTable() {
        $pdo = Database::connect();
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                action_en TEXT NOT NULL,
                action_es TEXT NOT NULL,
                ref_id INTEGER,
                module TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        ");
    }
}
