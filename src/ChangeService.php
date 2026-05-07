<?php
// src/ChangeService.php

class ChangeService {
    public static function syncWithBudget($event_id, $lang = 'en') {
        $pdo = Database::connect();
        
        // Simulation: Fetch event and update cost code
        $stmt = $pdo->prepare("SELECT * FROM change_events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            $msg = ($lang === 'es') 
                ? "Sincronizando evento '{$event['title_es']}' con el presupuesto." 
                : "Syncing event '{$event['title_en']}' with budget.";
            error_log($msg);
            
            ActivityLog::log('System', 'Synced Change Event #'.$event_id.' to Budget', 'Sincronizó Evento de Cambio #'.$event_id.' al Presupuesto', $event_id, 'change_mgmt');
        }
        return true;
    }

    public static function updateCommitmentValue($commitment_id, $amount, $lang = 'en') {
        // Simulation: Update the commitment total with approved change order value
        $msg = ($lang === 'es') 
            ? "Actualizando valor del compromiso #$commitment_id por \$$amount." 
            : "Updating commitment #$commitment_id value by \$$amount.";
        error_log($msg);
        
        ActivityLog::log('System', 'Updated Commitment #'.$commitment_id.' (CO)', 'Actualizó Compromiso #'.$commitment_id.' (CO)', $commitment_id, 'financials');
        return true;
    }
}
