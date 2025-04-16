<?php
include('includes/db_connection.php');

$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$sql = "SELECT DATE(appointment_datetime) as date, COUNT(*) as count 
        FROM appointments 
        WHERE MONTH(appointment_datetime) = ? 
        AND YEAR(appointment_datetime) = ?
        GROUP BY DATE(appointment_datetime)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$bookingCounts = [];
while ($row = $result->fetch_assoc()) {
    $bookingCounts[$row['date']] = $row['count'];
}

header('Content-Type: application/json');
echo json_encode($bookingCounts);
?> 