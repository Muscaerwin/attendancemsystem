<?php
include 'dbcon.php'; // Include your connection file

if ($conn) {
    echo "Connected successfully";
} else {
    echo "Connection failed: " . $conn->connect_error;
}
?>
