<?php
// Database connection parameters
$db_host = '193.203.184.121';
$db_user = 'u911550082_canext';
$db_pass = 'Milk@sdk14';
$db_name = 'u911550082_canext';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Sanitize user input
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

/**
 * Execute SQL query and return result
 * @param string $sql SQL query
 * @return mysqli_result|bool Query result
 */
function executeQuery($sql) {
    global $conn;
    return $conn->query($sql);
}

/**
 * Get total number of records from a table
 * @param string $table Table name
 * @param string $where Optional WHERE clause
 * @return int Number of records
 */
function getRecordCount($table, $where = '') {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}
?> 