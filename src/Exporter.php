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
}
