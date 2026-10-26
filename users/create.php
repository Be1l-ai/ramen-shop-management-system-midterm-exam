<?php 
require_once __DIR__ . '/../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: ../index.php");
    exit;
}

$shops = [];
$sql = "SELECT id, name FROM ramen_shops WHERE deleted_at IS NULL ORDER BY name";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$shops = $stmt->fetchAll();

$errorMessages = [
    'missing' => 'Please fill in every required field.',
    'password_mismatch' => 'The passwords do not match.',
    'password_short' => 'The password must be at least 6 characters long.',
    'username_exists' => 'That username is already taken.',
    'email_exists' => 'An account with that email already exists.',
    'invalid_role' => 'Please choose a valid role.',
    'invalid_shop' => 'The selected shop no longer exists.',
    'create_failed' => 'The user could not be created. Please try again.'
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
    <title>Create User - Ramen Shop Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Create New User</h1>
        
        <p>Accounts created here are active immediately. Shop assignment is optional and can be changed later from the Users page.</p>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="../handleForms.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="shop_owner">Shop Owner</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="shop_id">Assign to Shop (optional)</label>
                <select id="shop_id" name="shop_id">
                    <option value="">-- No Shop --</option>
                    <?php foreach ($shops as $shop): ?>
                        <option value="<?php echo $shop['id']; ?>"><?php echo $shop['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <input type="submit" name="createUserBtn" value="Create User" class="btn">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
