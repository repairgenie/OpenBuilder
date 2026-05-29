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

    public static function store($tmp_name, $original_name, $folder = 'documents') {
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $basename = pathinfo($original_name, PATHINFO_FILENAME);
        $stored_name = $basename . '_' . time() . '.' . $ext;
        $relative_path = $folder . '/' . $stored_name;
        $full_path = __DIR__ . '/../storage/' . $relative_path;
        $dir = dirname($full_path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (self::$driver === 'local') {
            if (move_uploaded_file($tmp_name, $full_path)) {
                return ['path' => $relative_path, 'url' => self::getUrl($relative_path)];
            }
            return null;
        }

        error_log("Simulating Cloud Store to " . self::$driver . " for $folder/$stored_name");
        return ['path' => $relative_path, 'url' => "https://cloud-storage-simulation.com/$relative_path"];
    }
}
