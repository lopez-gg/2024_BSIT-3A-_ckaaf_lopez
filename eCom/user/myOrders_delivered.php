<?php
include "../connection.php";

session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$sql_fetch_order_items = "SELECT * FROM orders WHERE userid = '$user_id' AND orderStatus = 'Shipped' ORDER BY order_reference_number";
$result_order_items = mysqli_query($conn, $sql_fetch_order_items);
?>

<!DOCTYPE html> 
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <section id="header">
        <a href="#"><img src="../img/logo.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li id="lg-bag"><a href="cart.php"><i class="far fa-shopping-bag"></i></a></li>
                <div class="dropdown">
                    <button class="dropbtn"><img src="../img/people/uicon.jpeg" class="uicon" alt=""></button>
                    <div class="dropdown-content">
                        <a href="my_orders.php">My Orders</a>
                        <a href="../request/logout.php">Logout</a>
                    </div>
                </div>
                <a id="close" href="#"><i class="far fa-times"></i></a>
            </ul>
        </div>
        <div id="mobile">
            <a href="cart.php"><i class="far fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <section id="page-header" class="about-header">
    <div class="myordersnav">
        <ul id="ordernavbar">
            <li><a href="my_orders.php">Pending</a></li>
            <li><a href="myOrders_shipped.php">Shipped</a></li>
            <li><a class="active" href="myOrders_delivered.php">Delivered</a></li>
            <li><a href="myOrders_completed.php">Completed</a></li>
            <li><a href="myOrders_canceled.php">Canceled</a></li>
        </ul>
    </div>

    <h2>#my orders</h2>
    <p>Add your coupon code & SAVE up to 70%!</p>
</section>

<section id="cart" class="section-p1">
        <table width="100%">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Image</td>
                    <td>Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Size</td>
                    <td>Total Price</td>
                    <td>Order Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                $orderId = "";
                $toggle_class = false;

                $sql_fetch_shipped_orders = "SELECT * FROM orders WHERE orderStatus = 'Delivered' ORDER BY order_reference_number";
                $result_order_items = mysqli_query($conn, $sql_fetch_shipped_orders);

                if (mysqli_num_rows($result_order_items) > 0) {
                    while ($row = mysqli_fetch_assoc($result_order_items)) {
                        if ($orderId != $row['order_reference_number']) {
                            if ($orderId != "") {
                                echo "<tr class='$row_class'><td colspan='9' id='totalorder'>Total: $total_price</td></tr>";
                            }
                            $orderId = $row['order_reference_number'];
                            $timestamp = strtotime($row['orderDate']);
                            $date = date('Y-m-d', $timestamp);
                            $subtotal = $row['totalPrice'];
                            $total_price =+ $subtotal;
                            $size = $row['size'];
                            $toggle_class = !$toggle_class;
                            $row_class = $toggle_class ? "order-batch-1" : "order-batch-2";
                            echo "<tr class='$row_class'><td colspan='8' id='refnum'><strong style='color:black';>Order Reference Number: </strong>" . $orderId . "</td><td>";
                            echo '<form id="orderForm' . $orderId . '" class="orderForm" action="../request/update_order_status.php" method="post">';
                            echo '<input type="hidden" name="orderId" value="' . $orderId . '">';
                            echo '<input type="hidden" name="order_status" value="Recieved">';
                            echo '<button type="submit" class="save-btn" name="orderStatus" value="Recieved">Recieved</button>';
                            echo '</form>';
                            echo '</td></tr>';
                            
                        }

                        $item_id = $row['prodId'];
                        $sql_fetch_product = "SELECT * FROM products WHERE prodId = '$item_id'";
                        $result_product = mysqli_query($conn, $sql_fetch_product);
                        $product = mysqli_fetch_assoc($result_product);

                ?>

                        <tr class="<?php echo $row_class; ?>">
                            <td><?php echo $counter?></td>
                            <td><img src="data:image/jpg;base64,<?php echo base64_encode($product['prodpic']); ?>" alt="<?php echo $product['prodname']; ?>" /></td>
                            <td><?php echo $product['prodname']; ?></td>
                            <td>$<span class="price"><?php echo $product['price'];?></span></td>
                            <td><?php echo $row['quantity'];?></td>    
                            <td><?php echo $size;?></td>
                            <td>$<?php echo $subtotal;?></td>
                            <td><?php echo $row['orderDate'];?></td>
                            <td style="color: green; font-weight: 600;"><?php echo $row['orderStatus'];?></td>
                        </tr>

                <?php
                        $counter++;
                    }
                    echo "<tr class='$row_class' ><td colspan='9' id='totalorder'>Total: $total_price</td></tr>";
                    
                } else {
                    echo "<tr><td colspan='9'>No shipped orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
</section>


    <section id="cart-add" class="section-p1">
        <div id="coupon">
            <h3>Apply Coupon</h3>
            <div>
                <input type="text" placeholder="Enter Your Coupon">
                <button class="normal">Apply</button>
            </div>
        </div>
    </section>

    <footer class="section-p1">
        <div class="col">
            <h4>Contact</h4>
            <p><strong>Address: </strong> Purok Earth, Centro Occidental, Buhi - Polangui Rd, Polangui, 4506 Albay</p>
            <p><strong>Phone:</strong>(+63) 9462243688</p>
            <p><strong>Hours:</strong> 9:00 - 4:00, Mon - Sat</p>
            <div class="follow">
                <h4>Follow Us</h4>
                <div class="icon">
                    <i class="fab fa-facebook-f"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-pinterest-p"></i>
                    <i class="fab fa-youtube"></i>
                </div>
            </div>
        </div>

        <div class="col">
            <h4>About</h4>
            <a href="#">About Us</a>
            <a href="#">Delivery Information</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
            <a href="#">Contact Us</a>
        </div>

        <div class="col">
            <h4>My Account</h4>
            <a href="#">My Orders</a>
            <a href="cart.php">View Cart</a>
            <a href="#">My Wishlist</a>
            <a href="#">Track My Order</a>
            <a href="#">Help</a>
        </div>

        <div class="col install">
        <p>Payment Options</p>
                <img src="../img/pay/pays.png" alt="">
        </div>

        <div class="copyright">
            <p>© 2024, Ckaaf Clothing Shop</p>
        </div>
    </footer>
   
    <style>
        .myordersnav{
            margin-bottom: 50px;
            margin-top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 14px;
        }
        #ordernavbar {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #ordernavbar li {
            list-style: none;
            padding: 0 20px;
            position: relative;
        }

        #ordernavbar li a {
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            color: #e9e9e9;
        }

        #ordernavbar li a:hover,
        #ordernavbar li a.active {
            color: lightseagreen;
        }

        #ordernavbar li a.active::after,
        #ordernavbar li a:hover::after {
            content: "";
            width: 30%;
            height: 2px;
            background: #088178;
            position: absolute;
            bottom: -4px;
            left: 20px;
        }
        .order-batch-1 {
            background-color: #f9f9f9;
        }
        .order-batch-2 {
            background-color: #e9e9e9;
        }
        #totalorder, #refnum{
            font-weight: 700;
            color: #088178;
        }

        
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');

        :root {
        --green: #27ae60;
        --black: #333;
        --white: #fff;
        --bg-color: #eee;
        --box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
        --border: .1rem solid var(--black);
        }

        * {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        outline: none;
        border: none;
        text-decoration: none;
        text-transform: capitalize;
        background-color: e9e9e9;
        }

        html {
        font-size: 45.5%;
        overflow-x: hidden;
        }

        .container {
        max-width: 3300px;
        padding: 2rem;
        margin: 10px auto;
        }

        .product-display {
        margin: 2rem 0;
        }

        .product-display .product-display-table {
        width: 100%;
        text-align: center;
        }

        .product-display .product-display-table thead {
        background-color: lightseagreen;
        }

        .product-display .product-display-table th {
        padding: 1rem;
        font-size: 2rem;
        }

        .product-display .product-display-table td {
        padding: 1rem;
        font-size: 2rem;
        border-bottom: var(--border);
        }

        .order-status {
        display: flex;
        align-items: center;
        }

        .order-status form {
        margin-left: 10px;
        }


        .order-table {
        width: 100%;
        border-collapse: collapse;
        }

        .order-table th, .order-table td {
        padding: 10px;
        text-align: center;
        }

        .order-table tr.order-group td {
        background-color: inherit !important;
        font-weight: bold;
        }

        .order-table tr.even td{
        background-color: #e6fff9 !important;
        }

        .order-table tr.odd td {
        background-color: #ffffff !important;
        }

        .statbtn{
        background-color: #F9E8D9;
        padding: 10px;
        border-top-left-radius: 7px;
        border-bottom-left-radius: 7px;
        }

        .save-btn {
        background-color: lightseagreen;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 7px;
        }

        .save-btn:hover {
        background-color: lightsalmon;
        }

        @media (max-width: 991px) {
        html {
            font-size: 55%;
        }
        }

        @media (max-width: 768px) {
        .product-display {
            overflow-y: scroll;
        }

        .product-display .product-display-table {
            width: 80rem;
        }
        }

        @media (max-width: 450px) {
        html {
            font-size: 50%;
        }
        }
    </style>

    <script src="script.js"></script>
</body>

</html>