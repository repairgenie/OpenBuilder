<?php
// src/Storage.php

class Storage {
    private static $driver = 'local'; // Options: local, s3, gcs

    public static function upload($file, $destination) {
        $full_path = __DIR__ . '/../storage/' . $destination;
        $dir = dirname($full_path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (self::$driver === 'local') {
            return move_uploaded_file($file['tmp_name'], $full_path);
        }
        
        // Simulation for Cloud Drivers
        error_log("Simulating Cloud Upload to " . self::$driver . " for $destination");
        return true;
    }

    public static function getUrl($path) {
        if (self::$driver === 'local') {
            return 'storage/' . $path;
        }
        return "https://cloud-storage-simulation.com/" . $path;
    }
}
