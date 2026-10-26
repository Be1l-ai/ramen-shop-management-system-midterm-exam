<?php 
require_once '../config/database.php';
require_once '../models.php';
require_once '../auth.php';

$user = requireAuth($pdo);

if (!canCreateShop($pdo)) {
    if ($user['role'] === 'shop_owner' && !empty($user['shop_id'])) {
        header("Location: ../index.php?error=shop_limit");
    }
    else {
        header("Location: ../index.php?error=forbidden");
    }
    exit;
}

$errorMessages = [
    'name_required' => 'Please enter a shop name.',
    'create_failed' => 'The shop could not be created. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '../';
$navActive = 'shops_create';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ramen Shop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Create Ramen Shop</h1>
        
        <?php if ($user['role'] === 'shop_owner'): ?>
            <p>This shop will be assigned to your account. A shop owner account can manage only one shop.</p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="../handleForms.php" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" cols="50"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="insertShopBtn" value="Create Shop" class="btn">
                <a href="../index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
