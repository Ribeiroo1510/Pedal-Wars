<?php
$host = '127.0.0.1';
$dbname = 'strava_users';
$username = 'root';
$password = 'diogomiguel';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Cria a tabela se não existir
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    strava_id BIGINT NOT NULL UNIQUE,
    firstname VARCHAR(50),
    lastname VARCHAR(50),
    profile_medium TEXT,
    profile TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    state VARCHAR(100),
    weight DECIMAL(5,2),
    sex VARCHAR(100),
    total_activities INT,
    total_distance DECIMAL(10,2),
    total_time INT UNSIGNED DEFAULT 0,
    month_activities INT,
    month_distance DECIMAL(10,2),
    month_time INT UNSIGNED DEFAULT 0,
    week_activities INT,
    week_distance DECIMAL(10,2),
    week_time INT UNSIGNED DEFAULT 0,
    access_token TEXT,
    refresh_token TEXT,
    expires_at INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$pdo->exec($query);

// Adiciona o campo country se não existir (para tabelas existentes)
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN country VARCHAR(100)");
} catch (PDOException $e) {
    // Campo já existe, ignora o erro
}

// Adiciona o campo weight se não existir (para tabelas existentes)
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN weight DECIMAL(5,2)");
} catch (PDOException $e) {
    // Campo já existe, ignora o erro
}
?>