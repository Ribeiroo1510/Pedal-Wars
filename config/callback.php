<?php
session_start();
require_once 'db_connect.php';

// Credenciais do Strava
$clientId = '157874';
$clientSecret = 'cdece55f2cd978fe1f8ba174207261da6de4e1a8';
$redirectUri = 'http://localhost:8080/pedal_wars/config/callback.php';

if (!isset($_GET['code'])) {
    die("Código de autorização não recebido");
}

$code = $_GET['code'];

// Troca o código pelo token
$tokenUrl = 'https://www.strava.com/oauth/token';
$data = [
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'code' => $code,
    'grant_type' => 'authorization_code'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($tokenUrl, false, $context);
$tokenData = json_decode($result, true);

if (!isset($tokenData['access_token'])) {
    die("Erro ao obter token de acesso do Strava");
}

$accessToken = $tokenData['access_token'];

// === Obtém dados do atleta ===
$athleteUrl = 'https://www.strava.com/api/v3/athlete';
$options = [
    'http' => [
        'header' => "Authorization: Bearer $accessToken\r\n"
    ]
];
$context = stream_context_create($options);
$athleteResult = file_get_contents($athleteUrl, false, $context);
$athleteData = json_decode($athleteResult, true);

// === Obtém estatísticas gerais e últimos 28 dias ===
$statsUrl = 'https://www.strava.com/api/v3/athletes/' . $athleteData['id'] . '/stats';
$statsResult = file_get_contents($statsUrl, false, $context);
$statsData = json_decode($statsResult, true);

// Totais gerais
$totalActivities = $statsData['all_ride_totals']['count'] ?? 0;
$totalDistance = $statsData['all_ride_totals']['distance'] ?? 0;
$totalTime = $statsData['all_ride_totals']['moving_time'] ?? 0;

// Últimos 28 dias
$lastMonthActivities = $statsData['recent_ride_totals']['count'] ?? 0;
$lastMonthDistance = $statsData['recent_ride_totals']['distance'] ?? 0;
$lastMonthTime = $statsData['recent_ride_totals']['moving_time'] ?? 0;

// === Calcula última semana ===
$weekActivities = 0;
$weekDistance = 0;
$weekTime = 0;

$activitiesUrl = 'https://www.strava.com/api/v3/athlete/activities?per_page=200&page=1';
$activitiesResult = file_get_contents($activitiesUrl, false, $context);
$activities = json_decode($activitiesResult, true);

$oneWeekAgo = strtotime('-7 days');

foreach ($activities as $act) {
    $actTime = strtotime($act['start_date']);
    if ($actTime >= $oneWeekAgo) {
        $weekActivities++;
        $weekDistance += $act['distance'];
        $weekTime += $act['moving_time'];
    }
}

try {
    // Verifica se o usuário já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE strava_id = ?");
    $stmt->execute([$athleteData['id']]);
    $user = $stmt->fetch();

    if ($user) {
        // Atualiza usuário existente
        $stmt = $pdo->prepare("UPDATE users SET 
            firstname = ?, 
            lastname = ?, 
            profile_medium = ?, 
            profile = ?, 
            city = ?, 
            country = ?,
            state = ?, 
            weight = ?,
            sex = ?,
            total_activities = ?,
            total_distance = ?,
            total_time = ?,
            month_activities = ?,
            month_distance = ?,
            month_time = ?,
            week_activities = ?,
            week_distance = ?,
            week_time = ?,
            access_token = ?, 
            refresh_token = ?, 
            expires_at = ? 
            WHERE strava_id = ?");
        
        $stmt->execute([
            $athleteData['firstname'],
            $athleteData['lastname'],
            $athleteData['profile_medium'],
            $athleteData['profile'],
            $athleteData['city'] ?? null,
            $athleteData['country'] ?? null,
            $athleteData['state'] ?? null,
            $athleteData['weight'] ?? null,
            $athleteData['sex'] ?? null,
            $totalActivities,
            $totalDistance,
            $totalTime,
            $lastMonthActivities,
            $lastMonthDistance,
            $lastMonthTime,
            $weekActivities,
            $weekDistance,
            $weekTime,
            $accessToken,
            $tokenData['refresh_token'],
            $tokenData['expires_at'],
            $athleteData['id']
        ]);

        $userId = $user['id'];
    } else {
        // Insere novo usuário
        $stmt = $pdo->prepare("INSERT INTO users (
            strava_id, firstname, lastname, profile_medium, profile, city, country, state, weight, sex,
            total_activities, total_distance, total_time,
            month_activities, month_distance, month_time,
            week_activities, week_distance, week_time,
            access_token, refresh_token, expires_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $athleteData['id'],
            $athleteData['firstname'],
            $athleteData['lastname'],
            $athleteData['profile_medium'],
            $athleteData['profile'],
            $athleteData['city'] ?? null,
            $athleteData['country'] ?? null,
            $athleteData['state'] ?? null,
            $athleteData['weight'] ?? null,
            $athleteData['sex'] ?? null,
            $totalActivities,
            $totalDistance,
            $totalTime,
            $lastMonthActivities,
            $lastMonthDistance,
            $lastMonthTime,
            $weekActivities,
            $weekDistance,
            $weekTime,
            $accessToken,
            $tokenData['refresh_token'],
            $tokenData['expires_at']
        ]);

        $userId = $pdo->lastInsertId();
    }

    // Define sessão
    $_SESSION['user_id'] = $userId;
    $_SESSION['strava_id'] = $athleteData['id'];
    $_SESSION['firstname'] = $athleteData['firstname'];
    $_SESSION['lastname'] = $athleteData['lastname'];
    $_SESSION['profile'] = $athleteData['profile'];
    $_SESSION['city'] = $athleteData['city'] ?? 'Não informado';
    $_SESSION['country'] = $athleteData['country'] ?? 'Não informado';
    $_SESSION['state'] = $athleteData['state'] ?? 'Não informado';
    $_SESSION['weight'] = $athleteData['weight'] ?? 'Não informado';
    $_SESSION['sex'] = $athleteData['sex'] ?? 'Não informado';
    $_SESSION['total_activities'] = $totalActivities;
    $_SESSION['total_distance'] = $totalDistance;
    $_SESSION['total_time'] = $totalTime;
    $_SESSION['month_activities'] = $lastMonthActivities;
    $_SESSION['month_distance'] = $lastMonthDistance;
    $_SESSION['month_time'] = $lastMonthTime;
    $_SESSION['week_activities'] = $weekActivities;
    $_SESSION['week_distance'] = $weekDistance;
    $_SESSION['week_time'] = $weekTime;
    $_SESSION['access_token'] = $accessToken;

    header('Location: ../views/dashboard.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar dados do usuário: " . $e->getMessage());
}
