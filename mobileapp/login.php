<?php
session_start(); // Start the session

// Include database connection
include './Includes/dbcon.php';

$errorMessage = ''; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'];
    $registrationNumber = $_POST['password'];

    // Prepare SQL statement to validate login
    $stmt = $conn->prepare("SELECT firstName, lastName FROM tblstudents WHERE email = ? AND registrationNumber = ?");
    $stmt->bind_param("ss", $email, $registrationNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching record was found
    if ($result->num_rows > 0) {
        // Successful login
        $student = $result->fetch_assoc(); // Fetch the student's name
        // Store the student's information in the session
        $_SESSION['studentName'] = $student['firstName'] . ' ' . $student['lastName'];
        $_SESSION['studentRegistrationNumber'] = $registrationNumber; // Store registration number for attendance
        header('Location: attendance.php'); // Redirect to attendance.php
        exit();
    } else {
        $errorMessage = 'Invalid email or registration number';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            font-family: "Comic Sans MS", cursive, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #87CEFA; /* Sky blue background */
            margin: 0;
            color: #FF69B4; /* Hot pink text */
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #FF4500; /* OrangeRed */
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
            font-weight: bold;
        }
        .login-container input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #FF69B4;
            border-radius: 10px;
            transition: border-color 0.3s;
        }
        .login-container input:focus {
            border-color: #00FF00; /* Lime green */
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #FFD700; /* Gold */
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #FF8C00; /* DarkOrange */
        }
        #error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="loginForm" method="POST" action="">
            <label for="username">Email</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Registration Number</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Sign in now!</button>
        </form>
        <?php if (!empty($errorMessage)): ?>
            <div id="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
