<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

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
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
