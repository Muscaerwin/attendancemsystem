<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<div class="table">
    <table>
        <thead>
            <tr>
                <th>Registration No</th>
                <th>Name</th>
                <th>Subjects</th>
                <th>Section</th>
                <th>Lecture</th>
                <th>Attendance</th>
                <th>Settings</th>
            </tr>
        </thead>
        <tbody id="studentTableContainer">
            <?php
            if (isset($_POST['courseID']) && isset($_POST['unitID']) && isset($_POST['venueID'])) {

                $courseID = $_POST['courseID'];
                $unitID = $_POST['unitID'];
                $venueID = $_POST['venueID'];

                $sql = "SELECT * FROM tblStudents WHERE courseCode = '$courseID'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        $registrationNumber = $row["registrationNumber"];
                        echo "<td>" . htmlspecialchars($registrationNumber) . "</td>";
                        echo "<td>" . htmlspecialchars($row["firstName"]) . " " . htmlspecialchars($row["lastName"]) . "</td>";
                        echo "<td>" . htmlspecialchars($courseID) . "</td>";
                        echo "<td>" . htmlspecialchars($unitID) . "</td>";
                        echo "<td>" . htmlspecialchars($venueID) . "</td>";
                        
                        // Check the attendance status (default to Absent)
                        echo "<td class='attendanceStatus' id='status_$registrationNumber'>Absent</td>"; 
                        
                        // Add a button to mark attendance
                        echo "<td>
                                <span>
                                    <i class='ri-edit-line edit' onclick=\"markAttendance('$registrationNumber', '$courseID', '$unitID', '$venueID')\"></i>
                                    <i class='ri-delete-bin-line delete' onclick=\"deleteRecord('$registrationNumber')\"></i>
                                </span>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No records found</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    function markAttendance(registrationNumber, courseID, unitID, venueID) {
        // Send an AJAX request to update attendance
        const attendanceStatus = 'present'; // This can be set based on user action (e.g., button click)
        const currentTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

        fetch('path_to_your_php_script.php', { // Update with the correct path
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                studentID: registrationNumber,
                attendanceStatus: attendanceStatus,
                course: courseID,
                unit: unitID,
                venue: venueID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('status_' + registrationNumber).innerText = attendanceStatus.charAt(0).toUpperCase() + attendanceStatus.slice(1);
                // Optionally, record the timeOuts in the display
                // This assumes you want to show timeOuts in the same table; adjust as needed.
                // You may need to update your database to store this information.
            } else {
                alert('Failed to update attendance.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteRecord(registrationNumber) {
        // Implement delete functionality
    }
</script>
