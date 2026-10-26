<?php  

function getRamenShop($pdo) {
	$sql = "SELECT * FROM ramen_shops WHERE deleted_at IS NULL LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function getRamenShopByID($pdo, $id) {
	$sql = "SELECT * FROM ramen_shops WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function getRamenShopByIDWithDeleted($pdo, $id) {
	$sql = "SELECT * FROM ramen_shops WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function insertRamenShop($pdo, $name, $description, $userId) {
	$sql = "INSERT INTO ramen_shops (name, description, created_by, updated_by) VALUES (?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description, $userId, $userId]);

	if ($executeQuery) {
		return $pdo->lastInsertId();
	}
	return false;
}

function updateRamenShop($pdo, $name, $description, $id, $userId) {
	$sql = "UPDATE ramen_shops SET name = ?, description = ?, updated_by = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description, $userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function deleteRamenShop($pdo, $id, $userId) {
	$sql = "UPDATE ramen_shops SET deleted_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function restoreRamenShop($pdo, $id, $userId) {
	$sql = "UPDATE ramen_shops SET deleted_at = NULL, updated_by = ? WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function getMenuItemsByShop($pdo, $shop_id) {
	$sql = "SELECT * FROM menu_items WHERE shop_id = ? AND deleted_at IS NULL ORDER BY id";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$shop_id]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getMenuItemByID($pdo, $id) {
	$sql = "SELECT * FROM menu_items WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function getMenuItemByIDWithDeleted($pdo, $id) {
	$sql = "SELECT * FROM menu_items WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function insertMenuItem($pdo, $shop_id, $name, $description, $price, $is_available, $userId) {
	$sql = "INSERT INTO menu_items (shop_id, name, description, price, is_available, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$shop_id, $name, $description, $price, $is_available, $userId, $userId]);

	if ($executeQuery) {
		return $pdo->lastInsertId();
	}
	return false;
}

function updateMenuItem($pdo, $name, $description, $price, $is_available, $id, $userId) {
	$sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, is_available = ?, updated_by = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description, $price, $is_available, $userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function deleteMenuItem($pdo, $id, $userId) {
	$sql = "UPDATE menu_items SET deleted_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function restoreMenuItem($pdo, $id, $userId) {
	$sql = "UPDATE menu_items SET deleted_at = NULL, updated_by = ? WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$userId, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function getUserByUsername($pdo, $username) {
	$sql = "SELECT * FROM users WHERE username = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$username]);
	return $stmt->fetch();
}

function getUserByEmail($pdo, $email) {
	$sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$email]);
	return $stmt->fetch();
}

function insertUser($pdo, $username, $email, $passwordHash, $role, $shopId, $isActive = 1) {
	$sql = "INSERT INTO users (username, email, password_hash, role, shop_id, is_active) VALUES (?, ?, ?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $email, $passwordHash, $role, $shopId, $isActive ? 1 : 0]);

	if ($executeQuery) {
		return $pdo->lastInsertId();
	}
	return false;
}

function logAudit($pdo, $userId, $action, $tableName, $recordId, $oldValues = null, $newValues = null) {
	$sql = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values) VALUES (?, ?, ?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$oldJson = $oldValues ? json_encode($oldValues) : null;
	$newJson = $newValues ? json_encode($newValues) : null;
	return $stmt->execute([$userId, $action, $tableName, $recordId, $oldJson, $newJson]);
}

function getAuditLogs($pdo, $tableName = null, $recordId = null, $limit = 50) {
	$sql = "SELECT al.*, u.username FROM audit_logs al JOIN users u ON al.user_id = u.id WHERE 1=1";
	$params = [];
	
	if ($tableName) {
		$sql .= " AND al.table_name = ?";
		$params[] = $tableName;
	}
	
	if ($recordId) {
		$sql .= " AND al.record_id = ?";
		$params[] = $recordId;
	}
	
	$sql .= " ORDER BY al.created_at DESC LIMIT ?";
	$params[] = $limit;
	
	$stmt = $pdo->prepare($sql);
	$stmt->execute($params);
	return $stmt->fetchAll();
}

function getDeletedShops($pdo) {
	$sql = "SELECT * FROM ramen_shops WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getDeletedMenuItems($pdo, $shopId = null) {
	if ($shopId) {
		$sql = "SELECT mi.*, rs.name AS shop_name FROM menu_items mi JOIN ramen_shops rs ON rs.id = mi.shop_id WHERE mi.shop_id = ? AND mi.deleted_at IS NOT NULL ORDER BY mi.deleted_at DESC";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$shopId]);
	} else {
		$sql = "SELECT mi.*, rs.name AS shop_name FROM menu_items mi JOIN ramen_shops rs ON rs.id = mi.shop_id WHERE mi.deleted_at IS NOT NULL ORDER BY mi.deleted_at DESC";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
	}
	return $stmt->fetchAll();
}

function getUsers($pdo) {
	$sql = "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY created_at";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getUserByID($pdo, $id) {
	$sql = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
	return $stmt->fetch();
}

function updateUser($pdo, $username, $email, $role, $shopId, $isActive, $id) {
	$sql = "UPDATE users SET username = ?, email = ?, role = ?, shop_id = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username, $email, $role, $shopId, $isActive, $id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

function deleteUser($pdo, $id) {
	$sql = "UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return true;
	}
	return false;
}

?>