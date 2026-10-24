<?php

require 'config/db.php';
checkLogin($pdo);
$msg = '';

// add new reservation 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_id'])) {
    $slotId = (int) $_POST['slot_id'];
    $note = trim($_POST['note']);
    $userId = $_SESSION['user_id'];

    // slot info
    $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE id = ?");
    $stmt->execute([$slotId]);
    $slot = $stmt->fetch(PDO::FETCH_ASSOC);

    // active bookings for this slot
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE slot_id = ? AND status IN ('Pending', 'Confirmed')");
    $cnt->execute([$slotId]);
    $booked = $cnt->fetchColumn();

    // check if user already booked this slot
    $dup = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND slot_id = ? AND status IN ('Pending', 'Confirmed')");
    $dup->execute([$userId, $slotId]);

    if (!$slot || strtotime($slot['slot_date'] . ' ' . $slot['start_time']) <= time()) {
        $msg = "Error: Cannot book a slot in the past.";
    } elseif ($booked >= $slot['capacity']) {
        $msg = "Error: This slot is full.";
    } elseif ($dup->fetch()) {
        $msg = "Error: You already booked this slot.";
    } else {
        $ins = $pdo->prepare("INSERT INTO reservations (user_id, slot_id, note, status) VALUES (?, ?, ?, 'Pending')");
        $ins->execute([$userId, $slotId, $note]);
        header("Location: my_reservations.php");
        exit;
    }
}

// fetch future Slots
$dateFilter = $_GET['date'] ?? '';
if ($dateFilter !== '') {
    $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE slot_date = ? AND TIMESTAMP(slot_date, start_time) > NOW() ORDER BY slot_date, start_time");
    $stmt->execute([$dateFilter]);
} else {
    $stmt = $pdo->query("SELECT * FROM time_slots WHERE TIMESTAMP(slot_date, start_time) > NOW() ORDER BY slot_date, start_time");
}
$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Coaching Slots</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php showNav(); ?>
    <div class="container">
        <h2>Available Coaching Time Slots</h2>
        <?php if ($msg): ?>
            <p class="alert"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

        <form method="GET" class="filter-form">
            <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
            <button type="submit">Filter by Date</button>
            <a href="index.php">Show All</a>
        </form>

        <div class="grid">
            <?php foreach ($slots as $s): ?>
                <?php
                // Calculate remaining capacity for this slot
                $c = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE slot_id = ? AND status IN ('Pending', 'Confirmed')");
                $c->execute([$s['id']]);
                $remaining = $s['capacity'] - $c->fetchColumn();
                ?>
                <div class="card">
                    <h3>Date: <?= htmlspecialchars($s['slot_date']) ?></h3>
                    <p>Time: <?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></p>
                    <p>Remaining Capacity: <strong><?= $remaining ?> / <?= $s['capacity'] ?></strong></p>

                    <?php if ($remaining > 0): ?>
                        <form method="POST">
                            <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                            <input type="text" name="note" placeholder="Optional coaching note...">
                            <button type="submit">Reserve Slot</button>
                        </form>
                    <?php else: ?>
                        <button disabled>Slot Full</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>