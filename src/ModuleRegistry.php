<?php
// src/ModuleRegistry.php

class ModuleRegistry {
    private static $modules = [];
    private static $config_file = __DIR__ . '/modules.json';

    public static function init() {
        if (file_exists(self::$config_file)) {
            self::$modules = json_decode(file_get_contents(self::$config_file), true);
        } else {
            // Default Core Modules
            self::$modules = [
                'dashboard' => ['enabled' => true, 'name_en' => 'Dashboard', 'name_es' => 'Panel'],
                'rfis'      => ['enabled' => true, 'name_en' => 'RFIs',      'name_es' => 'RFIs'],
                'daily_logs' => ['enabled' => true, 'name_en' => 'Daily Logs', 'name_es' => 'Diarios'],
                'budget'    => ['enabled' => true, 'name_en' => 'Budget',    'name_es' => 'Presupuesto'],
                'tasks'     => ['enabled' => true, 'name_en' => 'Tasks',     'name_es' => 'Tareas'],
                'admin'     => ['enabled' => true, 'name_en' => 'Admin',     'name_es' => 'Admin']
            ];
            self::save();
        }
    }

    public static function isEnabled($module) {
        return self::$modules[$module]['enabled'] ?? false;
    }

    public static function toggle($module, $state) {
        if (isset(self::$modules[$module])) {
            self::$modules[$module]['enabled'] = (bool)$state;
            self::save();
        }
    }

    public static function getModules() {
        return self::$modules;
    }

    private static function save() {
        file_put_contents(self::$config_file, json_encode(self::$modules, JSON_PRETTY_PRINT));
    }
}
