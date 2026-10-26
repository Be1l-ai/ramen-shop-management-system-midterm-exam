<?php 
require_once __DIR__ . '/../../src/bootstrap.php';

$user = requireAuth($pdo);

$id = $_GET['id'] ?? null;
if (!$id) { die('Shop ID required'); }

$shop = getRamenShopByID($pdo, $id);
if (!$shop) { die('Shop not found'); }

if (!canManageShop($pdo, $shop['id'])) {
    header("Location: " . BASE_URL . "index.php");
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
    <title>Delete Ramen Shop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Delete Ramen Shop</h1>
        
        <div class="warning">
            <strong>Warning:</strong> This will soft delete <strong><?php echo $shop['name']; ?></strong> and all its menu items.<br>
            Deleted items can be restored from the trash (admin only).
        </div>
        
        <form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
            <input type="submit" name="deleteShopBtn" value="Yes, Delete" class="btn btn-danger">
            <a href="../index.php?shop_id=<?php echo $shop['id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
