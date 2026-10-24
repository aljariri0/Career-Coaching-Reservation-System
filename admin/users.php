<?php

require '../config/db.php';
checkAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $uid = (int) $_POST['user_id'];
    if ($uid !== (int) $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        $stmt->execute([$uid]);
    }
    header("Location: admin_users.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php showNav(); ?>
    <div class="container">
        <h2>Manage Users</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($u['name']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($u['email']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($u['role']) ?>
                    </td>
                    <td>
                        <?= $u['is_active'] ? 'Active' : 'Deactivated' ?>
                    </td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit">
                                    <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>