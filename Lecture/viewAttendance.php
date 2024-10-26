<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

function getCourseNames($conn) {
    $sql = "SELECT courseCode, name FROM tblcourse";
    $result = $conn->query($sql);
    $courseNames = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courseNames[] = $row;
        }
    }
    return $courseNames;
}

function getVenueNames($conn) {
    $sql = "SELECT className FROM tblvenue";
    $result = $conn->query($sql);
    $venueNames = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $venueNames[] = $row;
        }
    }
    return $venueNames;
}

function getUnitNames($conn) {
    $sql = "SELECT unitCode, name FROM tblunit";
    $result = $conn->query($sql);
    $unitNames = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $unitNames[] = $row;
        }
    }
    return $unitNames;
}

function getAttendanceRecords($conn) {
    $sql = "SELECT studentRegistrationNumber, course, unit, attendanceStatus, attendanceStatusTimeout, dateMarked, timeIn, timeOuts 
            FROM tblattendance"; // Including both statuses
    $result = $conn->query($sql);
    $attendanceRecords = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attendanceRecords[] = $row;
        }
    }
    return $attendanceRecords;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendanceData = json_decode(file_get_contents("php://input"), true);

    if (!empty($attendanceData)) {
        foreach ($attendanceData as $data) {
            $studentID = $data['studentID'];
            $attendanceStatus = $data['attendanceStatus']; // e.g., present
            $attendanceStatusTimeout = $data['attendanceStatusTimeout']; // e.g., left
            $course = $data['course'];
            $unit = $data['unit'];
            $date = date("Y-m-d");
            $currentTime = date("H:i");

            // Check for existing attendance record
            $checkSql = "SELECT * FROM tblattendance 
                         WHERE studentRegistrationNumber = ? 
                         AND course = ? 
                         AND unit = ? 
                         AND dateMarked = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("ssss", $studentID, $course, $unit, $date);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                // Record exists
                $existingRecord = $result->fetch_assoc();
                
                if ($attendanceStatusTimeout === 'left') {
                    // Update the timeOuts and attendanceStatus for existing records
                    $stmt = $conn->prepare("UPDATE tblattendance SET attendanceStatusTimeout = ?, timeOuts = ? 
                                             WHERE studentRegistrationNumber = ? 
                                             AND course = ? 
                                             AND unit = ? 
                                             AND dateMarked = ?");
                    $stmt->bind_param("ssssss", $attendanceStatusTimeout, $currentTime, $studentID, $course, $unit, $date);
                    if ($stmt->execute()) {
                        echo "Attendance Updated Successfully for $course : $unit on $date at $currentTime<br>";
                    } else {
                        echo "Error updating attendance data: " . $stmt->error . "<br>";
                    }
                }
            } else {
                // No record exists, insert new
                if ($attendanceStatus === 'present') {
                    $stmt = $conn->prepare("INSERT INTO tblattendance(studentRegistrationNumber, course, unit, attendanceStatus, attendanceStatusTimeout, dateMarked, timeIn)  
                                             VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssss", $studentID, $course, $unit, $attendanceStatus, $attendanceStatusTimeout, $date, $currentTime);
                    
                    if ($stmt->execute()) {
                        echo "Attendance Recorded Successfully for $course : $unit on $date at $currentTime<br>";
                    } else {
                        echo "Error inserting attendance data: " . $stmt->error . "<br>";
                    }
                }
            }
            $stmt->close();
        }
    } else {
        echo "No attendance data received.<br>";
    }
}

// Download functionality remains unchanged
if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_records.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student ID', 'Course', 'Unit', 'Attendance Status', 'Attendance Status Timeout', 'Date Marked', 'Time In', 'Time Outs']); // Updated header

    $attendanceRecords = getAttendanceRecords($conn);
    foreach ($attendanceRecords as $record) {
        fputcsv($output, $record);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="../admin/img/logo/attnlg.png" rel="icon">
    <title>Lecture Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="face-api.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/topbar.php'; ?>
<section class="main">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main--content">
        <div id="messageDiv" class="messageDiv" style="display:none;"></div>

        <div class="download-button">
            <a href="?download=true" class="add">Download Attendance Records</a>
        </div>

        <div class="table-container">
            <table id="studentTableContainer">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Subjects</th>
                        <th>Section</th>
                        <th>Attendance Status</th> <!-- Displaying attendanceStatus -->
                        <th>Attendance Status Timeout</th> <!-- Displaying attendanceStatusTimeout -->
                        <th>Date Marked</th>
                        <th>Time In</th>
                        <th>Time Out</th> <!-- Added Time Outs column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $attendanceRecords = getAttendanceRecords($conn);
                    if (empty($attendanceRecords)) {
                        echo '<tr><td colspan="8">No records found.</td></tr>'; // Updated colspan to 8
                    } else {
                        foreach ($attendanceRecords as $record) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($record["studentRegistrationNumber"]) . '</td>';
                            echo '<td>' . htmlspecialchars($record["course"]) . '</td>';
                            echo '<td>' . htmlspecialchars($record["unit"]) . '</td>';
                            echo '<td>' . htmlspecialchars($record["attendanceStatus"]) . '</td>'; // Display attendanceStatus
                            echo '<td>' . htmlspecialchars($record["attendanceStatusTimeout"]) . '</td>'; // Display attendanceStatusTimeout
                            echo '<td>' . htmlspecialchars($record["dateMarked"]) . '</td>';
                            echo '<td>' . ($record["attendanceStatus"] === 'present' ? htmlspecialchars($record["timeIn"]) : 'N/A') . '</td>'; 
                            echo '<td>' . (!empty($record["timeOuts"]) ? htmlspecialchars($record["timeOuts"]) : 'N/A') . '</td>'; // Show timeOuts if available
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="script.js"></script>
<script src="../admin/javascript/main.js"></script>

</body>
</html>
