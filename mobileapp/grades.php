<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['studentRegistrationNumber'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Include database connection
include './Includes/dbcon.php';

// Fetch student details and grades
$registrationNumber = $_SESSION['studentRegistrationNumber']; // Get the registration number from session

// Fetch student name and grades
$stmt = $conn->prepare("SELECT firstName, lastName, ArtDrawing, LanguageSpeaking, ReligionHistory, GenKnowledge, GWA FROM tblstudents WHERE registrationNumber = ?");
$stmt->bind_param("s", $registrationNumber);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$studentName = $student['firstName'] . ' ' . $student['lastName']; // Combine first name and last name

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grades</title>
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
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
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
            position: relative; /* Added for proper centering */
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

        .button-container {
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 18px;
            margin: 10px 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Grades</h2>

        <!-- Grades Display Form -->
        <form>
            <label for="studentName">Student Name:</label>
            <input type="text" id="studentName" value="<?php echo htmlspecialchars($studentName); ?>" disabled>

            <label for="artDrawing">Art Drawing:</label>
            <input type="text" id="artDrawing" value="<?php echo htmlspecialchars($student['ArtDrawing']); ?>" disabled>

            <label for="languageSpeaking">Language Speaking:</label>
            <input type="text" id="languageSpeaking" value="<?php echo htmlspecialchars($student['LanguageSpeaking']); ?>" disabled>

            <label for="religionHistory">Religion History:</label>
            <input type="text" id="religionHistory" value="<?php echo htmlspecialchars($student['ReligionHistory']); ?>" disabled>

            <label for="genKnowledge">General Knowledge:</label>
            <input type="text" id="genKnowledge" value="<?php echo htmlspecialchars($student['GenKnowledge']); ?>" disabled>

            <label for="gwa">GWA:</label>
            <input type="text" id="gwa" value="<?php echo htmlspecialchars($student['GWA']); ?>" disabled>
        </form>

        <!-- Buttons for Navigation -->
        <div class="button-container">
            <a href="attendance.php" class="button">View Attendance</a>
            <a href="login.php" class="button">Logout</a>
        </div>
    </div>
</body>
</html>
