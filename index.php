<?php require_once 'config/database.php'; ?>
<?php require_once 'models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ramen Shop Menu</title>
</head>
<body>
	<?php $shop = getRamenShop($pdo); ?>
	
	<?php if (!$shop) { ?>
		<h1>No Ramen Shop Found</h1>
		<p><a href="shops/create.php">Create First Shop</a></p>
		<?php exit; ?>
	<?php } ?>
	
	<?php $menuItems = getMenuItemsByShop($pdo, $shop['id']); ?>
	
	<h1><?php echo $shop['name']; ?></h1>
	<p><?php echo $shop['description']; ?></p>
	<hr>
	
	<div>
		<a href="shops/edit.php?id=<?php echo $shop['id']; ?>">Edit Shop</a> | 
		<a href="shops/delete.php?id=<?php echo $shop['id']; ?>">Delete Shop</a> |
		<a href="menu/create.php?shop_id=<?php echo $shop['id']; ?>">Add Menu Item</a>
	</div>
	<hr>
	
	<h2>Menu Items</h2>
	
	<?php if (empty($menuItems)) { ?>
		<p>No menu items yet. <a href="menu/create.php?shop_id=<?php echo $shop['id']; ?>">Add one</a></p>
	<?php } else { ?>
		<table border="1" cellpadding="5">
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Description</th>
				<th>Price</th>
				<th>Available</th>
				<th>Actions</th>
			</tr>
			<?php foreach ($menuItems as $item) { ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['name']; ?></td>
				<td><?php echo $item['description']; ?></td>
				<td>₱<?php echo number_format($item['price'], 2); ?></td>
				<td><?php echo $item['is_available'] ? 'Yes' : 'No'; ?></td>
				<td>
					<a href="menu/edit.php?id=<?php echo $item['id']; ?>">Edit</a> | 
					<a href="menu/delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Delete this item?')">Delete</a>
				</td>
			</tr>
			<?php } ?>
		</table>
	<?php } ?>
</body>
</html>