<?php
// src/Exporter.php

class Exporter {
    public static function exportRFIs($rfis, $lang = 'en') {
        $headers = $lang === 'es' 
            ? ['Ref #', 'Asunto', 'Prioridad', 'Estado', 'Fecha']
            : ['Ref #', 'Subject', 'Priority', 'Status', 'Date'];

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="rfis_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rfis as $rfi) {
            fputcsv($output, [
                $rfi['ref_number'],
                $rfi['subject'],
                $rfi['priority'],
                $rfi['status'],
                $rfi['created_at']
            ]);
        }
        fclose($output);
        exit;
    }

    public static function exportBudget($codes, $lang = 'en') {
        $headers = $lang === 'es'
            ? ['Código', 'Nombre', 'Presupuesto', 'Gastado', 'Varianza']
            : ['Code', 'Name', 'Budget', 'Spent', 'Variance'];

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="budget_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($codes as $code) {
            $m = calculate_budget_metrics($code);
            fputcsv($output, [
                $code['code'],
                $code['name'],
                $m['revised_budget'],
                $code['committed_costs'],
                $m['variance']
            ]);
        }
        fclose($output);
        exit;
    }

    public static function exportDrawing($drawing_id, $markups, $lang = 'en') {
        // Simulation: Burn markups into PDF and force download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Drawing_'.$drawing_id.'_Marked.pdf"');
        echo "%PDF-1.4 [Simulation of Drawing Export with ".count($markups)." markups]";
        ActivityLog::log('System', 'Exported Drawing #'.$drawing_id.' with Markups', 'Exportó Plano #'.$drawing_id.' con Anotaciones', $drawing_id, 'drawings');
        exit;
    }

    public static function generateAsBuilts($drawing_ids, $lang = 'en') {
        // Simulation: Compile all drawings with final markups into a single ZIP/PDF
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="As-Built_Package_'.date('Y').'.zip"');
        echo "[Simulation of As-Built Package with ".count($drawing_ids)." drawings]";
        ActivityLog::log('System', 'Generated As-Built Package', 'Generó Paquete de Planos Conforme a Obra', count($drawing_ids), 'closeout');
        exit;
    }
}
