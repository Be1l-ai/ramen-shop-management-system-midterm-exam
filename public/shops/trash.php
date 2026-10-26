<?php 
require_once __DIR__ . '/../../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: ../index.php");
    exit;
}

$deletedShops = getDeletedShops($pdo);

$errorMessages = [
    'restore_failed' => 'The shop could not be restored. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '../';
$navActive = 'shops_trash';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Shops - Trash</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container">
        <h1>Deleted Shops (Trash)</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (empty($deletedShops)): ?>
            <p>No deleted shops found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($deletedShops as $shop): ?>
                <tr>
                    <td><?php echo $shop['id']; ?></td>
                    <td><?php echo $shop['name']; ?></td>
                    <td><?php echo $shop['description']; ?></td>
                    <td><?php echo $shop['deleted_at']; ?></td>
                    <td class="actions">
                        <form action="../handleForms.php?id=<?php echo $shop['id']; ?>" method="POST" style="display: inline;">
                            <input type="submit" name="restoreShopBtn" value="Restore" class="btn btn-success" onclick="return confirm('Restore this shop?')">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <p><a href="../index.php" class="btn btn-secondary">Back to Dashboard</a></p>
    </div>
</body>
</html>
