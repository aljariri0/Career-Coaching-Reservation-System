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
function showNav($root = '')
{
    echo '<nav class="navbar"><div class="nav-inner">';
    echo '<strong>Career Coaching</strong><div>';
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'admin') {
            echo '<a href="' . $root . 'admin/dashboard.php">Dashboard</a>';
            echo '<a href="' . $root . 'admin/reservations.php">Reservations</a>';
            echo '<a href="' . $root . 'admin/slots.php">Time Slots</a>';
            echo '<a href="' . $root . 'admin/users.php">Users</a>';
        } else {
            echo '<a href="' . $root . 'index.php">Available Slots</a>';
            echo '<a href="' . $root . 'my_reservations.php">My Reservations</a>';
        }
        echo '<a href="' . $root . 'logout.php">Logout (' . htmlspecialchars($_SESSION['name']) . ')</a>';
    } else {
        echo '<a href="' . $root . 'login.php">Login</a>';
        echo '<a href="' . $root . 'register.php">Register</a>';
    }
    echo '</div></div></nav>';
}