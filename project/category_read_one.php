<?php
require_once 'session_check.php';
checkSession();

// Include database connection
include 'config/database.php';

// Check if category ID is provided via GET
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Category ID is missing.";
    header("Location: category_read.php");
    exit;
}

// Retrieve category ID from GET parameter
$category_id = $_GET['id'];

// Get category details
$query = "SELECT id, category_name, description FROM category WHERE id = :category_id";
$stmt = $con->prepare($query);
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    $_SESSION['message'] = "Category not found.";
    header("Location: category_read.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Category Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Category Details</h1>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>Category ID</th>
                <td><?php echo $category['id']; ?></td>
            </tr>
            <tr>
                <th>Category Name</th>
                <td><?php echo $category['category_name']; ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo $category['description']; ?></td>
            </tr>
        </table>

        <a href="category_read.php" class="btn btn-primary">Back to Category List</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>