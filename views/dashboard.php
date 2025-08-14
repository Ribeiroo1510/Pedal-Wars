<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Verifica se a cidade, país ou peso não estão na sessão e carrega do banco de dados
if (!isset($_SESSION['city']) || !isset($_SESSION['country']) || !isset($_SESSION['weight'])) {
    $stmt = $pdo->prepare("SELECT city, country, weight FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/assets/css/btn.css">
    <link rel="stylesheet" href="../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="../public/assets/css/sidebar.css">
    <link rel="stylesheet" href="../public/assets/header.css">
</head>

<body>
    <?php include "partials/header.php" ?>
    <?php include "partials/sidebar.php" ?>

    <main id="mainContent" class="main-content">
        <section>
            <div class="dashboard-top">
                <h1>Dashboard</h1>
                <h4>Track your progress and compete with friends</h4>
            </div>

            <div class="dashboard-bottom">
                <div class="container profile-container">
                    <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" alt="profile_photo" width="100" height="100" class="profile-photo">
                    <div class="profile">
                        <h3>Profile</h3>
                        <h4><?php echo htmlspecialchars($_SESSION['firstname']) ?> <?php echo htmlspecialchars($_SESSION['lastname']) ?></h4>
                        <p>Weekly KM: <?php echo htmlspecialchars($_SESSION['week_activities'] / 1000) ?></p>
                    </div>
                </div>

                <div class="container">
                    <img src="../public/assets/images/icons/trophy.png" alt="trophy_image" width="80" height="80">
                    <div class="active-challenges">
                        <h3 style="margin-top: 30px;">Active Challenges</h3>
                        Challenge your friends and see who can pedal more!
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="friends-top">
                <h1>Friends Rankings</h1>
                <h4>See how you stack up against the top 5 friends</h4>
                <a href="#" class="primary-btn">see all</a>
            </div>

            <div class="friends-ranking">
                <div class="row">
                    <div class="user col-md-3">
                        <div class="user-photo">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" width="80">
                        </div>

                        <div class="user-stats">
                            <p class="user-name">Robert</p>
                            <h4 class="user-distance">200 KM</h4>
                            <p class="diference">+10%</p>
                        </div>
                    </div>

                    <div class="user col-md-3">
                        <div class="user-photo">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" width="80">
                        </div>

                        <div class="user-stats">
                            <p class="user-name">Bob</p>
                            <h4 class="user-distance">180 KM</h4>
                            <p class="diference">+7%</p>
                        </div>
                    </div>

                    <div class="user col-md-3">
                        <div class="user-photo">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" width="80">
                        </div>

                        <div class="user-stats">
                            <p class="user-name">Charlie</p>
                            <h4 class="user-distance">170 KM</h4>
                            <p class="diference">+5%</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="user col-md-3">
                        <div class="user-photo">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" width="80">
                        </div>

                        <div class="user-stats">
                            <p class="user-name">Dana</p>
                            <h4 class="user-distance">160 KM</h4>
                            <p class="diference">+4%</p>
                        </div>
                    </div>

                    <div class="user col-md-3">
                        <div class="user-photo">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile']) ?>" width="80">
                        </div>

                        <div class="user-stats">
                            <p class="user-name">Eve</p>
                            <h4 class="user-distance">150 KM</h4>
                            <p class="diference">+2%</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../public/assets/js/sidebar.js"></script>
    <script src="../public/assets/js/search.js"></script>
    <script>
        document.querySelector('.profile-container').addEventListener('click', () => {
            window.location.href = 'profile.php';
        })
    </script>
</body>

</html>