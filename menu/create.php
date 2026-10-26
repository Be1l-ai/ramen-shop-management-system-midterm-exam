<?php 
require_once __DIR__ . '/../src/bootstrap.php';

$user = requireAuth($pdo);

$shopId = $_GET['shop_id'] ?? null;
if (!$shopId) { die('Shop ID required'); }

$shop = getRamenShopByID($pdo, $shopId);
if (!$shop) { die('Shop not found'); }

if (!canManageShop($pdo, $shopId)) {
    header("Location: ../index.php");
    exit;
}

$errorMessages = [
    'create_failed' => 'The menu item could not be added. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '../';
$navActive = 'dashboard';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Add Menu Item to <?php echo $shop['name']; ?></h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="../handleForms.php?shop_id=<?php echo $shopId; ?>" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" cols="50"></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_available" value="1" checked> Available
                </label>
            </div>
            <div class="form-group">
                <input type="submit" name="insertMenuItemBtn" value="Add Menu Item" class="btn">
                <a href="../index.php?shop_id=<?php echo $shopId; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
