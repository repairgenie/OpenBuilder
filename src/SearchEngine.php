<?php
// src/SearchEngine.php

class SearchEngine {
    public static function search($query, $lang = 'en') {
        $pdo = Database::connect();
        $results = [];

        // Search RFIs
        $stmt = $pdo->prepare("SELECT id, subject as title, 'rfis' as type FROM rfis WHERE subject LIKE ? LIMIT 5");
        $stmt->execute(["%$query%"]);
        $results['rfis'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Search Logs
        $stmt = $pdo->prepare("SELECT id, work_performed as title, 'daily_logs' as type FROM daily_logs WHERE work_performed LIKE ? LIMIT 5");
        $stmt->execute(["%$query%"]);
        $results['logs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Search Budget
        $stmt = $pdo->prepare("SELECT code as id, name as title, 'budget' as type FROM cost_codes WHERE name LIKE ? OR code LIKE ? LIMIT 5");
        $stmt->execute(["%$query%", "%$query%"]);
        $results['budget'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}
