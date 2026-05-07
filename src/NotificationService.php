<?php
// src/NotificationService.php

class NotificationService {
    private static $notifications = [];

    public static function notify($user_id, $message_en, $message_es, $type = 'info') {
        // In a real app, this would persist to DB and push via WebSockets
        self::$notifications[] = [
            'user_id' => $user_id,
            'msg_en'  => $message_en,
            'msg_es'  => $message_es,
            'type'    => $type,
            'time'    => date('Y-m-d H:i:s')
        ];
    }

    public static function getUnread($user_id, $lang = 'en') {
        // Simulation: Return a mock unread notification
        return [
            ['id' => 1, 'text' => $lang === 'es' ? 'Nueva RFI asignada' : 'New RFI assigned', 'time' => '2 min ago']
        ];
    }
}
