<?php
// src/DrawingProcessor.php

class DrawingProcessor {
    public static function extractMetadata($file_path, $lang = 'en') {
        // Simulation: OCR extraction of sheet number and title
        $msg = ($lang === 'es') ? "Procesando plano con OCR... Título detectado." : "Processing drawing with OCR... Title detected.";
        error_log($msg);
        
        return [
            'sheet_number' => 'A-201',
            'sheet_title' => $lang === 'es' ? 'Planta de Elevación Norte' : 'North Elevation Plan',
            'discipline' => 'Architectural',
            'confidence' => 0.98
        ];
    }
}
