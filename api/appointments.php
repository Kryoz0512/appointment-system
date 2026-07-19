<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Require login for any appointment action
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'availability') {
    // Check quota and availability for a specific month or date range to render calendar
    $month = $_GET['month'] ?? date('m');
    $year = $_GET['year'] ?? date('Y');
    $transactionId = $_GET['transaction_id'] ?? null;

    if (!$transactionId) {
        echo json_encode(['success' => false, 'error' => 'Transaction ID required']);
        exit;
    }

    // Get daily quota
    $stmt = $pdo->prepare("SELECT DailyQuota FROM transactions WHERE TransactionID = :tid");
    $stmt->execute(['tid' => $transactionId]);
    $transaction = $stmt->fetch();
    if (!$transaction) {
        echo json_encode(['success' => false, 'error' => 'Invalid transaction']);
        exit;
    }
    $quota = $transaction['DailyQuota'];

    // Get blocked dates for the month
    $stmtBlocked = $pdo->prepare("SELECT BlockedDate FROM blockeddates WHERE MONTH(BlockedDate) = :m AND YEAR(BlockedDate) = :y");
    $stmtBlocked->execute(['m' => $month, 'y' => $year]);
    $blocked = $stmtBlocked->fetchAll(PDO::FETCH_COLUMN);

    // Get active appointment counts per day
    $stmtAppts = $pdo->prepare("
        SELECT ApptDate, COUNT(*) as count 
        FROM appointments 
        WHERE TransactionID = :tid 
          AND MONTH(ApptDate) = :m 
          AND YEAR(ApptDate) = :y 
          AND Status IN ('Pending', 'Confirmed', 'Pending_Reschedule')
        GROUP BY ApptDate
    ");
    $stmtAppts->execute(['tid' => $transactionId, 'm' => $month, 'y' => $year]);
    $counts = $stmtAppts->fetchAll(PDO::FETCH_KEY_PAIR);

    echo json_encode([
        'success' => true,
        'quota' => $quota,
        'blocked_dates' => $blocked,
        'booked_counts' => $counts
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'book') {
    // Book an appointment with Row-Level Locking to prevent Race Conditions
    $data = json_decode(file_get_contents('php://input'), true);
    $transactionId = $data['transaction_id'] ?? null;
    $date = $data['date'] ?? null;
    $time = $data['time'] ?? '08:00:00'; // Defaulting for now or based on selection

    if (!$transactionId || !$date || !$time) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }

    // Check if it's weekend
    $dayOfWeek = date('N', strtotime($date));
    if ($dayOfWeek >= 6) {
        echo json_encode(['success' => false, 'error' => 'Cannot book on weekends']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        // 1. Lock the transaction row to check quota safely
        $stmt = $pdo->prepare("SELECT DailyQuota FROM transactions WHERE TransactionID = :tid FOR UPDATE");
        $stmt->execute(['tid' => $transactionId]);
        $transaction = $stmt->fetch();
        
        if (!$transaction) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Invalid transaction']);
            exit;
        }

        // 2. Check if date is blocked
        $stmtBlocked = $pdo->prepare("SELECT 1 FROM blockeddates WHERE BlockedDate = :d");
        $stmtBlocked->execute(['d' => $date]);
        if ($stmtBlocked->fetch()) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'This date is blocked']);
            exit;
        }

        // 3. Count current bookings for this transaction on this date
        $stmt2 = $pdo->prepare("SELECT COUNT(*) as current_bookings FROM appointments WHERE ApptDate = :date AND TransactionID = :tid AND Status IN ('Pending', 'Confirmed', 'Pending_Reschedule')");
        $stmt2->execute(['date' => $date, 'tid' => $transactionId]);
        $current_bookings = $stmt2->fetch()['current_bookings'];
        
        // 4. Check if we can book
        if ($current_bookings < $transaction['DailyQuota']) {
            // Safe to insert
            $stmt3 = $pdo->prepare("INSERT INTO appointments (UserID, TransactionID, ApptDate, ApptTime, Status) VALUES (:uid, :tid, :date, :time, 'Pending')");
            $stmt3->execute([
                'uid' => $userId,
                'tid' => $transactionId,
                'date' => $date,
                'time' => $time
            ]);
            $pdo->commit();
            echo json_encode(['success' => true]);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Daily quota reached for this transaction']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Booking failed due to a server error']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list_user') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $date = $_GET['date'] ?? '';

    $whereParams = ['uid' => $userId];
    $whereClauses = ["a.UserID = :uid"];

    if (!empty($search)) {
        $whereClauses[] = "(t.TransactionName LIKE :search)";
        $whereParams['search'] = "%{$search}%";
    }

    if (!empty($status)) {
        $whereClauses[] = "a.Status = :status";
        $whereParams['status'] = $status;
    }

    if (!empty($date)) {
        $whereClauses[] = "a.ApptDate = :date";
        $whereParams['date'] = $date;
    }

    $whereSql = implode(' AND ', $whereClauses);

    // Get total count
    $countSql = "
        SELECT COUNT(a.AppointmentID)
        FROM appointments a 
        INNER JOIN transactions t ON a.TransactionID = t.TransactionID 
        WHERE $whereSql
    ";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($whereParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Get paginated data
    $dataSql = "
        SELECT a.AppointmentID, a.ApptDate, a.ApptTime, a.Status, t.TransactionName 
        FROM appointments a 
        INNER JOIN transactions t ON a.TransactionID = t.TransactionID 
        WHERE $whereSql 
        ORDER BY a.ApptDate DESC, a.ApptTime DESC
        LIMIT $limit OFFSET $offset
    ";
    $stmt = $pdo->prepare($dataSql);
    $stmt->execute($whereParams);
    $appointments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'data' => $appointments,
        'meta' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_rows' => $totalRows,
            'limit' => $limit
        ]
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list_admin') {
    if ($role !== 'Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $date = $_GET['date'] ?? '';

    $whereParams = [];
    $whereClauses = ["1=1"];

    if (!empty($search)) {
        $whereClauses[] = "(u.Email LIKE :search OR t.TransactionName LIKE :search)";
        $whereParams['search'] = "%{$search}%";
    }

    if (!empty($status)) {
        $whereClauses[] = "a.Status = :status";
        $whereParams['status'] = $status;
    }

    if (!empty($date)) {
        $whereClauses[] = "a.ApptDate = :date";
        $whereParams['date'] = $date;
    }

    $whereSql = implode(' AND ', $whereClauses);

    // Get total count for pagination
    $countSql = "
        SELECT COUNT(a.AppointmentID)
        FROM appointments a
        INNER JOIN users u ON a.UserID = u.UserID
        INNER JOIN transactions t ON a.TransactionID = t.TransactionID
        WHERE $whereSql
    ";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($whereParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Get paginated data
    $dataSql = "
        SELECT 
            a.AppointmentID, a.ApptDate, a.ApptTime, a.Status, a.CancelReason,
            u.Email, u.Role,
            t.TransactionName, t.Requirements
        FROM appointments a
        INNER JOIN users u ON a.UserID = u.UserID
        INNER JOIN transactions t ON a.TransactionID = t.TransactionID
        WHERE $whereSql
        ORDER BY a.ApptDate DESC, a.ApptTime DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($dataSql);
    $stmt->execute($whereParams);
    $appointments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'data' => $appointments,
        'meta' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_rows' => $totalRows,
            'limit' => $limit
        ]
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    $data = json_decode(file_get_contents('php://input'), true);
    $appointmentId = $data['id'] ?? null;
    $status = $data['status'] ?? null;
    $reason = $data['reason'] ?? null;
    $newDate = $data['new_date'] ?? null;
    $newTime = $data['new_time'] ?? null;

    if (!$appointmentId || !$status) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }

    if ($role !== 'Admin') {
        // Users can cancel or accept reschedules
        if ($status !== 'Cancelled' && $status !== 'Confirmed') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized action']);
            exit;
        }
        
        // Verify ownership and current status
        $stmtOwner = $pdo->prepare("SELECT UserID, Status FROM appointments WHERE AppointmentID = :id");
        $stmtOwner->execute(['id' => $appointmentId]);
        $appt = $stmtOwner->fetch();
        
        if (!$appt || $appt['UserID'] != $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized ownership']);
            exit;
        }

        if ($status === 'Confirmed' && $appt['Status'] !== 'Pending_Reschedule') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Cannot confirm this appointment']);
            exit;
        }
    }

    if ($status === 'Cancelled') {
        $stmt = $pdo->prepare("UPDATE appointments SET Status = 'Cancelled', CancelReason = :reason WHERE AppointmentID = :id");
        $stmt->execute(['reason' => $reason, 'id' => $appointmentId]);
        echo json_encode(['success' => true]);
    } elseif ($status === 'Rescheduled') {
        // Reschedule actually sets status to Pending_Reschedule
        if (!$newDate || !$newTime) {
            echo json_encode(['success' => false, 'error' => 'New date and time required for rescheduling']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE appointments SET Status = 'Pending_Reschedule', ApptDate = :d, ApptTime = :t WHERE AppointmentID = :id");
        $stmt->execute(['d' => $newDate, 't' => $newTime, 'id' => $appointmentId]);
        echo json_encode(['success' => true]);
    } elseif ($status === 'Confirmed') {
        $stmt = $pdo->prepare("UPDATE appointments SET Status = 'Confirmed' WHERE AppointmentID = :id");
        $stmt->execute(['id' => $appointmentId]);
        echo json_encode(['success' => true]);
    } elseif ($status === 'Completed') {
        $stmt = $pdo->prepare("UPDATE appointments SET Status = 'Completed' WHERE AppointmentID = :id");
        $stmt->execute(['id' => $appointmentId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
