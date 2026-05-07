<?php
// src/InspectionService.php

class InspectionService {
    public static function createObservationFromFail($item_id, $lang = 'en') {
        $pdo = Database::connect();
        
        // Simulation: Trigger Observation workflow
        $msg = ($lang === 'es') 
            ? "Creando Observación por falla en el ítem #$item_id." 
            : "Creating Observation for failure on item #$item_id.";
        error_log($msg);
        
        ActivityLog::log('System', 'Created Observation from Inspection Fail', 'Creó Observación por falla de Inspección', $item_id, 'inspections');
        return true;
    }
}
