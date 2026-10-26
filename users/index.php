<?php 
require_once __DIR__ . '/../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: ../index.php");
    exit;
}

$users = getUsers($pdo);

$noticeMessages = [
    'created' => 'User created successfully.',
    'updated' => 'User updated successfully.',
    'deleted' => 'User deactivated successfully.'
];
$errorMessages = [
    'not_found' => 'User not found.',
    'self_delete' => 'You cannot delete your own account.',
    'delete_failed' => 'The user could not be deactivated. Please try again.'
];
$notice = isset($_GET['created']) ? $noticeMessages['created'] : (isset($_GET['updated']) ? $noticeMessages['updated'] : (isset($_GET['deleted']) ? $noticeMessages['deleted'] : null));
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
    <title>Users - Ramen Shop Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container">
        <h1>User Management</h1>
        
        <?php if ($notice): ?>
            <p class="success"><?php echo $notice; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <p><a href="create.php" class="btn btn-success">Add New User</a></p>
        
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Shop ID</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $u): ?>
                <tr <?php echo !$u['is_active'] ? 'class="inactive"' : ''; ?>>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo $u['username']; ?></td>
                    <td><?php echo $u['email']; ?></td>
                    <td><span class="badge badge-<?php echo $u['role']; ?>"><?php echo ucfirst(str_replace('_', ' ', $u['role'])); ?></span></td>
                    <td><?php echo $u['shop_id'] ?? 'N/A'; ?></td>
                    <td><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></td>
                    <td><?php echo $u['created_at']; ?></td>
                    <td><?php echo $u['updated_at'] ?? 'Never'; ?></td>
                    <td class="actions">
                        <?php if ($u['id'] != $user['id']): ?>
                            <a href="edit.php?id=<?php echo $u['id']; ?>">Edit</a> | 
                            <a href="delete.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Deactivate this user?')">Deactivate</a>
                        <?php else: ?>
                            Current User
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
