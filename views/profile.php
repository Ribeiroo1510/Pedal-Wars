<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Verifica se está visualizando o perfil de outro usuário
$viewing_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];
$is_own_profile = ($viewing_user_id === $_SESSION['user_id']);

// Carrega os dados do usuário que está sendo visualizado
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$viewing_user_id]);
$profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile_user) {
    header('Location: dashboard.php');
    exit;
}

// Se for o próprio perfil e não tiver dados na sessão, carrega do banco
if ($is_own_profile && (!isset($_SESSION['city']) || !isset($_SESSION['country']) || !isset($_SESSION['weight']))) {
    $_SESSION['city'] = $profile_user['city'];
    $_SESSION['country'] = $profile_user['country'];
    $_SESSION['weight'] = $profile_user['weight'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/header.css">
    <link rel="stylesheet" href="../public/assets/css/sidebar.css">
    <link rel="stylesheet" href="../public/assets/profile.css">
</head>
<body>
    <?php include "partials/header.php" ?>
    <?php include "partials/sidebar.php" ?>

    <main id="mainContent" class="main-content">
        <section>
            <div class="profile-top">
                <h1><?php echo $is_own_profile ? 'Profile' : htmlspecialchars($profile_user['firstname'] . "'s Profile") ?></h1>
                <h4><?php echo $is_own_profile ? 'Your personal statistics and information' : 'Statistics and information' ?></h4>
                <?php if (!$is_own_profile): ?>
                    <a href="dashboard.php" class="back-btn">← Voltar ao Dashboard</a>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <div class="profile-card">
                    <img src="<?php echo htmlspecialchars($profile_user['profile']) ?>" alt="profile_photo" class="profile-photo">
                    <div class="profile-details">
                        <h3><?php echo htmlspecialchars($profile_user['firstname']) ?> <?php echo htmlspecialchars($profile_user['lastname']) ?></h3>
                        <p>📍 <?php echo htmlspecialchars($profile_user['city']) ?>, <?php echo htmlspecialchars($profile_user['state']) ?>, <?php echo htmlspecialchars($profile_user['country']) ?></p>
                        <?php if ($profile_user['weight']): ?>
                            <p>⚖️ <?php echo htmlspecialchars($profile_user['weight']) ?> kg</p>
                        <?php endif; ?>
                        <?php
                            if ($profile_user['sex'] === 'M') {
                                echo '<p>👤 Masculino</p>';
                            } elseif ($profile_user['sex'] === 'F') {
                                echo '<p>👤 Feminino</p>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <div class="stats-title">
                    <h2>Overall Statistics</h2>
                    <h4>Your complete cycling journey</h4>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Activities</h3>
                        <p class="stat-value"><?php echo htmlspecialchars($profile_user['total_activities']) ?></p>
                        <p class="stat-label">rides</p>
                    </div>

                    <div class="stat-card">
                        <h3>Total Distance</h3>
                        <p class="stat-value"><?php echo htmlspecialchars(round($profile_user['total_distance'] / 1000, 2)) ?></p>
                        <p class="stat-label">km</p>
                    </div>

                    <div class="stat-card">
                        <h3>Total Time</h3>
                        <?php
                            $total_hours = floor($profile_user['total_time'] / 3600);
                            $total_minutes = floor(($profile_user['total_time'] % 3600) / 60);
                        ?>
                        <p class="stat-value"><?php echo htmlspecialchars($total_hours) ?>h <?php echo htmlspecialchars($total_minutes) ?>m</p>
                        <p class="stat-label">hours</p>
                    </div>
                </div>

                <div class="period-stats">
                    <div class="period-card">
                        <h3>This Month</h3>
                        <div class="period-stats-grid">
                            <div class="period-stat">
                                <p class="period-value"><?php echo htmlspecialchars($profile_user['month_activities']) ?></p>
                                <p class="period-label">activities</p>
                            </div>
                            <div class="period-stat">
                                <p class="period-value"><?php echo htmlspecialchars(round($profile_user['month_distance'] / 1000, 2)) ?></p>
                                <p class="period-label">km</p>
                            </div>
                            <div class="period-stat">
                                <?php
                                    $month_hours = floor($profile_user['month_time'] / 3600);
                                    $month_minutes = floor(($profile_user['month_time'] % 3600) / 60);
                                ?>
                                <p class="period-value"><?php echo htmlspecialchars($month_hours) ?>h <?php echo htmlspecialchars($month_minutes) ?>m</p>
                                <p class="period-label">time</p>
                            </div>
                            <div class="period-stat">
                                <?php if ($profile_user['month_time'] === 0): ?>
                                    <p class="period-value">0</p>
                                <?php else: ?>
                                    <p class="period-value"><?php echo htmlspecialchars(round($profile_user['month_distance'] / ($month_hours + ($month_minutes / 60)), 1)) ?></p>
                                <?php endif; ?>
                                <p class="period-label">avg km/h</p>
                            </div>
                        </div>
                    </div>

                    <div class="period-card">
                        <h3>This Week</h3>
                        <div class="period-stats-grid">
                            <div class="period-stat">
                                <p class="period-value"><?php echo htmlspecialchars($profile_user['week_activities']) ?></p>
                                <p class="period-label">activities</p>
                            </div>
                            <div class="period-stat">
                                <p class="period-value"><?php echo htmlspecialchars(round($profile_user['week_distance'] / 1000, 2)) ?></p>
                                <p class="period-label">km</p>
                            </div>
                            <div class="period-stat">
                                <?php
                                    $week_hours = floor($profile_user['week_time'] / 3600);
                                    $week_minutes = floor(($profile_user['week_time'] % 3600) / 60);
                                ?>
                                <p class="period-value"><?php echo htmlspecialchars($week_hours) ?>h <?php echo htmlspecialchars($week_minutes) ?>m</p>
                                <p class="period-label">time</p>
                            </div>
                            <div class="period-stat">
                                <?php if ($profile_user['week_time'] === 0): ?>
                                    <p class="period-value">0</p>
                                <?php else: ?>
                                    <p class="period-value"><?php echo htmlspecialchars(round($profile_user['week_distance'] / ($week_hours + ($week_minutes / 60)), 1)) ?></p>
                                <?php endif; ?>
                                <p class="period-label">avg km/h</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/search.js"></script>
</body>
</html>