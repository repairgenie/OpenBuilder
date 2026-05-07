<?php
// src/FinancialService.php

class FinancialService {
    public static function calculateRetention($amount, $rate = 0.10) {
        return $amount * $rate;
    }

    public static function processClaim($claim_id, $lang = 'en') {
        $pdo = Database::connect();
        
        // Simulation: Calculate retention and approve claim
        $msg = ($lang === 'es') 
            ? "Procesando reclamo #$claim_id. Retención aplicada (10%)." 
            : "Processing claim #$claim_id. Retention applied (10%).";
        error_log($msg);
        
        ActivityLog::log('System', 'Processed Progress Claim #'.$claim_id, 'Procesó Reclamo de Progreso #'.$claim_id, $claim_id, 'financials');
        return true;
    }

    public static function syncLaborCosts($hours, $cost_code, $lang = 'en') {
        // Simulation: Update budget spend based on reported labor hours
        $rate = 45.00; // Mock blended hourly rate
        $total = $hours * $rate;
        
        $msg = ($lang === 'es') 
            ? "Sincronizando costos laborales para '$cost_code': \$$total ($hours hrs)." 
            : "Syncing labor costs for '$cost_code': \$$total ($hours hrs).";
        error_log($msg);
        
        ActivityLog::log('System', 'Synced Labor Costs to Budget ('.$cost_code.')', 'Sincronizó Costos Laborales al Presupuesto ('.$cost_code.')', $hours, 'labor');
        return true;
    }
}
