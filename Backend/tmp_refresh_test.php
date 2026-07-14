<?php

function request(string $method, string $url, array $body = [], array $headers = []): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json'], $headers));
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return [
        'status' => $info['http_code'],
        'body' => $response,
    ];
}

$base = 'http://127.0.0.1:8000/api/v1/auth';
$email = 'test+' . time() . '@example.com';
$password = 'SecurePass123!@';

$register = request('POST', "$base/register", [
    'name' => 'Postman Tester',
    'email' => $email,
    'password' => $password,
    'password_confirmation' => $password,
    'org_id' => 1,
    'role' => 'coordinator',
]);
echo "REGISTER status: {$register['status']}\n";
echo "REGISTER body: {$register['body']}\n\n";

$login = request('POST', "$base/login", [
    'email' => $email,
    'password' => $password,
]);
echo "LOGIN status: {$login['status']}\n";
echo "LOGIN body: {$login['body']}\n\n";

$loginJson = json_decode($login['body'], true);
if (!isset($loginJson['access_token'])) {
    exit(1);
}

$token = $loginJson['access_token'];

$me = request('GET', "$base/me", [], ["Authorization: Bearer $token"]);
echo "ME status: {$me['status']}\n";
echo "ME body: {$me['body']}\n\n";

$refresh = request('POST', "$base/refresh", ['refresh_token' => 'dummy-refresh'], ["Authorization: Bearer $token"]);
echo "REFRESH status: {$refresh['status']}\n";
echo "REFRESH body: {$refresh['body']}\n\n";

$refreshJson = json_decode($refresh['body'], true);
$refreshToken = $refreshJson['access_token'] ?? null;
if (!$refreshToken) {
    exit(1);
}

echo "NEW ACCESS TOKEN: {$refreshToken}\n\n";

$logout = request('POST', "$base/logout", [], ["Authorization: Bearer $refreshToken"]);
echo "LOGOUT status: {$logout['status']}\n";
echo "LOGOUT body: {$logout['body']}\n\n";

$meAfterLogout = request('GET', "$base/me", [], ["Authorization: Bearer $refreshToken"]);
echo "ME AFTER LOGOUT status: {$meAfterLogout['status']}\n";
echo "ME AFTER LOGOUT body: {$meAfterLogout['body']}\n";
