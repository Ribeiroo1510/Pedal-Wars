<?php
session_start();

// Credenciais Strava
$clientId = '157874';
$redirectUri = 'http://localhost:8080/pedal_wars/config/callback.php';
$scope = 'read,profile:read_all,activity:read';

$authUrl = "https://www.strava.com/oauth/authorize?" . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => $scope,
    'approval_prompt' => 'auto'
]);

header('Location: ' . $authUrl);
exit;
?>