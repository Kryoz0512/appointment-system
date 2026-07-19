<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    // Both User and Admin can fetch transactions
    $stmt = $pdo->query("SELECT * FROM transactions");
    $transactions = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $transactions]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_quota') {
    // Only Admin can update quota
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $transactionId = $data['id'] ?? null;
    $newQuota = $data['quota'] ?? null;

    if (!$transactionId || $newQuota === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE transactions SET DailyQuota = :quota WHERE TransactionID = :id");
    if ($stmt->execute(['quota' => $newQuota, 'id' => $transactionId])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update quota']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? null;
    $requirements = $data['requirements'] ?? null;
    $quota = $data['quota'] ?? 0;

    if (!$name) {
        echo json_encode(['success' => false, 'error' => 'Transaction name is required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO transactions (TransactionName, Requirements, DailyQuota) VALUES (:name, :req, :quota)");
    if ($stmt->execute(['name' => $name, 'req' => $requirements, 'quota' => $quota])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create transaction']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $transactionId = $data['id'] ?? null;
    $name = $data['name'] ?? null;
    $requirements = $data['requirements'] ?? null;
    $quota = $data['quota'] ?? null;

    if (!$transactionId || !$name || $quota === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE transactions SET TransactionName = :name, Requirements = :req, DailyQuota = :quota WHERE TransactionID = :id");
    if ($stmt->execute(['name' => $name, 'req' => $requirements, 'quota' => $quota, 'id' => $transactionId])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update transaction']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
