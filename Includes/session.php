<?php
session_start(); 
if (!isset($_SESSION['userId'])) {
    echo "<script type='text/javascript'>
    window.location = '../index.php';
    </script>";
    exit(); // It's good practice to exit after a redirect to stop further execution.
}
?>
