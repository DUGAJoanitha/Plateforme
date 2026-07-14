<?php

echo "Starting Smoke Tests...\n";

$baseUrl = 'http://localhost:8000/api';
$endpoints = [
    '/health' => 200,
];

$allPassed = true;

foreach ($endpoints as $path => $expectedCode) {
    $url = $baseUrl . $path;
    echo "Testing {$url}...\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === $expectedCode) {
        echo "✅ SUCCESS: {$path} returned {$httpCode}\n";
    } else {
        echo "❌ FAILED: {$path} returned {$httpCode} (Expected: {$expectedCode})\n";
        $allPassed = false;
    }
    
    curl_close($ch);
}

if ($allPassed) {
    echo "All Smoke Tests Passed.\n";
    exit(0);
} else {
    echo "Smoke Tests Failed.\n";
    exit(1);
}
