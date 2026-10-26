<?php 
require_once 'config/database.php';
require_once 'models.php';
require_once 'auth.php';

startSession();

$errorMessages = [
    'missing' => 'Please fill in every required field.',
    'password_mismatch' => 'The passwords do not match.',
    'password_short' => 'The password must be at least 6 characters long.',
    'username_exists' => 'That username is already taken.',
    'email_exists' => 'An account with that email already exists.',
    'registration_failed' => 'Registration failed. Please try again.'
];

$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) ? $errorMessages[$_GET['error']] : null;
$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Owner Account - Ramen Shop Management</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin-top: 60px;">
        <h1>Create Owner Account</h1>
        
        <p>This form creates a <strong>shop owner</strong> account. A new account stays inactive until an administrator approves it, so you can sign in only after approval.</p>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success">Your account has been submitted successfully. Please wait for administrator approval, then log in.</p>
            <p><a href="login.php" class="btn">Go to Login</a></p>
        <?php else: ?>
            <form action="handleForms.php" method="POST">
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
                    <input type="submit" name="publicRegisterBtn" value="Register Owner Account" class="btn">
                    <a href="login.php" class="btn btn-secondary">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
