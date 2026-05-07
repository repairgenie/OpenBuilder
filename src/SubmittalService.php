<?php
// src/SubmittalService.php

class SubmittalService {
    public static function processReview($submittal_id, $status, $comment_en, $comment_es, $lang = 'en') {
        $pdo = Database::connect();
        
        // Simulation: Update submittal status and record response
        $msg = ($lang === 'es') 
            ? "Revisión procesada para Submittal #$submittal_id: $status." 
            : "Review processed for Submittal #$submittal_id: $status.";
        error_log($msg);
        
        ActivityLog::log('System', 'Reviewed Submittal #'.$submittal_id.' ('.$status.')', 'Revisó Submittal #'.$submittal_id.' ('.$status.')', $submittal_id, 'submittals');
        return true;
    }

    public static function initiateWorkflow($submittal_id, $type = 'sequential', $approvers = [], $lang = 'en') {
        // Simulation: Setup the review chain
        $msg = ($lang === 'es') 
            ? "Iniciando flujo $type para Submittal #$submittal_id con ".count($approvers)." revisores." 
            : "Initiating $type workflow for Submittal #$submittal_id with ".count($approvers)." reviewers.";
        error_log($msg);
        
        ActivityLog::log('System', 'Started '.ucfirst($type).' Workflow for Submittal #'.$submittal_id, 'Inició flujo '.ucfirst($type).' para Submittal #'.$submittal_id, $submittal_id, 'submittals');
        return true;
    }
}
