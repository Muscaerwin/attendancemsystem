<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/2.5.0/remixicon.min.css">
    <style>
        /* Sidebar Styles */
        .sidebar {
            position: fixed; /* Fixed positioning for full height */
            top: 0;
            left: 0;
            height: 100%;
            width: 250px; /* Default width */
            background-color: #fff;
            padding: 20px; /* Adjusted padding */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 2px solid #f1f1f1;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            overflow-y: auto; /* Allow scrolling */
            transition: width 0.3s; /* Smooth transition for width changes */
        }

        .sidebar.active {
            width: 80px; /* Reduced width when active */
            overflow: hidden; /* Hide overflow content */
        }

        li {
            list-style: none;
        }

        a {
            text-decoration: none;
        }

        .sidebar--items a,
        .sidebar--bottom-items a {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1rem; /* Adjusted font size for better readability */
            color: #000;
            padding: 10px;
            border-radius: 8px; /* Adjusted border radius */
            transition: background-color 0.3s; /* Smooth hover effect */
        }

        .sidebar--items a:hover,
        .sidebar--bottom-items a:hover {
            background-color: #5073fb; /* Hover background color */
            color: #fff; /* Hover text color */
        }

        #active--link {
            background-color: #5073fb; /* Active link background */
            color: #fff; /* Active link text color */
        }

        .sidebar--bottom-items li:last-child a {
            margin-bottom: 0; /* Remove margin for last item */
        }

        .icon {
            margin-right: 10px; /* Reduced spacing for icons */
            font-size: 1.2rem; /* Adjusted icon size */
        }

        .icon-1 {
            color: blue;
            font-weight: bold;
        }

        .icon-2 {
            color: red;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px; /* Adjust sidebar width for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 150px; /* Further reduce width for very small screens */
            }

            .sidebar--items a,
            .sidebar--bottom-items a {
                font-size: 0.9rem; /* Smaller font size */
            }

            .icon {
                margin-right: 5px; /* Smaller margin for icons */
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <ul class="sidebar--items">
        <li>
            <a href="index.php">
                <span class="icon icon-1"><i class="ri-layout-grid-line"></i></span>
                <span class="sidebar--item">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="attendance.php">
                <span class="icon icon-1"><i class="ri-calendar-line"></i></span>
                <span class="sidebar--item">Attendance</span>
            </a>
        </li>
        <li>
            <a href="grades.php">
                <span class="icon icon-1"><i class="ri-pencil-line"></i></span>
                <span class="sidebar--item">Grades</span>
            </a>
        </li>
        <li>
            <a href="history.php">
                <span class="icon icon-1"><i class="ri-history-line"></i></span>
                <span class="sidebar--item">History</span>
            </a>
        </li>
        <li>
            <a href="createLecture.php">
                <span class="icon icon-1"><i class="ri-user-line"></i></span>
                <span class="sidebar--item">Manage Lectures</span>
            </a>
        </li>
        <li>
            <a href="createStudent.php">
                <span class="icon icon-1"><i class="ri-user-line"></i></span>
                <span class="sidebar--item">Manage Students</span>
            </a>
        </li>
        <li>
            <a href="RFID.php">
                <span class="icon icon-1"><i class="ri-user-line"></i></span>
                <span class="sidebar--item">RFID</span>
            </a>
        </li>
    </ul>
    <ul class="sidebar--bottom-items">
        <li>
            <a href="#">
                <span class="icon icon-2"><i class="ri-settings-3-line"></i></span>
                <span class="sidebar--item">Settings</span>
            </a>
        </li>
        <li>
            <a href="../logout.php">
                <span class="icon icon-2"><i class="ri-logout-box-r-line"></i></span>
                <span class="sidebar--item">Logout</span>
            </a>
        </li>
    </ul>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var currentUrl = window.location.href.split('?')[0]; // Strip query params
    var links = document.querySelectorAll('.sidebar a');
    links.forEach(function(link) {
        if (link.href === currentUrl) {
            link.id = 'active--link';
        }
    });
});
</script>

</body>
</html>
