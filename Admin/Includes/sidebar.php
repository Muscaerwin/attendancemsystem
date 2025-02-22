<div class="sidebar">
            <ul class="sidebar--items">
                <li>
                    <a href="index.php">
                        <span class="icon icon-1"><i class="ri-layout-grid-line"></i></span>
                        <span class="sidebar--item">Dashboard</span>
                    </a>
                </li>
               
                
                <li>
                    <a href="createLecture.php">
                        <span class="icon icon-1"><i class="ri-user-line"></i></span>
                        <span class="sidebar--item">manage lectures</span>
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

