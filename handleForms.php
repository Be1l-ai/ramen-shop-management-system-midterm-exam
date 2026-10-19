<?php 

require_once 'config/database.php'; 
require_once 'models.php';

if (isset($_POST['insertShopBtn'])) {
	$query = insertRamenShop($pdo, $_POST['name'], $_POST['description']);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Insertion failed";
	}
}

if (isset($_POST['updateShopBtn'])) {
	$query = updateRamenShop($pdo, $_POST['name'], $_POST['description'], $_GET['id']);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Edit failed";
	}
}

if (isset($_POST['deleteShopBtn'])) {
	$query = deleteRamenShop($pdo, $_GET['id']);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Deletion failed";
	}
}

if (isset($_POST['insertMenuItemBtn'])) {
	$is_available = isset($_POST['is_available']) ? 1 : 0;
	$query = insertMenuItem($pdo, $_GET['shop_id'], $_POST['name'], $_POST['description'], $_POST['price'], $is_available);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Insertion failed";
	}
}

if (isset($_POST['updateMenuItemBtn'])) {
	$is_available = isset($_POST['is_available']) ? 1 : 0;
	$query = updateMenuItem($pdo, $_POST['name'], $_POST['description'], $_POST['price'], $is_available, $_GET['id']);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Update failed";
	}
}

if (isset($_POST['deleteMenuItemBtn'])) {
	$query = deleteMenuItem($pdo, $_GET['id']);

	if ($query) {
		header("Location: index.php");
	}
	else {
		echo "Deletion failed";
	}
}

?>