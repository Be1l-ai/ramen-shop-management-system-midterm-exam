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

$errorMessages = [
    'name_required' => 'Please enter a shop name.',
    'update_failed' => 'The shop could not be updated. Please try again.'
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
    <title>Edit Ramen Shop</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Edit Ramen Shop</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $shop['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" cols="50"><?php echo $shop['description']; ?></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="updateShopBtn" value="Update Shop" class="btn">
                <a href="../index.php?shop_id=<?php echo $shop['id']; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
