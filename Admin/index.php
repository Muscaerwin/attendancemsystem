
<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.png" rel="icon">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/topbar.php';?>
    <section class="main">
        <?php include 'includes/sidebar.php';?>
    <div class="main--content">
        <div class="overview">
                <div class="title">
                    <h2 class="section--title">Overview</h2>
                    <select name="date" id="date" class="dropdown">
                        <option value="today">Today</option>
                        <option value="lastweek">Last Week</option>
                        <option value="lastmonth">Last Month</option>
                        <option value="lastyear">Last Year</option>
                        <option value="alltime">All Time</option>
                    </select>
                </div>
                <div class="cards">
                    <div class="card card-1">
                        <?php 
                        $query1=mysqli_query($conn,"SELECT * from tblstudents");                       
                        $students = mysqli_num_rows($query1);
                        ?>
                        <div class="card--data">
                            <div class="card--content">
                                <h5 class="card--title">Registered Students</h5>
                                <h1><?php echo $students;?></h1>
                            </div>
                            <i class="ri-user-2-line card--icon--lg"></i>
                        </div>
                       
                    </div>
                   
                    
                   
                    <div class="card card-1">
                        <?php 
                        $query1=mysqli_query($conn,"SELECT * from tbllecture");                       
                        $totalLecture = mysqli_num_rows($query1);
                        ?>
                        <div class="card--data">
                            <div class="card--content">
                                <h5 class="card--title">Registered Lectures</h5>
                                <h1><?php echo $totalLecture;?></h1>
                            </div>
                            <i class="ri-user-line card--icon--lg"></i>
                        </div>
                       
                    </div>
                </div>
            </div>
           
            <div class="table-container">
            <a href="createLecture.php" style="text-decoration:none;"> <div class="title">
                    <h2 class="section--title">Lectures</h2>
                    <button class="add"><i class="ri-add-line"></i>Add lecture</button>
                </div>
            </a>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email Address</th>
                                <th>Phone No</th>
                                <th>Faculty</th>
                                <th>Date Registered</th>
                                <th>Settings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                        <?php
                         $sql = "SELECT l.*, f.facultyName
                         FROM tbllecture l
                         LEFT JOIN tblfaculty f ON l.facultyCode = f.facultyCode";
                          $result = $conn->query($sql);
                          if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["firstName"] . "</td>";
                            echo "<td>" . $row["emailAddress"] . "</td>";
                            echo "<td>" . $row["phoneNo"] . "</td>";
                            echo "<td>" . $row["facultyName"] . "</td>";
                            echo "<td>" . $row["dateCreated"] . "</td>";
                            echo "<td><span><i class='ri-edit-line edit'></i><i class='ri-delete-bin-line delete'></i></span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }

                    ?>                     
                                
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table-container">
            <a href="createStudent.php" style="text-decoration:none;"> <div class="title">
                    <h2 class="section--title">Students</h2>
                    <button class="add"><i class="ri-add-line"></i>Add Student</button>
                </div>
            </a>
                <div class="table">
                    <table>
                        <thead >
                            <tr>
                                <th>Registration No</th>
                                <th>Name</th>
                                <th>Faculty</th>
                                <th>Subjects</th>
                                <th>Email</th>
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
                            echo "<td>" . $row["firstName"] . "</td>";
                            echo "<td>" . $row["faculty"] . "</td>";
                            echo "<td>" . $row["courseCode"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td><span><i class='ri-edit-line edit'></i><i class='ri-delete-bin-line delete'></i></span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }

                    ?>     
                            
                        </tbody>
                    </table>
                </div>
                
            </div>
            
                </div>
                
            </div> 
        </div>
    </section>
    <script src="javascript/main.js"></script>
      <?php include 'includes/footer.php';?>
     
 

</body>

</html>