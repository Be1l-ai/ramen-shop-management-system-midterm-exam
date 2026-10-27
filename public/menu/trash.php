<?php 
require_once __DIR__ . '/../../src/bootstrap.php';

$user = requireAuth($pdo);

$shopId = $_GET['shop_id'] ?? null;
$shop = $shopId ? getRamenShopByID($pdo, $shopId) : null;

if ($shopId && !$shop) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

if (!isAdmin($pdo) && (!$shopId || !canManageShop($pdo, $shopId))) {
    header("Location: " . BASE_URL . "index.php?error=forbidden");
    exit;
}

$deletedItems = getDeletedMenuItems($pdo, $shopId);

$errorMessages = [
    'restore_failed' => 'The menu item could not be restored. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '../';
$navActive = 'menu_trash';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Menu Items - Trash</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container">
        <h1><?php echo $shop ? 'Deleted Menu Items for ' . htmlspecialchars($shop['name']) : 'Deleted Menu Items (All Shops)'; ?> (Trash)</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (empty($deletedItems)): ?>
            <p>No deleted menu items found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <?php if (!$shop): ?>
                        <th>Shop</th>
                    <?php endif; ?>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($deletedItems as $item): ?>
                <tr>
                    <?php if (!$shop): ?>
                        <td><?php echo $item['shop_name']; ?></td>
                    <?php endif; ?>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['description']; ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['deleted_at']; ?></td>
                    <td class="actions">
                        <form action="../handleForms.php?id=<?php echo $item['id']; ?>" method="POST" style="display: inline;">
                            <input type="submit" name="restoreMenuItemBtn" value="Restore" class="btn btn-success" onclick="return confirm('Restore this menu item?')">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <p><a href="<?php echo $shop ? '../index.php?shop_id=' . $shop['id'] : '../index.php'; ?>" class="btn btn-secondary">Back to Dashboard</a></p>
    </div>
</body>
</html>
