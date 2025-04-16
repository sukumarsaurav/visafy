<?php
include('includes/db_connection.php');

// Check if request is POST and has necessary data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = isset($_POST['status']) ? sanitize($_POST['status']) : null;
    $payment_status = isset($_POST['payment_status']) ? sanitize($_POST['payment_status']) : null;
    
    $updates = [];
    if ($status) {
        $updates[] = "status = '$status'";
    }
    if ($payment_status) {
        $updates[] = "payment_status = '$payment_status'";
    }
    
    if (!empty($updates)) {
        $update_str = implode(', ', $updates);
        $sql = "UPDATE appointments SET $update_str WHERE id = $booking_id";
        
        if (executeQuery($sql)) {
            echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No updates provided']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 