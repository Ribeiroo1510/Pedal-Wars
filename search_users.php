<?php
session_start();
require_once 'config/db_connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Verifica se foi enviado um termo de pesquisa
if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['query']);
$currentUserId = $_SESSION['user_id'];

try {
    // Pesquisa usuários por nome (firstname ou lastname) excluindo o usuário atual
    $stmt = $pdo->prepare("
        SELECT id, strava_id, firstname, lastname, profile_medium, city, country, 
               total_distance, total_activities
        FROM users 
        WHERE (firstname LIKE ? OR lastname LIKE ? OR CONCAT(firstname, ' ', lastname) LIKE ?) 
        AND id != ? 
        ORDER BY firstname ASC 
        LIMIT 10
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $currentUserId]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar os resultados
    $results = [];
    foreach ($users as $user) {
        $results[] = [
            'id' => $user['id'],
            'strava_id' => $user['strava_id'],
            'name' => trim($user['firstname'] . ' ' . $user['lastname']),
            'profile_image' => $user['profile_medium'] ?: '../public/assets/images/icons/user.png',
            'location' => trim(($user['city'] ?: '') . ($user['city'] && $user['country'] ? ', ' : '') . ($user['country'] ?: '')),
            'total_distance' => round($user['total_distance'] / 1000, 1), // Converter para KM
            'total_activities' => $user['total_activities']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($results);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na pesquisa: ' . $e->getMessage()]);
}
?>