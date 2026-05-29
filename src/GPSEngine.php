<?php
// src/GPSEngine.php

class GPSEngine {
    /**
     * Validate a lat/lon pair.
     * @return bool
     */
    public static function isValidCoords($lat, $lon) {
        return is_numeric($lat) && is_numeric($lon)
            && $lat >= -90 && $lat <= 90
            && $lon >= -180 && $lon <= 180;
    }

    /**
     * Format a GPS stamp string from lat/lon.
     * @return string e.g. "37.7749,-122.4194"
     */
    public static function formatStamp($lat, $lon) {
        if (!self::isValidCoords($lat, $lon)) return '';
        return sprintf('%.6f,%.6f', $lat, $lon);
    }

    /**
     * Get location string for a given lat/lon (reverse geocode stub).
     * In production this would call a geocoding API.
     * For now, returns a formatted coordinate string with Google Maps link.
     */
    public static function getLocationString($lat, $lon) {
        if (!self::isValidCoords($lat, $lon)) {
            return ($_GET['lang'] ?? 'en') === 'es' ? 'Ubicacion desconoida' : 'Unknown location';
        }
        return sprintf('<a href="https://www.google.com/maps?q=%.6f,%.6f" target="_blank" class="text-primary hover:underline">%.4f, %.4f</a>', $lat, $lon, $lat, $lon);
    }

    /**
     * Get nearby drawing links for given coordinates (spatial query stub).
     * In a real implementation this would query a spatial index of drawings.
     * @return array
     */
    public static function getDrawingAtLocation($lat, $lon) {
        if (!self::isValidCoords($lat, $lon)) return [];
        // Placeholder spatial query — return empty for now (real impl needs spatial index)
        return [];
    }
}