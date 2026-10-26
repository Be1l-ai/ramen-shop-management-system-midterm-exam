<?php  

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function attemptLogin($pdo, $username, $password) {
    $sql = "SELECT * FROM users WHERE username = ? AND deleted_at IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($password, $user['password_hash'])) {
        return ['status' => 'invalid', 'user' => null];
    }

    if (!$user['is_active']) {
        return ['status' => 'inactive', 'user' => $user];
    }

    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['shop_id'] = $user['shop_id'];
    return ['status' => 'success', 'user' => $user];
}

function login($pdo, $username, $password) {
    $result = attemptLogin($pdo, $username, $password);
    return $result['status'] === 'success';
}

function logout() {
    startSession();
    session_destroy();
    header("Location: " . BASE_URL . "login.php");
    exit;
}

function getCurrentUser($pdo) {
    startSession();
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $sql = "SELECT * FROM users WHERE id = ? AND is_active = 1 AND deleted_at IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireAuth($pdo) {
    $user = getCurrentUser($pdo);
    if (!$user) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }
    return $user;
}

function isAdmin($pdo) {
    $user = getCurrentUser($pdo);
    return $user && $user['role'] === 'admin';
}

function canManageShop($pdo, $shopId) {
    $user = getCurrentUser($pdo);
    if (!$user) return false;
    if ($user['role'] === 'admin') return true;
    return $user['shop_id'] == $shopId;
}

function canCreateShop($pdo) {
    $user = getCurrentUser($pdo);
    if (!$user) return false;
    if ($user['role'] === 'admin') return true;
    if ($user['role'] === 'shop_owner') return empty($user['shop_id']);
    return false;
}

?>