<?php 
require_once __DIR__ . '/../src/bootstrap.php';

$user = requireAuth($pdo);

$id = $_GET['id'] ?? null;
if (!$id) { die('Item ID required'); }

$item = getMenuItemByID($pdo, $id);
if (!$item) { die('Menu item not found'); }

if (!canManageShop($pdo, $item['shop_id'])) {
    header("Location: ../index.php");
    exit;
}

$navBase = '../';
$navActive = 'dashboard';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Menu Item</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Delete Menu Item</h1>
        
        <div class="warning">
            <strong>Warning:</strong> This will soft delete <strong><?php echo $item['name']; ?></strong>.<br>
            Deleted items can be restored from the trash (admin only).
        </div>
        
        <p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
        
        <form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
            <input type="submit" name="deleteMenuItemBtn" value="Yes, Delete" class="btn btn-danger">
            <a href="../index.php?shop_id=<?php echo $item['shop_id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
