<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['PasswordHash']) || md5($password) === $user['PasswordHash'])) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role'];
        
        echo json_encode([
            'success' => true, 
            'role' => $user['Role'],
            'redirect' => $user['Role'] === 'Admin' ? 'pages/admin/dashboard.php' : 'pages/user/dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required']);
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT UserID FROM users WHERE Email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already registered']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (Email, PasswordHash, Role) VALUES (:email, :hash, 'User')");
    $stmt->execute(['email' => $email, 'hash' => $hash]);

    echo json_encode(['success' => true]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'status') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['logged_in' => true, 'role' => $_SESSION['role']]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
