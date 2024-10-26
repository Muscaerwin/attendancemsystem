<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

function getCourseNames($conn) {
    $sql = "SELECT courseCode, name FROM tblcourse";
    $result = $conn->query($sql);

    $courseNames = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courseNames[] = $row;
        }
    }
    return $courseNames;
}

function getFacultyNames($conn) {
    $sql = "SELECT facultyCode, facultyName FROM tblfaculty";
    $result = $conn->query($sql);

    $facultyNames = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $facultyNames[] = $row;
        }
    }
    return $facultyNames;
}

// Update student
if (isset($_POST['updateStudent'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $registrationNumber = $_POST['registrationNumber'];
    $rfid = $_POST['rfid'];
    $faculty = $_POST['faculty'];
    $courseCode = $_POST['course'];
    $artDrawing = $_POST['artDrawing'];
    $languageSpeaking = $_POST['languageSpeaking'];
    $religionHistory = $_POST['religionHistory'];
    $genKnowledge = $_POST['genKnowledge'];
    $studentId = $_POST['studentId'];

    // Compute GWA
    $gwa = ($artDrawing + $languageSpeaking + $religionHistory + $genKnowledge) / 4;

    $query = mysqli_query($conn, "UPDATE tblstudents 
        SET firstName='$firstName', lastName='$lastName', email='$email', 
            registrationNumber='$registrationNumber', rfid='$rfid', 
            faculty='$faculty', courseCode='$courseCode', 
            ArtDrawing='$artDrawing', LanguageSpeaking='$languageSpeaking', 
            ReligionHistory='$religionHistory', GenKnowledge='$genKnowledge', 
            GWA='$gwa'
        WHERE Id='$studentId'");

    if ($query) {
        $message = "Student details updated successfully.";
    } else {
        $message = "Error updating student: " . mysqli_error($conn);
    }
}

// Delete student
if (isset($_POST['deleteStudent'])) {
    $studentId = $_POST['studentId'];
    $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$studentId'");
    if ($query) {
        $message = "Student deleted successfully.";
    } else {
        $message = "Error deleting student: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.png" rel="icon">
    <title>AMS - Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/topbar.php';?>

    <section class="main">
        <?php include "Includes/sidebar.php";?>

        <div class="main--content"> 
            <div id="overlay"></div>
            <div id="messageDiv" class="messageDiv" style="display:none;"></div>

            <div class="table-container">
                <div class="title" id="editStudent">
                    <h2 class="section--title">Viewing Grades</h2>
                </div>
                
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th>Registration No</th>
                                <th>RFID</th>
                                <th>Name</th>
                                <th>Faculty</th>
                                <th>Section</th>
                                <th>Email</th>
                                <th>Art&Drawings</th>
                                <th>Language&Speaking</th>
                                <th>Religion&History</th>
                                <th>General Knowledge</th>
                                <th>GWA</th>
                                <th>Settings</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM tblstudents";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["registrationNumber"] . "</td>";
                                echo "<td>" . $row["rfid"] . "</td>";
                                echo "<td>" . $row["firstName"] . "</td>";
                                echo "<td>" . $row["faculty"] . "</td>";
                                echo "<td>" . $row["courseCode"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . $row["ArtDrawing"] . "</td>";
                                echo "<td>" . $row["LanguageSpeaking"] . "</td>";
                                echo "<td>" . $row["ReligionHistory"] . "</td>";
                                echo "<td>" . $row["GenKnowledge"] . "</td>";
                                echo "<td>" . $row["GWA"] . "</td>";
                                echo "<td>
                                    <span class='edit' data-id='" . $row["Id"] . "'><i class='ri-edit-line'></i></span>
                                    <form method='post' style='display:inline;'>
                                        <input type='hidden' name='studentId' value='" . $row["Id"] . "'>
                                        <button type='button' class='deleteBtn' data-id='" . $row["Id"] . "'><i class='ri-delete-bin-line'></i></button>
                                    </form>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No records found</td></tr>";
                        }
                        ?>     
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="formDiv--" id="editStudentForm" style="display:none; max-height: 80vh; overflow-y: auto;">
                <form method="post">
                    <div style="display:flex; justify-content:space-between;">
                        <div class="form-title">
                            <p>Edit Student</p>
                        </div>
                        <div>
                            <span class="close">&times;</span>
                        </div>
                    </div>
                    <input type="hidden" name="studentId" id="studentId">
                    <div>
                        <input type="text" name="firstName" id="firstName" placeholder="First Name"> 
                        <input type="text" name="lastName" id="lastName" placeholder="Last Name">
                        <input type="email" name="email" id="email" placeholder="Email Address">
                        <input type="text" name="registrationNumber" id="registrationNumber" placeholder="Registration Number">
                        <input type="text" name="rfid" id="rfid" placeholder="RFID">
                        
                        <select name="faculty" id="faculty">
                            <option value="" selected>Select Faculty</option>
                            <?php
                            $facultyNames = getFacultyNames($conn);
                            foreach ($facultyNames as $faculty) {
                                echo '<option value="' . $faculty["facultyCode"] . '">' . $faculty["facultyName"] . '</option>';
                            }
                            ?>
                        </select>
                        <select name="course" id="course">
                            <option value="" selected>Select Course</option>
                            <?php
                            $courseNames = getCourseNames($conn);
                            foreach ($courseNames as $course) {
                                echo '<option value="' . $course["courseCode"] . '">' . $course["name"] . '</option>';
                            }
                            ?>
                        </select>

                        <input type="text" name="artDrawing" id="artDrawing" placeholder="Art & Drawing">
                        <input type="text" name="languageSpeaking" id="languageSpeaking" placeholder="Language & Speaking">
                        <input type="text" name="religionHistory" id="religionHistory" placeholder="Religion & History">
                        <input type="text" name="genKnowledge" id="genKnowledge" placeholder="General Knowledge">
                    </div>
                    
                    <input type="submit" class="btn-submit" value="Update Student" name="updateStudent" />
                </form>
            </div>
        </div>
    </section>
    <script src="javascript/main.js"></script>
    <script>
        // Handle edit button click and display student data in the form
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');

                fetch(`getStudentDetails.php?id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate form with student data
                        document.getElementById('studentId').value = studentId;
                        document.getElementById('firstName').value = data.firstName;
                        document.getElementById('lastName').value = data.lastName;
                        document.getElementById('email').value = data.email;
                        document.getElementById('registrationNumber').value = data.registrationNumber;
                        document.getElementById('rfid').value = data.rfid;
                        document.querySelector('select[name="faculty"]').value = data.faculty;
                        document.querySelector('select[name="course"]').value = data.courseCode;
                        document.getElementById('artDrawing').value = data.ArtDrawing;
                        document.getElementById('languageSpeaking').value = data.LanguageSpeaking;
                        document.getElementById('religionHistory').value = data.ReligionHistory;
                        document.getElementById('genKnowledge').value = data.GenKnowledge;

                        // Show the form
                        document.getElementById('editStudentForm').style.display = 'block';
                        document.getElementById('overlay').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(error => console.error('Error fetching student details:', error));
            });
        });

        // Close form functionality
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('editStudentForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        // Display success message if exists
        <?php if (isset($message)): ?>
            document.getElementById('messageDiv').innerHTML = "<?php echo $message; ?>";
            document.getElementById('messageDiv').style.display = 'block';
            setTimeout(() => {
                document.getElementById('messageDiv').style.display = 'none';
            }, 3000);
        <?php endif; ?>

        // Handle delete confirmation
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this student?")) {
                    const form = this.closest('form');
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
