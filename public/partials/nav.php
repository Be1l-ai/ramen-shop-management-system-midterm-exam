<?php

if (!isset($navBase)) {
    $navBase = '';
}
if (!isset($navActive)) {
    $navActive = '';
}
if (!isset($navUser) || !is_array($navUser)) {
    $navUser = getCurrentUser($pdo);
}

$navIsAdmin = isset($navUser['role']) && $navUser['role'] === 'admin';
$navCanCreateShop = canCreateShop($pdo);
$navShopId = !empty($navUser['shop_id']) ? $navUser['shop_id'] : null;
$navCurrent = function ($key) use ($navActive) {
    return $navActive === $key ? ' aria-current="page"' : '';
};
?>
<aside class="nav-bar">
    <strong>Ramen Shop Management</strong>
    <nav class="nav-links">
        <a href="<?php echo $navBase; ?>index.php"<?php echo $navCurrent('dashboard'); ?>>Dashboard</a>
        <?php if ($navIsAdmin): ?>
            <a href="<?php echo $navBase; ?>users/index.php"<?php echo $navCurrent('users'); ?>>Users</a>
            <a href="<?php echo $navBase; ?>audit/index.php"<?php echo $navCurrent('audit'); ?>>Audit Logs</a>
            <a href="<?php echo $navBase; ?>shops/trash.php"<?php echo $navCurrent('shops_trash'); ?>>Deleted Shops</a>
        <?php endif; ?>
        <?php if ($navIsAdmin || $navCanCreateShop): ?>
            <a href="<?php echo $navBase; ?>shops/create.php"<?php echo $navCurrent('shops_create'); ?>>Add Shop</a>
        <?php endif; ?>
        <?php if ($navIsAdmin): ?>
            <a href="<?php echo $navBase; ?>menu/trash.php"<?php echo $navCurrent('menu_trash'); ?>>Menu Trash</a>
        <?php elseif ($navShopId): ?>
            <a href="<?php echo $navBase; ?>menu/trash.php?shop_id=<?php echo $navShopId; ?>"<?php echo $navCurrent('menu_trash'); ?>>Menu Trash</a>
        <?php endif; ?>
    </nav>
    <span class="user-info">
        <?php echo htmlspecialchars($navUser['username']); ?> (<?php echo htmlspecialchars($navUser['role']); ?>) |
        <a href="<?php echo $navBase; ?>logout.php">Logout</a>
    </span>
</aside>
