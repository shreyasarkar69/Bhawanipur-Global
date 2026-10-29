<?php
// Local XAMPP defaults: username=root, password=blank.
// Change these values if your MySQL setup uses different credentials.
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "", "event_management_db");

if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed. Import database.sql and check db.php credentials.");
}
$conn->set_charset("utf8mb4");
?>
