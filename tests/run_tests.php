<?php
// tests/run_tests.php

require_once __DIR__ . '/../src/WeatherProvider.php';
require_once __DIR__ . '/../src/AIProvider.php';

function test_weather_provider() {
    echo "Running WeatherProvider tests...\n";
    $wp = new WeatherProvider();
    
    $en = $wp->getWeather('en');
    assert(isset($en['temp']), "EN weather missing temp");
    assert(in_array($en['condition'], ['Sunny', 'Partly Cloudy', 'Overcast', 'Light Rain']), "Invalid EN condition");
    
    $es = $wp->getWeather('es');
    assert(in_array($es['condition'], ['Soleado', 'Parcialmente Nublado', 'Nublado', 'Lluvia Ligera']), "Invalid ES condition");
    
    echo "WeatherProvider tests PASSED.\n";
}

function test_ai_provider_prompts() {
    echo "Running AIProvider prompt tests...\n";
    $ai = new AIProvider('mock_key');
    
    // Using Reflection to test private method getPrompt
    $reflector = new ReflectionClass('AIProvider');
    $method = $reflector->getMethod('getPrompt');
    $method->setAccessible(true);
    
    $prompt_en = $method->invokeArgs($ai, ['Testing notes', '', 'en']);
    assert(str_contains($prompt_en, 'ENGLISH'), "EN prompt should mention ENGLISH");
    
    $prompt_es = $method->invokeArgs($ai, ['Notas de prueba', '', 'es']);
    assert(str_contains($prompt_es, 'ESPAÑOL'), "ES prompt should mention ESPAÑOL");
    
    echo "AIProvider tests PASSED.\n";
}

try {
    test_weather_provider();
    test_ai_provider_prompts();
    echo "\nAll backend tests PASSED.\n";
} catch (Exception $e) {
    echo "\nTest FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
