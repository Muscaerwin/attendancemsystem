<?php 
include 'Includes/dbcon.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="admin/img/logo/attnlg.png" rel="icon">
    <title>Thrones Rainbow Registration</title>
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
        .btn-register {
            background-color: #ff6600;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .btn-register:hover {
            background-color: #ff9933;
        }
        .messageDiv {
            color: #ffcc00;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container" id="register">
    <h1>REGISTER</h1>
    <div id="messageDiv" class="messageDiv" style="display:none;"></div>

    <form method="post" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="example@gmail.com" required>
        <input type="password" name="password" placeholder="password" required>
        <select required name="userType">
            <option value="">--Select User Roles--</option>
            <option value="Administrator">Administrator</option>
            <option value="Lecture">Lecture</option>
        </select>
        <input type="submit" class="btn-register" value="Register" name="register" />
    </form>
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
if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $userType = $_POST['userType'];

    // Check if the email already exists
    $checkQuery = "
        SELECT emailAddress FROM tbladmin WHERE emailAddress='$email' 
        UNION 
        SELECT emailAddress FROM tbllecture WHERE emailAddress='$email'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        echo "<script>showMessage('Email already registered!');</script>";
    } else {
        // Insert into the appropriate table based on user type
        if ($userType == "Administrator") {
            $insertQuery = "INSERT INTO tbladmin (firstName, emailAddress, password) VALUES ('$name', '$email', '$password')";
        } else {
            $insertQuery = "INSERT INTO tbllecture (firstName, emailAddress, password) VALUES ('$name', '$email', '$password')";
        }

        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>showMessage('Registration successful!');</script>";
        } else {
            echo "<script>showMessage('Error: " . $conn->error . "');</script>";
        }
    }
}

?>

</body>
</html>
