<?php
// src/LinkEngine.php

class LinkEngine {
    public static function detectLinks($drawing_id, $lang = 'en') {
        // Simulation: Detect RFI numbers in drawings and generate spatial links
        $msg = ($lang === 'es') ? "Detectando referencias a RFI y Submittals en el plano #$drawing_id." : "Detecting RFI and Submittal references in drawing #$drawing_id.";
        error_log($msg);
        
        return [
            ['type' => 'rfi', 'ref' => 'RFI-042', 'coords' => [120, 350], 'title' => $lang === 'es' ? 'Detalle de Cimentación' : 'Foundation Detail'],
            ['type' => 'submittal', 'ref' => 'SUB-015', 'coords' => [800, 150], 'title' => $lang === 'es' ? 'Especificación de Concreto' : 'Concrete Spec']
        ];
    }
}
