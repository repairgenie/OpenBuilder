<?php
// src/SpecManager.php

class SpecManager {
    public static function getSections($lang = 'en') {
        // Simulation: Return a list of MasterFormat sections with bilingual names
        return [
            ['code' => '03-3000', 'name' => $lang === 'es' ? 'Hormigón Moldeado in Situ' : 'Cast-in-Place Concrete'],
            ['code' => '05-1200', 'name' => $lang === 'es' ? 'Acero Estructural' : 'Structural Steel'],
            ['code' => '09-2900', 'name' => $lang === 'es' ? 'Paneles de Yeso' : 'Gypsum Board']
        ];
    }
}
