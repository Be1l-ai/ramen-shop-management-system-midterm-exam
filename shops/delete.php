<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Delete Ramen Shop</title>
</head>
<body>
	<?php $id = $_GET['id'] ?? null; ?>
	<?php if (!$id) { die('Shop ID required'); } ?>
	
	<?php $shop = getRamenShopByID($pdo, $id); ?>
	<?php if (!$shop) { die('Shop not found'); } ?>
	
	<h1>Delete Ramen Shop</h1>
	
	<p>Are you sure you want to delete <strong><?php echo $shop['name']; ?></strong>?</p>
	<p>This will also delete all menu items for this shop.</p>
	
	<form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
		<input type="submit" name="deleteShopBtn" value="Yes, Delete">
		<a href="../index.php">Cancel</a>
	</form>
</body>
</html>