<?php
// src/ProjectAPI.php

class ProjectAPI {
    public static function getProjects($lang = 'en') {
        return [
            [
                'id' => 1,
                'name' => $lang === 'es' ? 'Torre Norte' : 'North Tower',
                'address' => '123 Construction Way',
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'name' => $lang === 'es' ? 'Residencias Oceanía' : 'Oceania Residences',
                'address' => '456 Beach Blvd',
                'status' => 'Active'
            ],
            [
                'id' => 3,
                'name' => $lang === 'es' ? 'Centro Logístico' : 'Logistics Center',
                'address' => '789 Industrial Pkwy',
                'status' => 'Planning'
            ]
        ];
    }
}
