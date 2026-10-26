<?php 
require_once __DIR__ . '/../../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) { die('User ID required'); }

$targetUser = getUserByID($pdo, $id);
if (!$targetUser) { die('User not found'); }

$shops = [];
$sql = "SELECT id, name FROM ramen_shops WHERE deleted_at IS NULL ORDER BY name";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$shops = $stmt->fetchAll();

$errorMessages = [
    'missing' => 'Please fill in every required field.',
    'username_exists' => 'That username is already taken.',
    'email_exists' => 'An account with that email already exists.',
    'invalid_role' => 'Please choose a valid role.',
    'invalid_shop' => 'The selected shop no longer exists.',
    'update_failed' => 'The user could not be updated. Please try again.'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;

$navBase = '../';
$navActive = 'users';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Ramen Shop Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Edit User: <?php echo $targetUser['username']; ?></h1>
        
        <p>Mark the account <strong>Active</strong> to approve a public registration, and optionally assign a shop.</p>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $targetUser['username']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $targetUser['email']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?php echo $targetUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="shop_owner" <?php echo $targetUser['role'] === 'shop_owner' ? 'selected' : ''; ?>>Shop Owner</option>
                    <option value="staff" <?php echo $targetUser['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="shop_id">Assign to Shop</label>
                <select id="shop_id" name="shop_id">
                    <option value="">-- No Shop --</option>
                    <?php foreach ($shops as $shop): ?>
                        <option value="<?php echo $shop['id']; ?>" <?php echo $targetUser['shop_id'] == $shop['id'] ? 'selected' : ''; ?>>
                            <?php echo $shop['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" <?php echo $targetUser['is_active'] ? 'checked' : ''; ?>> Active
                </label>
            </div>
            
            <div class="form-group">
                <input type="submit" name="updateUserBtn" value="Update User" class="btn">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
