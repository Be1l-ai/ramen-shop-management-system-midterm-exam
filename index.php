<?php 
require_once __DIR__ . '/src/bootstrap.php';

$user = requireAuth($pdo);

$shops = [];
if ($user['role'] === 'admin') {
    $sql = "SELECT * FROM ramen_shops WHERE deleted_at IS NULL ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $shops = $stmt->fetchAll();
} else {
    $sql = "SELECT * FROM ramen_shops WHERE deleted_at IS NULL AND (created_by = ? OR updated_by = ? OR id = ?) ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id'], $user['id'], $user['shop_id']]);
    $shops = $stmt->fetchAll();
}

$selectedShopId = isset($_GET['shop_id']) && $_GET['shop_id'] !== '' ? $_GET['shop_id'] : ($shops ? $shops[0]['id'] : null);
$selectedShop = null;

foreach ($shops as $shop) {
    if ($shop['id'] == $selectedShopId) {
        $selectedShop = $shop;
        break;
    }
}

$menuItems = $selectedShop ? getMenuItemsByShop($pdo, $selectedShop['id']) : [];

$isAdminUser = $user['role'] === 'admin';
$canCreateShopAllowed = canCreateShop($pdo);
$canManageSelected = $selectedShop ? canManageShop($pdo, $selectedShop['id']) : false;

$errorMessages = [
    'shop_limit' => 'You already have a shop assigned. A shop owner account can manage only one shop.',
    'forbidden' => 'You are not allowed to perform that action.',
    'create_failed' => 'The shop could not be created. Please try again.',
    'update_failed' => 'The shop could not be updated. Please try again.',
    'delete_failed' => 'The shop could not be deleted. Please try again.',
    'menu_create_failed' => 'The menu item could not be added. Please try again.',
    'menu_update_failed' => 'The menu item could not be updated. Please try again.',
    'menu_delete_failed' => 'The menu item could not be deleted. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '';
$navActive = 'dashboard';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramen Shop Menu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>
    
    <div class="container">
        <h1>Dashboard</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div style="margin-bottom: 15px;">
            <?php if ($canCreateShopAllowed): ?>
                <a href="shops/create.php" class="btn">Add Shop</a>
            <?php endif; ?>
            <?php if ($isAdminUser): ?>
                <a href="shops/trash.php" class="btn btn-secondary">Deleted Shops</a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($shops)): ?>
            <?php if ($canCreateShopAllowed): ?>
                <p>No Ramen Shop found yet.</p>
                <p><a href="shops/create.php" class="btn">Add Shop</a></p>
            <?php elseif ($user['role'] === 'staff'): ?>
                <p>No shop is assigned to your account yet. Please ask an administrator to assign you to a shop.</p>
            <?php else: ?>
                <p>Your assigned shop is currently unavailable. Please contact an administrator.</p>
            <?php endif; ?>
        <?php else: ?>
            <form method="GET" class="shop-selector">
                <label>Select Shop: 
                    <select name="shop_id" onchange="this.form.submit()">
                        <option value="">-- Select Shop --</option>
                        <?php foreach ($shops as $shop): ?>
                            <option value="<?php echo $shop['id']; ?>" <?php echo $selectedShop && $selectedShop['id'] == $shop['id'] ? 'selected' : ''; ?>>
                                <?php echo $shop['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </form>
            
            <?php if ($selectedShop): ?>
                <h2><?php echo $selectedShop['name']; ?></h2>
                <p><?php echo $selectedShop['description']; ?></p>
                <p><small>Created: <?php echo $selectedShop['created_at']; ?> | Updated: <?php echo $selectedShop['updated_at'] ?? 'Never'; ?></small></p>
                <hr>
                
                <div style="margin-bottom: 15px;">
                    <?php if ($canManageSelected): ?>
                        <a href="shops/edit.php?id=<?php echo $selectedShop['id']; ?>" class="btn">Edit Shop</a> 
                        <a href="shops/delete.php?id=<?php echo $selectedShop['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this shop?')">Delete Shop</a>
                        <a href="menu/create.php?shop_id=<?php echo $selectedShop['id']; ?>" class="btn">Add Menu Item</a>
                    <?php else: ?>
                        <p>View only &mdash; you do not have management access to this shop.</p>
                    <?php endif; ?>
                    <?php if ($isAdminUser): ?>
                        <a href="menu/trash.php?shop_id=<?php echo $selectedShop['id']; ?>" class="btn btn-secondary">Menu Trash</a>
                    <?php endif; ?>
                </div>
                <hr>
                
                <h2>Menu Items</h2>
                
                <?php if (empty($menuItems)): ?>
                    <?php if ($canManageSelected): ?>
                        <p>No menu items yet. <a href="menu/create.php?shop_id=<?php echo $selectedShop['id']; ?>" class="btn">Add Menu Item</a></p>
                    <?php else: ?>
                        <p>No menu items yet.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Available</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach ($menuItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['description']; ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['is_available'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td><?php echo $item['updated_at'] ?? 'Never'; ?></td>
                            <td class="actions">
                                <?php if ($canManageSelected): ?>
                                    <a href="menu/edit.php?id=<?php echo $item['id']; ?>">Edit</a> | 
                                    <a href="menu/delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Delete this item?')">Delete</a>
                                <?php else: ?>
                                    View only
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            <?php else: ?>
                <p>Please select a shop from the dropdown above.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
