<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['studentRegistrationNumber'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Include database connection
include './Includes/dbcon.php';

// Fetch student details and attendance
$registrationNumber = $_SESSION['studentRegistrationNumber']; // Get the registration number from session

// Fetch student name
$stmt = $conn->prepare("SELECT firstName, lastName FROM tblstudents WHERE registrationNumber = ?");
$stmt->bind_param("s", $registrationNumber);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$studentName = $student['firstName'] . ' ' . $student['lastName']; // Combine first name and last name

// Fetch today's attendance details
$stmt = $conn->prepare("SELECT timeIn, timeOuts, dateMarked FROM tblattendance WHERE studentRegistrationNumber = ? ORDER BY dateMarked DESC LIMIT 1");
$stmt->bind_param("s", $registrationNumber);
$stmt->execute();
$result = $stmt->get_result();
$attendanceRecord = $result->fetch_assoc();

// Fetch previous attendance records
$stmt = $conn->prepare("SELECT timeIn, timeOuts, dateMarked FROM tblattendance WHERE studentRegistrationNumber = ? ORDER BY dateMarked DESC");
$stmt->bind_param("s", $registrationNumber);
$stmt->execute();
$previousResults = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Attendance</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Add Font Awesome -->
    <style>
        @keyframes backgroundChange {
            0% { background-color: #ff7e5f; }
            25% { background-color: #feb47b; }
            50% { background-color: #ff6a6a; }
            75% { background-color: #6a82fb; }
            100% { background-color: #ff7e5f; }
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Comic Neue', cursive;
        }

        body {
            animation: backgroundChange 8s infinite;
            color: #00796b;
            display: flex;
            justify-content: center; /* Center the content */
            align-items: flex-start; /* Align items to the start */
            padding-top: 50px; /* Add padding to avoid overlap */
        }

        .container {
            max-width: 600px;
            max-height: 80vh; /* Set a maximum height for scrolling */
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 2px solid #00796b;
            text-align: center;
            overflow-y: auto; /* Enable vertical scrolling */
        }

        h2 {
            color: #d81b60;
            font-size: 32px;
            font-weight: bold;
            text-shadow: 2px 2px #fff;
            margin-bottom: 20px;
        }

        form {
            border: 2px solid #d81b60;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        form label {
            font-weight: bold;
            color: #00796b;
        }

        form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #d81b60;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
        }

        .attendance-records {
            margin-top: 20px;
            text-align: left;
            border: 2px solid #d81b60;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
        }

        .attendance-records h3 {
            color: #d81b60;
        }

        .attendance-records ul {
            list-style: none;
            padding: 0;
        }

        .attendance-records li {
            padding: 10px;
            border-bottom: 1px solid #d81b60;
        }

        .attendance-records li:last-child {
            border-bottom: none;
        }

        .button-container {
            margin-top: 20px;
        }

        .button {
            background-color: #ffeb3b;
            color: #000;
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 10px;
        }

        .button:hover {
            background-color: #ffd600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Today's Attendance</h2>

        <!-- Today's Attendance Form -->
        <form>
            <label for="studentName">Student Name:</label>
            <input type="text" id="studentName" value="<?php echo htmlspecialchars($studentName); ?>" disabled>

            <label for="timeIn">Time In:</label>
            <input type="text" id="timeIn" value="<?php echo htmlspecialchars($attendanceRecord['timeIn'] ?? 'N/A'); ?>" disabled>

            <label for="timeOut">Time Out:</label>
            <input type="text" id="timeOut" value="<?php echo htmlspecialchars($attendanceRecord['timeOuts'] ?? 'N/A'); ?>" disabled>

            <label for="date">Date:</label>
            <input type="text" id="date" value="<?php echo htmlspecialchars($attendanceRecord['dateMarked'] ?? 'N/A'); ?>" disabled>
        </form>

        <!-- Previous Attendance Records -->
        <div class="attendance-records">
            <h3>Previous Attendance Records:</h3>
            <ul>
                <?php while ($prevRecord = $previousResults->fetch_assoc()): ?>
                    <li>
                        Date: <?php echo htmlspecialchars($prevRecord['dateMarked']); ?> - 
                        Time In: <?php echo htmlspecialchars($prevRecord['timeIn']); ?>, 
                        Time Out: <?php echo htmlspecialchars($prevRecord['timeOuts']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Button Container -->
        <div class="button-container">
            <button class="button" onclick="location.href='grades.php'">View Grades</button>
            <button class="button" onclick="location.href='login.php'">Log Out</button>
        </div>
    </div>
</body>
</html>
