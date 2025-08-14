<?php
session_start();
include "config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Erro ao recuperar dados do usuário: " . $e->getMessage();
}
?>
?>