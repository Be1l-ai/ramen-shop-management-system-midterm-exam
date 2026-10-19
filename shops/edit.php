<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Ramen Shop</title>
</head>
<body>
	<?php $id = $_GET['id'] ?? null; ?>
	<?php if (!$id) { die('Shop ID required'); } ?>
	
	<?php $shop = getRamenShopByID($pdo, $id); ?>
	<?php if (!$shop) { die('Shop not found'); } ?>
	
	<h1>Edit Ramen Shop</h1>
	
	<form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
		<p>
			<label>Name: <input type="text" name="name" value="<?php echo $shop['name']; ?>" required></label>
		</p>
		<p>
			<label>Description:<br>
			<textarea name="description" rows="4" cols="50"><?php echo $shop['description']; ?></textarea>
			</label>
		</p>
		<p>
			<input type="submit" name="updateShopBtn" value="Update Shop">
			<a href="../index.php">Cancel</a>
		</p>
	</form>
</body>
</html>