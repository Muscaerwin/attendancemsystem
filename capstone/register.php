<?php 
include 'Includes/dbcon.php'; // Ensure this file establishes a connection to the database
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="admin/img/logo/attnlg.png" rel="icon">
    <title>Thrones Rainbow Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/loginStyle.css">
    <style>
        body {
            background-image: url('capstone/img/playing-1.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            max-width: 400px;
            background: rgba(0, 0, 0, 0.9);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
        }

        h1 {
            color: #ffcc00;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ffcc00;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-login {
            background-color: #ff6600;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }

        .btn-login:hover {
            background-color: #ff9933;
        }

        .icons i {
            font-size: 30px;
            color: #ffcc00;
            margin: 0 10px;
            cursor: pointer;
        }

        .or {
            margin: 20px 0;
            color: #ffcc00;
        }

        .messageDiv {
            color: #ffcc00;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container" id="signin">
    <h1>Register</h1>
    <div id="messageDiv" class="messageDiv" style="display:none;"></div>

    <form method="post" action="">
        <select required name="userType">
            <option value="">--Select User Roles--</option>
            <option value="Administrator">Administrator</option>
            <option value="Lecture">Lecture</option>
        </select>
        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="example@gmail.com" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        
        <input type="submit" class="btn-login" value="Register" name="register" />
    </form>
    <p class="or"> --------or-------- </p>
    <div class="icons">
        <i class="fab fa-google"></i>
        <i class="fab fa-facebook"></i>
    </div>
</div> 

<script>
function showMessage(message) {
    var messageDiv = document.getElementById('messageDiv');
    messageDiv.style.display = "block";
    messageDiv.innerHTML = message;
    messageDiv.style.opacity = 1;
    setTimeout(function() {
        messageDiv.style.opacity = 0;
    }, 5000);
}
</script>

<?php
if (isset($_POST['register'])) {
    $userType = $_POST['userType'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password
    $confirmPassword = md5($_POST['confirm_password']); // Hash confirm password

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        echo "<script>showMessage('Passwords do not match!');</script>";
    } else {
        // Prepare SQL based on user type
        if ($userType == "Administrator") {
            $query = "INSERT INTO tbladmin (firstName, lastName, emailAddress, password) VALUES (?, ?, ?, ?)";
        } elseif ($userType == "Lecture") {
            $query = "INSERT INTO tbllecture (firstName, lastName, emailAddress, password) VALUES (?, ?, ?, ?)";
        } else {
            echo "<script>showMessage('Invalid User Type!');</script>";
            exit();
        }

        // Prepare and bind
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to index.php after successful registration
                header("Location: index.php");
                exit();
            } else {
                echo "<script>showMessage('Error: " . $stmt->error . "');</script>"; // Show error
            }
            $stmt->close();
        } else {
            echo "<script>showMessage('Error: Could not prepare the statement.');</script>";
        }
    }
}
?>

</body>
</html>
