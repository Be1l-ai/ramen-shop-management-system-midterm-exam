<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add Menu Item</title>
</head>
<body>
	<?php $shopId = $_GET['shop_id'] ?? null; ?>
	<?php if (!$shopId) { die('Shop ID required'); } ?>
	
	<?php $shop = getRamenShopByID($pdo, $shopId); ?>
	<?php if (!$shop) { die('Shop not found'); } ?>
	
	<h1>Add Menu Item to <?php echo $shop['name']; ?></h1>
	
	<form action="../handleForms.php?shop_id=<?php echo $shopId; ?>" method="POST">
		<p>
			<label>Name: <input type="text" name="name" required></label>
		</p>
		<p>
			<label>Description:<br>
			<textarea name="description" rows="3" cols="50"></textarea>
			</label>
		</p>
		<p>
			<label>Price: <input type="number" name="price" step="0.01" min="0" required></label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="is_available" value="1" checked> Available
			</label>
		</p>
		<p>
			<input type="submit" name="insertMenuItemBtn" value="Add Menu Item">
			<a href="../index.php">Cancel</a>
		</p>
	</form>
</body>
</html>