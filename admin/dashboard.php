<?php

require '../config/db.php';
checkAdmin($pdo);

$totalRes = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Confirmed'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Rejected'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Cancelled'")->fetchColumn();
$users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php showNav('../'); ?>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <div class="grid">
            <div class="card">
                <h3>Total Reservations</h3>
                <p class="big"><?= $totalRes ?></p>
            </div>
            <div class="card">
                <h3>Pending</h3>
                <p class="big"><?= $pending ?></p>
            </div>
            <div class="card">
                <h3>Confirmed</h3>
                <p class="big"><?= $confirmed ?></p>
            </div>
            <div class="card">
                <h3>Registered Users</h3>
                <p class="big"><?= $users ?></p>
            </div>
        </div>

        <div class="card" style="margin-top:20px; max-width:600px;">
            <h3>Reservations by Status</h3>
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('myChart'), {
            type: 'bar',
            data: {
                labels: ['Pending', 'Confirmed', 'Rejected', 'Cancelled'],
                datasets: [{
                    label: 'Reservations',
                    data: [<?= $pending ?>, <?= $confirmed ?>, <?= $rejected ?>, <?= $cancelled ?>],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#6b7280']
                }]
            }
        });
    </script>
</body>

</html>