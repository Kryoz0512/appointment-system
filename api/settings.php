<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? 'list_blocked';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list_blocked') {
    $stmt = $pdo->query("SELECT * FROM blockeddates ORDER BY BlockedDate ASC");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_blocked') {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;
    $reason = $data['reason'] ?? '';

    if (!$date) {
        echo json_encode(['success' => false, 'error' => 'Date is required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO blockeddates (BlockedDate, Reason) VALUES (:d, :r)");
        $stmt->execute(['d' => $date, 'r' => $reason]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Handle duplicate entry gracefully
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(['success' => false, 'error' => 'Date is already blocked']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to block date']);
        }
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'remove_blocked') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Blocked ID is required']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM blockeddates WHERE BlockedID = :id");
    $stmt->execute(['id' => $id]);
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
