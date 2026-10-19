<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Delete Menu Item</title>
</head>
<body>
	<?php $id = $_GET['id'] ?? null; ?>
	<?php if (!$id) { die('Item ID required'); } ?>
	
	<?php $item = getMenuItemByID($pdo, $id); ?>
	<?php if (!$item) { die('Menu item not found'); } ?>
	
	<h1>Delete Menu Item</h1>
	
	<p>Are you sure you want to delete <strong><?php echo $item['name']; ?></strong>?</p>
	<p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
	
	<form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
		<input type="submit" name="deleteMenuItemBtn" value="Yes, Delete">
		<a href="../index.php">Cancel</a>
	</form>
</body>
</html>