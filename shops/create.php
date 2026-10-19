<?php require_once '../config/database.php'; ?>
<?php require_once '../models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create Ramen Shop</title>
</head>
<body>
	<h1>Create Ramen Shop</h1>
	
	<form action="../handleForms.php" method="POST">
		<p>
			<label>Name: <input type="text" name="name" required></label>
		</p>
		<p>
			<label>Description:<br>
			<textarea name="description" rows="4" cols="50"></textarea>
			</label>
		</p>
		<p>
			<input type="submit" name="insertShopBtn" value="Create Shop">
			<a href="../index.php">Cancel</a>
		</p>
	</form>
</body>
</html>