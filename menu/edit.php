<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Menu Item</title>
</head>
<body>
	<?php $id = $_GET['id'] ?? null; ?>
	<?php if (!$id) { die('Item ID required'); } ?>
	
	<?php $item = getMenuItemByID($pdo, $id); ?>
	<?php if (!$item) { die('Menu item not found'); } ?>
	
	<h1>Edit Menu Item</h1>
	
	<form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
		<p>
			<label>Name: <input type="text" name="name" value="<?php echo $item['name']; ?>" required></label>
		</p>
		<p>
			<label>Description:<br>
			<textarea name="description" rows="3" cols="50"><?php echo $item['description']; ?></textarea>
			</label>
		</p>
		<p>
			<label>Price: <input type="number" name="price" step="0.01" min="0" value="<?php echo $item['price']; ?>" required></label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="is_available" value="1" <?php if ($item['is_available']) echo 'checked'; ?>> Available
			</label>
		</p>
		<p>
			<input type="submit" name="updateMenuItemBtn" value="Update Menu Item">
			<a href="../index.php">Cancel</a>
		</p>
	</form>
</body>
</html>