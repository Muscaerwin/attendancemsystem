<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Top Bar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            position: relative;
            width: 100%;
            background-color: #f9f9f9; /* Light background for contrast */
        }

        .header {
            height: 60px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Space out elements */
            border-bottom: 2px solid #f1f1f1;
            background-color: #fff; /* White background for top bar */
            padding: 0 20px; /* Add horizontal padding */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo span {
            color: #5073fb;
        }

        .search--notification--profile {
            display: flex;
            align-items: center;
        }

        .search {
            background-color: #f1f4f8;
            border-radius: 25px; /* More rounded corners */
            width: 300px; /* Adjusted width for responsiveness */
            padding: 5px;
            display: flex;
            align-items: center;
            margin-right: 20px; /* Space between search and notifications */
        }

        .search input {
            background-color: transparent;
            outline: none;
            border: none;
            text-indent: 15px;
            width: 85%;
            font-size: 1em; /* Smaller font size */
        }

        .search button {
            outline: none;
            border: none;
            border-radius: 50%;
            background-color: #fff;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search button i {
            font-size: 1rem;
            color: #5073fb;
        }

        .notification--profile {
            display: flex;
            align-items: center;
        }

        .picon {
            margin-left: 20px;
            font-size: 1rem; /* Smaller font size */
            padding: 5px;
            border-radius: 5px;
            position: relative; /* For better positioning */
        }

        .lock {
            color: #5073fb;
            background-color: rgba(80, 115, 251, .2);
            padding: 5px 10px; /* Added padding for better aesthetics */
            border-radius: 15px; /* More rounded edges */
        }

        .profile {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main {
            position: relative;
            width: 100%;
            min-height: calc(100vh - 60px);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column; /* Stack elements on small screens */
                height: auto;
                padding: 10px; /* Less padding for smaller screens */
            }
            .search {
                width: 100%; /* Full width search bar */
                margin: 10px 0; /* Margin for spacing */
            }
        }
    </style>
</head>
<body>

<section class="header">
    <div class="logo">
        <i class="ri-menu-line icon icon-0 menu"></i>
        <h2>Attendance M<span>s</span></h2>
    </div>
    <div class="search--notification--profile">
        <div id="searchInput" class="search">
            <input type="text" id="searchText" placeholder="Search .....">
            <button onclick="searchItems()">
                <i class="ri-search-2-line"></i>
            </button>
        </div>
        <div class="notification--profile">
            <div class="picon lock">
                
            </div>
            <div class="picon profile">
                <img src="img/user.png" alt="Profile Image">
            </div>
        </div>
    </div>
</section>

<script>
    function searchItems() {
        var input = document.getElementById('searchText').value.toLowerCase();
        var rows = document.querySelectorAll('table tr'); 

        rows.forEach(function(row) {
            var cells = row.querySelectorAll('td'); 
            var found = false;

            cells.forEach(function(cell) {
                if (cell.innerText.toLowerCase().includes(input)) { 
                    found = true;
                }
            });

            row.style.display = found ? '' : 'none'; // Simplified conditional
        });
    }
</script>

</body>
</html>
