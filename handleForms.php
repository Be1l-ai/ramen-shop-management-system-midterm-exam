<?php 

require_once 'config/database.php'; 
require_once 'models.php';
require_once 'auth.php';

// Handle login/logout/public register FIRST (before auth check)
if (isset($_POST['loginBtn'])) {
	$result = attemptLogin($pdo, $_POST['username'] ?? '', $_POST['password'] ?? '');
	if ($result['status'] === 'success') {
		header("Location: index.php");
		exit;
	}
	if ($result['status'] === 'inactive') {
		header("Location: login.php?error=inactive");
		exit;
	}
	header("Location: login.php?error=invalid");
	exit;
}

if (isset($_POST['publicRegisterBtn'])) {
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';

	if ($username === '' || $email === '' || $password === '') {
		header("Location: register.php?error=missing");
		exit;
	}
	if ($password !== $confirmPassword) {
		header("Location: register.php?error=password_mismatch");
		exit;
	}
	if (strlen($password) < 6) {
		header("Location: register.php?error=password_short");
		exit;
	}
	if (getUserByUsername($pdo, $username)) {
		header("Location: register.php?error=username_exists");
		exit;
	}
	if (getUserByEmail($pdo, $email)) {
		header("Location: register.php?error=email_exists");
		exit;
	}

	$newUserId = false;
	try {
		$newUserId = insertUser($pdo, $username, $email, hashPassword($password), 'shop_owner', null, 0);
	} catch (PDOException $e) {
		$newUserId = false;
	}

	if ($newUserId) {
		header("Location: register.php?success=1");
		exit;
	}
	header("Location: register.php?error=registration_failed");
	exit;
}

// NOW check auth for all other operations
$user = requireAuth($pdo);
$userId = $user['id'];

if (isset($_POST['insertShopBtn'])) {
	if (!canCreateShop($pdo)) {
		if ($user['role'] === 'shop_owner' && !empty($user['shop_id'])) {
			header("Location: index.php?error=shop_limit");
		}
		else {
			header("Location: index.php?error=forbidden");
		}
		exit;
	}

	$name = trim($_POST['name'] ?? '');
	$description = trim($_POST['description'] ?? '');

	if ($name === '') {
		header("Location: shops/create.php?error=name_required");
		exit;
	}

	$shopId = insertRamenShop($pdo, $name, $description, $userId);
	
	if ($shopId) {
		// If shop_owner creates a shop, assign it to them
		if ($user['role'] === 'shop_owner') {
			$pdo->prepare("UPDATE users SET shop_id = ? WHERE id = ?")->execute([$shopId, $userId]);
			$_SESSION['shop_id'] = $shopId;
		}
		logAudit($pdo, $userId, 'create', 'ramen_shops', $shopId, null, [
			'name' => $name,
			'description' => $description
		]);
		header("Location: index.php?shop_id=" . $shopId);
		exit;
	}
	header("Location: shops/create.php?error=create_failed");
	exit;
}

if (isset($_POST['updateShopBtn'])) {
	$id = $_GET['id'] ?? null;
	$shop = getRamenShopByID($pdo, $id);
	
	if (!$shop || !canManageShop($pdo, $shop['id'])) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$name = trim($_POST['name'] ?? '');
	if ($name === '') {
		header("Location: shops/edit.php?id=" . urlencode($id) . "&error=name_required");
		exit;
	}
	
	$oldValues = ['name' => $shop['name'], 'description' => $shop['description']];
	$query = updateRamenShop($pdo, $name, $_POST['description'] ?? '', $id, $userId);
	
	if ($query) {
		logAudit($pdo, $userId, 'update', 'ramen_shops', $id, $oldValues, [
			'name' => $name,
			'description' => $_POST['description'] ?? ''
		]);
		header("Location: index.php?shop_id=" . $id);
		exit;
	}
	header("Location: shops/edit.php?id=" . urlencode($id) . "&error=update_failed");
	exit;
}

if (isset($_POST['deleteShopBtn'])) {
	$id = $_GET['id'] ?? null;
	$shop = getRamenShopByID($pdo, $id);
	
	if (!$shop || !canManageShop($pdo, $shop['id'])) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$oldValues = ['name' => $shop['name'], 'description' => $shop['description']];
	$query = deleteRamenShop($pdo, $id, $userId);
	
	if ($query) {
		logAudit($pdo, $userId, 'delete', 'ramen_shops', $id, $oldValues, null);
		header("Location: index.php");
		exit;
	}
	header("Location: index.php?error=delete_failed");
	exit;
}

if (isset($_POST['insertMenuItemBtn'])) {
	$shopId = $_GET['shop_id'] ?? null;
	$shop = getRamenShopByID($pdo, $shopId);
	
	if (!$shop || !canManageShop($pdo, $shopId)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$is_available = isset($_POST['is_available']) ? 1 : 0;
	$itemId = insertMenuItem($pdo, $shopId, $_POST['name'] ?? '', $_POST['description'] ?? '', $_POST['price'] ?? 0, $is_available, $userId);
	
	if ($itemId) {
		logAudit($pdo, $userId, 'create', 'menu_items', $itemId, null, [
			'shop_id' => $shopId,
			'name' => $_POST['name'] ?? '',
			'description' => $_POST['description'] ?? '',
			'price' => $_POST['price'] ?? 0,
			'is_available' => $is_available
		]);
		header("Location: index.php?shop_id=" . $shopId);
		exit;
	}
	header("Location: menu/create.php?shop_id=" . urlencode($shopId) . "&error=create_failed");
	exit;
}

if (isset($_POST['updateMenuItemBtn'])) {
	$id = $_GET['id'] ?? null;
	$item = getMenuItemByID($pdo, $id);
	
	if (!$item || !canManageShop($pdo, $item['shop_id'])) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$is_available = isset($_POST['is_available']) ? 1 : 0;
	$oldValues = [
		'name' => $item['name'],
		'description' => $item['description'],
		'price' => $item['price'],
		'is_available' => $item['is_available']
	];
	$query = updateMenuItem($pdo, $_POST['name'] ?? '', $_POST['description'] ?? '', $_POST['price'] ?? 0, $is_available, $id, $userId);
	
	if ($query) {
		logAudit($pdo, $userId, 'update', 'menu_items', $id, $oldValues, [
			'name' => $_POST['name'] ?? '',
			'description' => $_POST['description'] ?? '',
			'price' => $_POST['price'] ?? 0,
			'is_available' => $is_available
		]);
		header("Location: index.php?shop_id=" . $item['shop_id']);
		exit;
	}
	header("Location: menu/edit.php?id=" . urlencode($id) . "&error=update_failed");
	exit;
}

if (isset($_POST['deleteMenuItemBtn'])) {
	$id = $_GET['id'] ?? null;
	$item = getMenuItemByID($pdo, $id);
	
	if (!$item || !canManageShop($pdo, $item['shop_id'])) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$oldValues = [
		'name' => $item['name'],
		'description' => $item['description'],
		'price' => $item['price'],
		'is_available' => $item['is_available']
	];
	$query = deleteMenuItem($pdo, $id, $userId);
	
	if ($query) {
		logAudit($pdo, $userId, 'delete', 'menu_items', $id, $oldValues, null);
		header("Location: index.php?shop_id=" . $item['shop_id']);
		exit;
	}
	header("Location: index.php?error=menu_delete_failed");
	exit;
}

if (isset($_POST['restoreShopBtn'])) {
	if (!isAdmin($pdo)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$id = $_GET['id'] ?? null;
	$shop = getRamenShopByIDWithDeleted($pdo, $id);
	
	if ($shop && restoreRamenShop($pdo, $id, $userId)) {
		logAudit($pdo, $userId, 'restore', 'ramen_shops', $id, null, [
			'name' => $shop['name'],
			'description' => $shop['description']
		]);
		header("Location: shops/trash.php");
		exit;
	}
	header("Location: shops/trash.php?error=restore_failed");
	exit;
}

if (isset($_POST['restoreMenuItemBtn'])) {
	if (!isAdmin($pdo)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$id = $_GET['id'] ?? null;
	$item = getMenuItemByIDWithDeleted($pdo, $id);
	
	if ($item && restoreMenuItem($pdo, $id, $userId)) {
		logAudit($pdo, $userId, 'restore', 'menu_items', $id, null, [
			'name' => $item['name'],
			'description' => $item['description'],
			'price' => $item['price'],
			'is_available' => $item['is_available']
		]);
		header("Location: menu/trash.php?shop_id=" . $item['shop_id']);
		exit;
	}
	header("Location: menu/trash.php?error=restore_failed");
	exit;
}

if (isset($_POST['createUserBtn'])) {
	if (!isAdmin($pdo)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';
	$role = $_POST['role'] ?? 'staff';
	$shopId = !empty($_POST['shop_id']) ? (int)$_POST['shop_id'] : null;
	
	if (!in_array($role, ['admin', 'shop_owner', 'staff'], true)) {
		header("Location: users/create.php?error=invalid_role");
		exit;
	}
	if ($username === '' || $email === '' || $password === '') {
		header("Location: users/create.php?error=missing");
		exit;
	}
	if ($password !== $confirmPassword) {
		header("Location: users/create.php?error=password_mismatch");
		exit;
	}
	if (strlen($password) < 6) {
		header("Location: users/create.php?error=password_short");
		exit;
	}
	if (getUserByUsername($pdo, $username)) {
		header("Location: users/create.php?error=username_exists");
		exit;
	}
	if (getUserByEmail($pdo, $email)) {
		header("Location: users/create.php?error=email_exists");
		exit;
	}
	if ($shopId !== null && !getRamenShopByID($pdo, $shopId)) {
		header("Location: users/create.php?error=invalid_shop");
		exit;
	}
	
	$newUserId = false;
	try {
		$newUserId = insertUser($pdo, $username, $email, hashPassword($password), $role, $shopId, 1);
	} catch (PDOException $e) {
		$newUserId = false;
	}
	
	if ($newUserId) {
		logAudit($pdo, $userId, 'create', 'users', $newUserId, null, [
			'username' => $username,
			'email' => $email,
			'role' => $role,
			'shop_id' => $shopId,
			'is_active' => 1
		]);
		header("Location: users/index.php?created=1");
		exit;
	}
	header("Location: users/create.php?error=create_failed");
	exit;
}

if (isset($_POST['updateUserBtn'])) {
	if (!isAdmin($pdo)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$id = $_GET['id'] ?? null;
	$oldUser = getUserByID($pdo, $id);
	
	if (!$oldUser) {
		header("Location: users/index.php?error=not_found");
		exit;
	}
	
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$role = $_POST['role'] ?? $oldUser['role'];
	$shopId = !empty($_POST['shop_id']) ? (int)$_POST['shop_id'] : null;
	$isActive = isset($_POST['is_active']) ? 1 : 0;
	
	if (!in_array($role, ['admin', 'shop_owner', 'staff'], true)) {
		header("Location: users/edit.php?id=" . urlencode($id) . "&error=invalid_role");
		exit;
	}
	if ($username === '' || $email === '') {
		header("Location: users/edit.php?id=" . urlencode($id) . "&error=missing");
		exit;
	}
	$existingUsername = getUserByUsername($pdo, $username);
	if ($existingUsername && $existingUsername['id'] != $id) {
		header("Location: users/edit.php?id=" . urlencode($id) . "&error=username_exists");
		exit;
	}
	$existingEmail = getUserByEmail($pdo, $email);
	if ($existingEmail && $existingEmail['id'] != $id) {
		header("Location: users/edit.php?id=" . urlencode($id) . "&error=email_exists");
		exit;
	}
	if ($shopId !== null && !getRamenShopByID($pdo, $shopId)) {
		header("Location: users/edit.php?id=" . urlencode($id) . "&error=invalid_shop");
		exit;
	}
	
	$oldValues = [
		'username' => $oldUser['username'],
		'email' => $oldUser['email'],
		'role' => $oldUser['role'],
		'shop_id' => $oldUser['shop_id'],
		'is_active' => $oldUser['is_active']
	];
	
	$query = updateUser($pdo, $username, $email, $role, $shopId, $isActive, $id);
	
	if ($query) {
		logAudit($pdo, $userId, 'update', 'users', $id, $oldValues, [
			'username' => $username,
			'email' => $email,
			'role' => $role,
			'shop_id' => $shopId,
			'is_active' => $isActive
		]);
		header("Location: users/index.php?updated=1");
		exit;
	}
	header("Location: users/edit.php?id=" . urlencode($id) . "&error=update_failed");
	exit;
}

if (isset($_POST['deleteUserBtn'])) {
	if (!isAdmin($pdo)) {
		header("Location: index.php?error=forbidden");
		exit;
	}
	
	$id = $_GET['id'] ?? null;
	
	if ($id == $userId) {
		header("Location: users/index.php?error=self_delete");
		exit;
	}
	
	$oldUser = getUserByID($pdo, $id);
	
	if ($oldUser && deleteUser($pdo, $id)) {
		logAudit($pdo, $userId, 'delete', 'users', $id, [
			'username' => $oldUser['username'],
			'email' => $oldUser['email'],
			'role' => $oldUser['role']
		], null);
		header("Location: users/index.php?deleted=1");
		exit;
	}
	header("Location: users/index.php?error=delete_failed");
	exit;
}

?>
