<?php 
require_once __DIR__ . '/../src/bootstrap.php';

startSession();

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$errorMessages = [
    'invalid' => 'Invalid username or password',
    'inactive' => 'Account inactive / awaiting administrator approval'
];
$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ramen Shop Management</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="guest">
    <div class="container" style="max-width: 400px; margin-top: 100px;">
        <h1>Login</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="handleForms.php" method="POST">
            <div class="form-group">
                <label>Username: <input type="text" name="username" required></label>
            </div>
            <div class="form-group">
                <label>Password: <input type="password" name="password" required></label>
            </div>
            <div class="form-group">
                <input type="submit" name="loginBtn" value="Login" class="btn">
            </div>
        </form>
        
        <p><a href="register.php">Create owner account</a> &mdash; shop owner registrations need administrator approval before they can log in.</p>
        
        <div class="login-demo">
            <p>Demo accounts:</p>
            <ul>
                <li>Admin: admin / admin123</li>
                <li>Owner: owner / owner123</li>
                <li>Staff: staff / staff123</li>
            </ul>
        </div>
    </div>
</body>
</html>
