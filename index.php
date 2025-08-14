<?php
session_start();
require_once 'config/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: views/dashboard.php');
    exit;
} else {
    header('Location: public/login.php');
    exit;
}
?>