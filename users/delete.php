<?php 
require_once __DIR__ . '/../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) { die('User ID required'); }

if ($id == $user['id']) {
    die('You cannot delete yourself');
}

$targetUser = getUserByID($pdo, $id);
if (!$targetUser) { die('User not found'); }

$navBase = '../';
$navActive = 'users';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User - Ramen Shop Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container" style="max-width: 600px;">
        <h1>Deactivate User</h1>
        
        <div class="warning">
            <strong>Warning:</strong> This will deactivate <strong><?php echo $targetUser['username']; ?></strong> (<?php echo $targetUser['email']; ?>).<br>
            The user will not be able to log in. This action can be reversed by an admin.
        </div>
        
        <form action="../handleForms.php?id=<?php echo $id; ?>" method="POST">
            <input type="submit" name="deleteUserBtn" value="Yes, Deactivate" class="btn btn-danger">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
