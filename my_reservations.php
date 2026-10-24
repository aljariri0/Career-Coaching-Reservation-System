<?php

require 'config/db.php';
checkLogin($pdo);
$userId = $_SESSION['user_id'];

// cancel button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $resId = (int) $_POST['cancel_id'];

    // reservation belongs to this user
    $stmt = $pdo->prepare("
        SELECT r.*, t.slot_date, t.start_time
        FROM reservations r
        JOIN time_slots t ON r.slot_id = t.id
        WHERE r.id = ? AND r.user_id = ?
    ");
    $stmt->execute([$resId, $userId]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    // not started yet
    if ($res && strtotime($res['slot_date'] . ' ' . $res['start_time']) > time()) {
        $upd = $pdo->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
        $upd->execute([$resId, $userId]);
    }
    header("Location: my_reservations.php");
    exit;
}

// get slots
$stmt = $pdo->prepare("
    SELECT r.*, t.slot_date, t.start_time, t.end_time
    FROM reservations r
    JOIN time_slots t ON r.slot_id = t.id
    WHERE r.user_id = ?
    ORDER BY t.slot_date DESC
");
$stmt->execute([$userId]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Reservations</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php showNav(); ?>
    <div class="container">
        <h2>My Reservations</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Note</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($list as $row): ?>
                <?php
                $isFuture = strtotime($row['slot_date'] . ' ' . $row['start_time']) > time();
                $canCancel = $isFuture && ($row['status'] === 'Pending' || $row['status'] === 'Confirmed');
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['slot_date']) ?></td>
                    <td><?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?></td>
                    <td><?= htmlspecialchars($row['note']) ?></td>
                    <td><strong><?= htmlspecialchars($row['status']) ?></strong></td>
                    <td>
                        <?php if ($canCancel): ?>
                            <form method="POST">
                                <input type="hidden" name="cancel_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-danger">Cancel</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>