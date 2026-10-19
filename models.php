<?php  

function getRamenShop($pdo) {
	$sql = "SELECT * FROM ramen_shops LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function getRamenShopByID($pdo, $id) {
	$sql = "SELECT * FROM ramen_shops WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function insertRamenShop($pdo, $name, $description) {
	$sql = "INSERT INTO ramen_shops (name, description) VALUES (?, ?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description]);

	if ($executeQuery) {
		return true;
	}
}

function updateRamenShop($pdo, $name, $description, $id) {
	$sql = "UPDATE ramen_shops SET name = ?, description = ? WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description, $id]);

	if ($executeQuery) {
		return true;
	}
}

function deleteRamenShop($pdo, $id) {
	$sql = "DELETE FROM ramen_shops WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return true;
	}
}

function getMenuItemsByShop($pdo, $shop_id) {
	$sql = "SELECT * FROM menu_items WHERE shop_id = ? ORDER BY id";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$shop_id]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getMenuItemByID($pdo, $id) {
	$sql = "SELECT * FROM menu_items WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function insertMenuItem($pdo, $shop_id, $name, $description, $price, $is_available) {
	$sql = "INSERT INTO menu_items (shop_id, name, description, price, is_available) VALUES (?, ?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$shop_id, $name, $description, $price, $is_available]);

	if ($executeQuery) {
		return true;
	}
}

function updateMenuItem($pdo, $name, $description, $price, $is_available, $id) {
	$sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, is_available = ? WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$name, $description, $price, $is_available, $id]);

	if ($executeQuery) {
		return true;
	}
}

function deleteMenuItem($pdo, $id) {
	$sql = "DELETE FROM menu_items WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return true;
	}
}

?>