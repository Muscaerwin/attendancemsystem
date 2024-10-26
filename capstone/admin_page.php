<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="admin.css">

    <title>Admin_Tubegan</title>
    <style>

body {
      background-image: url('img/loginbg.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      margin: 0;
 
    }

        table {
            width: 80%;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
        }

        .empty {
            padding: 15px;
            text-align: center;
            width: 100%;
        }

        .update-product-form input[type="text"],
        .update-product-form input[type="number"] {
            width: 300px !important;
            height: 40px !important;
            margin-bottom: 10px;
        }

        .update-product-form .btn {
            width: 150px !important;
            height: 40px !important;
        }

        .delete-btn {
            width: 150px !important;
            height: 40px !important;
        }

        .delete-btn:hover {
            background: #ff4f6a;
        }

        .user-count {
            font-size: 24px;
            color: #333;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<?php
include('server.php');
?>

<section id="sidebar" style="background-color: var(--light-yellow);">
    <a href="#" class="brand">
        <i class='bx bxs-smile'></i>
        <span class="text">Admin_Tubeegan</span>
    </a>
    <ul class="side-menu top">
        <li class="active">
            <a href="admin_page.php">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="users.php">
                <i class='bx bxs-shopping-bag-alt'></i>
                <span class="text">Users</span>
            </a>
        </li>
        <li>
            <a href="order_history.php">
                <i class='bx bxs-doughnut-chart'></i>
                <span class="text">Orders</span>
            </a>
        </li>
        <li>
            <a href="admin.php" class="logout">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>

<section id="content">
    <nav>
        
    </nav>

    <main>
        <div class="head-title">
            <div class="left">
            <h1 style="color: white;">Dashboard</h1>
                <ul class="breadcrumb">
                    <li>
                        <a class="active" href="#">Dashboard</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>
                        <a class="active" href="#">Home</a>
                    </li>
                </ul>
            </div>

        </div>

        <ul class="box-info">
            <li>
                <i class='bx bxs-calendar-check'></i>
                <span class="text">
                    <h3>1020</h3>
                    <p>New Order</p>
                </span>
            </li>
            <li>
                <?php
                $conn = mysqli_connect('localhost', 'root', '', 'project') or die('Connection failed');
                $countUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as totalUsers FROM `users`");
                $totalUsers = 0;

                if ($countUsersQuery && mysqli_num_rows($countUsersQuery) > 0) {
                    $userData = mysqli_fetch_assoc($countUsersQuery);
                    $totalUsers = $userData['totalUsers'];
                }

                mysqli_close($conn);
                ?>
                <i class='bx bxs-group'></i>
                <span class="text">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Registered Users</p>
                </span>
            </li>
            <li>
                <i class='bx bxs-dollar-circle'></i>
                <span class="text">
                    <h3>$2543</h3>
                    <p>Total Sales</p>
                </span>
            </li>
        </ul>

        <div class="table-data">
            <div class="order">
                <div class="head">
                    <h3>Products</h3>
                    <i class='bx bx-search'></i>
                    <i class='bx bx-filter'></i>
                </div>

                <form method="post" enctype="multipart/form-data" action="server.php" class="add-product-form">
    <h3>Add a new product</h3>
    <input type="text" name="p_name" placeholder="Enter the product name" class="box" required>
    <input type="number" name="p_price" min="0" placeholder="Enter the product price" class="box" required>
    <input type="text" name="p_sizes" placeholder="Enter product sizes (comma-separated)" class="box" required>
    <input type="text" name="p_color" placeholder="Enter product color" class="box" required>
    <input type="file" name="p_image" accept="image/png, image/jpg, image/jpeg" class="box" required>
    <input type="submit" value="Add the Product" name="add_product" class="btn">
</form>

                <section class="display-product-table">
                    <table>
                        <thead>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Product Sizes</th>
                            <th>Product Color</th>
                            <th>Product Price</th>

                            <th>Action</th>
                        </thead>
                        <tbody>
                        <?php
                        $conn = mysqli_connect('localhost', 'root', '', 'project') or die('Connection failed');
                        $select_products = mysqli_query($conn, "SELECT * FROM `products`");

                        if ($select_products && mysqli_num_rows($select_products) > 0) {
                            while ($row = mysqli_fetch_assoc($select_products)) {
                                $imageUrl = "image.php?image=" . $row['product_img'];
                                ?>
                                <tr>
                                    <td><img src='<?php echo $imageUrl; ?>' style='width: 30%; height: 50%;' alt='Product Image'></td>
                                    <td><?php echo $row['product_name']; ?></td>
                                    <td><?php echo $row['product_sizes']; ?></td>
                                    <td><?php echo $row['product_color']; ?></td>
                                    <td><?php echo $row['product_price']; ?></td>
                                <td>
                                        <!-- Add Update Form -->
                                        <form method="post" action="server.php" class="update-product-form">
                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                    <input type="text" name="updated_product_name" placeholder="New product name" required>
                    <input type="number" name="updated_product_price" min="0" placeholder="New product price" required>
                    <input type="text" name="updated_product_sizes" placeholder="New product sizes" value="<?php echo $row['product_sizes']; ?>" required>
                    <input type="text" name="updated_product_color" placeholder="New product color" value="<?php echo $row['product_color']; ?>" required>
                    <input type="submit" value="Update" name="update_product" class="btn">
                </form>

                                        <!-- Delete Button -->
                                        <a href="admin_page.php?delete=<?php echo $row['product_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            mysqli_close($conn);
                        } else {
                            echo "<tr><td colspan='4' class='empty'>No products added</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </main>
</section>

</body>
</html>
