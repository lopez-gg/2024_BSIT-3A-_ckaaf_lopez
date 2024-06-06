<?php
include "connection.php";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';

// Check if the query parameter is a valid column name to avoid SQL injection
$allowed_columns = ['prodname', 'price', 'prod_description']; // Replace with actual column names
$column = in_array($query, $allowed_columns) ? $query : 'prod_description'; // Use a default column if the input is not valid

// Build the SQL query dynamically with the specified column
$sql = "SELECT * FROM products WHERE prodname LIKE ? OR prod_description LIKE ?";
$stmt = $conn->prepare($sql);

// Bind the parameter for the value
$likeQuery = '%' . $query . '%';
$stmt->bind_param('ss', $likeQuery,  $likeQuery);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
$conn->close();
?>

<!-- Include the search results in the admin panel -->
<?php include 'search_view.php'; ?>
<!-- Display error notification if no products are found -->
<?php if (empty($orders)): ?>
    <p>No products found.</p>
<?php endif; ?>
