<?php
include '../Includes/dbcon.php'; // Database connection
include '../Includes/session.php'; // Session management

header('Content-Type: text/html; charset=UTF-8');

// Check connection
if ($conn->connect_error) {
    die('{"success": false, "message": "Database connection failed."}');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfid = isset($_POST['rfid']) ? $conn->real_escape_string($_POST['rfid']) : '';

    if ($rfid) {
        $sql = "
            SELECT s.*, a.timeIn, a.attendanceStatus, s.studentImage1, s.studentImage2 
            FROM tblstudents s 
            LEFT JOIN tblattendance a ON s.registrationNumber = a.studentRegistrationNumber 
            WHERE s.rfid = '$rfid'
        ";
        $result = $conn->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                echo json_encode([
                    'success' => true,
                    'rfid' => $row['rfid'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'registrationNumber' => $row['registrationNumber'],
                    'email' => $row['email'],
                    'timeIn' => $row['timeIn'],
                    'attendanceStatus' => $row['attendanceStatus'],
                    'studentImage1' => $row['studentImage1'],
                    'studentImage2' => $row['studentImage2'],
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => '']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No RFID provided.']);
    }
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Input Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
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
        }

        body {
            animation: backgroundChange 8s infinite;
            color: #00796b;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 2px solid #00796b;
            text-align: center;
            font-family: 'Comic Neue', cursive;
        }

        h2 {
            color: #d81b60;
            font-size: 30px;
            font-weight: bold;
            text-shadow: 2px 2px #fff;
        }

        input[type="text"] {
            width: calc(100% - 20px);
            padding: 15px;
            margin: 15px 0;
            border: 2px solid #d81b60;
            border-radius: 8px;
            font-size: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .result {
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
            line-height: 1.5;
        }

        button {
            background-color: #ffeb3b;
            color: #000;
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            position: absolute;
            left: 20px;
            bottom: 20px;
        }

        button:hover {
            background-color: #ffd600;
        }

        img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thrones Rainbow RFID Scanner</h2>
    <input type="text" id="rfidInput" placeholder="Scan your RFID here..." autofocus>
    
    <p id="message" class="result"></p>
</div>

<button onclick="window.location.href='index.php'">Return to Dashboard</button>

<script>
    document.getElementById('rfidInput').addEventListener('input', function() {
        const rfidValue = this.value.trim();
        const messageElement = document.getElementById('message');

        if (rfidValue) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'rfid=' + encodeURIComponent(rfidValue)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const studentFolder = data.registrationNumber; // Folder name based on registration number
                    const image1URL = `../Lecture/labels/${studentFolder}/1.png`;

                    const img1 = new Image();

                    img1.onload = function() {
                        messageElement.innerHTML += `<strong>Image 1:</strong> <div style="text-align: center;"><img src="${image1URL}" alt="${data.firstName} ${data.lastName} Image 1" style="width: 75%; height: auto;"></div><br>`;
                    };
                    img1.onerror = function() {
                        messageElement.innerHTML += `<strong>Image 1 not found.</strong><br>`;
                    };

                    img1.src = image1URL;

                    messageElement.innerHTML += ` 
                        <strong>Name:</strong> ${data.firstName} ${data.lastName}<br>
                        <strong>Registration Number:</strong> ${data.registrationNumber}<br>
                        <strong>Email:</strong> ${data.email}<br>
                        <strong>Time In:</strong> ${data.timeIn}<br>
                        <strong>Attendance Status:</strong> ${data.attendanceStatus}<br>
                        <strong>RFID Scanned:</strong> ${data.rfid}<br>
                    `;
                    messageElement.style.color = '#00796b';

                    // Clear the input after successful scan
                    this.value = '';
                } else {
                    messageElement.textContent = data.message;
                    messageElement.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                messageElement.textContent = 'Error: ' + error.message;
                messageElement.style.color = 'red';
            });
        } else {
            messageElement.textContent = 'Please scan an RFID.';
            messageElement.style.color = 'red';
        }
    }); 

    window.onload = function() {
        document.getElementById('rfidInput').focus();
    };
</script>

</body>
</html>
