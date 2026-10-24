<?php

require '../config/db.php';
checkAdmin($pdo);
$msg = '';

// add new slot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slot'])) {
    $date = $_POST['slot_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $cap = (int) $_POST['capacity'];

    if ($cap > 0 && $end > $start) {
        $stmt = $pdo->prepare("INSERT INTO time_slots (slot_date, start_time, end_time, capacity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$date, $start, $end, $cap]);
        header("Location: admin_slots.php");
        exit;
    } else {
        $msg = "Invalid time range or capacity.";
    }
}

// delete slot if no pending/confirmed 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = (int) $_POST['delete_id'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE slot_id = ? AND status IN ('Pending', 'Confirmed')");
    $check->execute([$delId]);

    if ($check->fetchColumn() > 0) {
        $msg = "Cannot delete: This slot has Pending or Confirmed reservations!";
    } else {
        $pdo->prepare("DELETE FROM reservations WHERE slot_id = ?")->execute([$delId]);
        $pdo->prepare("DELETE FROM time_slots WHERE id = ?")->execute([$delId]);
        header("Location: admin_slots.php");
        exit;
    }
}

$slots = $pdo->query("SELECT * FROM time_slots ORDER BY slot_date, start_time")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Time Slots</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php showNav(); ?>
    <div class="container">
        <h2>Manage Time Slots</h2>
        <?php if ($msg): ?>
            <p class="alert"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

        <form method="POST" class="card" style="margin-bottom: 20px;">
            <h3>Add New Time Slot</h3>
            <label>Date</label>
            <input type="date" name="slot_date" required>
            <label>Start Time</label>
            <input type="time" name="start_time" required>
            <label>End Time</label>
            <input type="time" name="end_time" required>
            <label>Capacity</label>
            <input type="number" name="capacity" min="1" value="3" required>
            <button type="submit" name="add_slot">Add Slot</button>
        </form>

        <table>
            <tr>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Capacity</th>
                <th>Action</th>
            </tr>
            <?php foreach ($slots as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['slot_date']) ?></td>
                    <td><?= htmlspecialchars($s['start_time']) ?></td>
                    <td><?= htmlspecialchars($s['end_time']) ?></td>
                    <td><?= $s['capacity'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>