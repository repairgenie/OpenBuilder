<?php
// src/PunchService.php
require_once __DIR__ . '/../src/Database.php';

class PunchService {
    public static function batchAssign($item_ids, $assigned_to, $lang = 'en') {
        $pdo = Database::connect();

        if (empty($item_ids) || empty($assigned_to)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $stmt = $pdo->prepare("UPDATE punch_list_items SET assigned_to=? WHERE id IN ($placeholders)");
        $stmt->execute([$assigned_to, ...$item_ids]);

        return true;
    }

    public static function createPunchItem($data) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO punch_list_items (description, location, assigned_to, priority, due_date, created_by, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))");
        return $stmt->execute([
            $data['description'],
            $data['location'] ?? '',
            $data['assigned_to'] ?? null,
            $data['priority'] ?? 'Medium',
            $data['due_date'] ?? null,
            $data['created_by'],
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        ]);
    }

    public static function updatePunchItem($id, $data) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE punch_list_items SET description=?, location=?, assigned_to=?, priority=?, due_date=?, latitude=?, longitude=? WHERE id=?");
        return $stmt->execute([
            $data['description'],
            $data['location'] ?? '',
            $data['assigned_to'] ?? null,
            $data['priority'] ?? 'Medium',
            $data['due_date'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $id
        ]);
    }

    public static function verifyPunchItem($id) {
        $pdo = Database::connect();
        return $pdo->prepare("UPDATE punch_list_items SET status='Verified' WHERE id=?")->execute([$id]);
    }

    public static function closePunchItems($item_ids) {
        $pdo = Database::connect();
        if (empty($item_ids)) return false;
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        return $pdo->prepare("UPDATE punch_list_items SET status='Closed' WHERE id IN ($placeholders)")->execute([...$item_ids]);
    }
}