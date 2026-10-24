<?php

require '../config/db.php';
checkAdmin($pdo);

// update reservation status 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['res_id'], $_POST['status'])) {
    $upd = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $upd->execute([$_POST['status'], (int) $_POST['res_id']]);
    header("Location: admin/reservations.php");
    exit;
}

// Search & Filter (GET)
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "
    SELECT r.*, u.name, u.email, t.slot_date, t.start_time, t.end_time
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN time_slots t ON r.slot_id = t.id
    WHERE (u.name LIKE ? OR u.email LIKE ?)
";
$params = ["%$search%", "%$search%"];

if ($status !== '') {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY t.slot_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
    <?php showNav(); ?>
    <div class="container">
        <h2>Manage Reservations</h2>

        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search name or email"
                value="<?= htmlspecialchars($search) ?>">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Confirmed" <?= $status === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Date & Time</th>
                <th>Note</th>
                <th>Status</th>
                <th>Change Status</th>
            </tr>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['slot_date']) ?> (<?= htmlspecialchars($r['start_time']) ?>)</td>
                    <td><?= htmlspecialchars($r['note']) ?></td>
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="res_id" value="<?= $r['id'] ?>">
                            <button type="submit" name="status" value="Confirmed">Confirm</button>
                            <button type="submit" name="status" value="Rejected" class="btn-danger">Reject</button>
                            <button type="submit" name="status" value="Cancelled" class="btn-danger">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>