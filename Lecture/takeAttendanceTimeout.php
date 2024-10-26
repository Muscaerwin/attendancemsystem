<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Set the default timezone to Philippines
date_default_timezone_set('Asia/Manila');

function getCourseNames($conn) {
    $sql = "SELECT courseCode, name FROM tblcourse";
    $result = $conn->query($sql);
    $courseNames = [];
    if ($result->num_rows > 0) {
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
    if ($result->num_rows > 0) {
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
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $unitNames[] = $row;
        }
    }
    return $unitNames;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendanceData = json_decode(file_get_contents("php://input"), true);

    if (!empty($attendanceData)) {
        // Extract common data (Course, Unit) from the first student (assuming all belong to the same class)
        $course = $attendanceData[0]['course'];
        $unit = $attendanceData[0]['unit'];
        $date = date("Y-m-d");
        $timeOuts = date("h:i A"); // Get the current time in AM/PM format

        // Debugging: Output common data
        echo "Processing timeOuts for Course = $course, Unit = $unit, Date = $date at $timeOuts<br>";

        // Fetch all students who are marked 'present' for the selected course, unit, and date
        $checkSql = "SELECT studentRegistrationNumber FROM tblattendance 
                     WHERE course = '$course' 
                     AND unit = '$unit' 
                     AND dateMarked = '$date' 
                     AND attendanceStatus = 'present'";
        $checkResult = $conn->query($checkSql);

        if ($checkResult->num_rows > 0) {
            // Loop through all present students and update their timeOuts
            while ($row = $checkResult->fetch_assoc()) {
                $studentID = $row['studentRegistrationNumber'];

                // Debugging: Output which student is being updated
                echo "Updating TimeOuts for Student ID: $studentID<br>";

                // Update the timeOuts for each student
                $updateSql = "UPDATE tblattendance 
                               SET timeOuts = '$timeOuts' 
                               WHERE studentRegistrationNumber = '$studentID' 
                               AND course = '$course' 
                               AND unit = '$unit' 
                               AND dateMarked = '$date' 
                               AND attendanceStatus = 'present'";
                if ($conn->query($updateSql) === TRUE) {
                    echo "TimeOuts recorded successfully for Student ID: $studentID at $timeOuts<br>";
                } else {
                    echo "Error updating TimeOuts for Student ID: $studentID: " . $conn->error . "<br>";
                }
            }
        } else {
            echo "No students found marked as present for Course: $course, Unit: $unit, Date: $date<br>";
        }
    } else {
        echo "No attendance data received.<br>";
    }
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

        <form class="lecture-options" id="selectForm">
            <select required name="course" id="courseSelect" onChange="updateTable()">
                <option value="" selected>Select Subjects</option>
                <?php
                $courseNames = getCourseNames($conn);
                foreach ($courseNames as $course) {
                    echo '<option value="' . $course["courseCode"] . '">' . $course["name"] . '</option>';
                }
                ?>
            </select>

            <select required name="unit" id="unitSelect" onChange="updateTable()">
                <option value="" selected>Select Sections</option>
                <?php
                $unitNames = getUnitNames($conn);
                foreach ($unitNames as $unit) {
                    echo '<option value="' . $unit["unitCode"] . '">' . $unit["name"] . '</option>';
                }
                ?>
            </select>

            <select required name="venue" id="venueSelect" onChange="updateTable()">
                <option value="" selected>Select Lecture</option>
                <?php
                $venueNames = getVenueNames($conn);
                foreach ($venueNames as $venue) {
                    echo '<option value="' . $venue["className"] . '">' . $venue["className"] . '</option>';
                }
                ?>
            </select>   
        </form>

        <div class="attendance-button">
            <button id="startButton" class="add">Launch Facial Recognition</button>
            <button id="endButton" class="add" style="display:none">End Attendance Process</button>
            <button id="endAttendance" class="add">END Attendance Taking</button>
        </div>

        <div class="video-container" style="display:none;">
            <video id="video" width="600" height="450" autoplay></video>
            <canvas id="overlay"></canvas>
        </div>

        <div class="table-container">
            <table id="studentTableContainer">
                <!-- Table content will be generated dynamically -->
            </table>
        </div>
    </div>
</section>

<script src="timeout.js"></script>
<script src="../admin/javascript/main.js"></script>

</body>
</html>
