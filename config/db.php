<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=coaching_db;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function checkLogin($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    // business rule: deactivated users cannot use the system
    $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetchColumn() == 0) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// is it admin?
function checkAdmin($pdo)
{
    checkLogin($pdo);
    if ($_SESSION['role'] !== 'admin') {
        die("Access Denied: Admins only. <a href='index.php'>Go Back</a>");
    }
}

// navbar
function showNav()
{
    echo '<nav class="navbar"><div class="nav-inner">';
    echo '<strong>Career Coaching</strong><div>';
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'admin') {
            echo '<a href="admin_dashboard.php">Dashboard</a>';
            echo '<a href="admin_reservations.php">Reservations</a>';
            echo '<a href="admin_slots.php">Time Slots</a>';
            echo '<a href="admin_users.php">Users</a>';
        } else {
            echo '<a href="index.php">Available Slots</a>';
            echo '<a href="my_reservations.php">My Reservations</a>';
        }
        echo '<a href="logout.php">Logout (' . htmlspecialchars($_SESSION['name']) . ')</a>';
    }
    else {
        echo '<a href="login.php">Login</a>';
        echo '<a href="register.php">Register</a>';
    }
    echo '</div></div></nav>';
}