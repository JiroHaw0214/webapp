<?php
require_once 'session_check.php';
checkSession();

// Include database connection
include 'config/database.php';

// Check if category ID is provided via GET
if (!isset($_GET['id'])) {
    die("Category ID is missing.");
}

// Retrieve category ID from GET parameter
$category_id = $_GET['id'];

// Check if the category exists
$query = "SELECT id, category_name, description FROM category WHERE id = :category_id";
$stmt = $con->prepare($query);
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

// Initialize error message
$error_message = '';

// Handle form submission
if ($_POST) {
    $newCategoryName = $_POST['category_name'];
    $newDescription = $_POST['description'];

    // Check if category name is empty
    if (empty($newCategoryName)) {
        $error_message = "Category name cannot be empty.";
    } 
    if (empty($newDescription)) {
        $error_message = "Description cannot be empty.";
    } 
    else {
        // Update the category
        $updateQuery = "UPDATE category SET category_name = :category_name, description = :description WHERE id = :category_id";
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bindParam(':category_name', $newCategoryName);
        $updateStmt->bindParam(':description', $newDescription);
        $updateStmt->bindParam(':category_id', $category_id);

        if ($updateStmt->execute()) {
            // Re-query the updated data
            $query = "SELECT id, category_name, description FROM category WHERE id = :category_id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            $success_message = "Category updated successfully.";
        } else {
            $error_message = "Failed to update category.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Update Category</h1>
        </div>

        <!-- Display error message if category name is empty -->
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Display success message if category is updated -->
        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Form to update category -->
        <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $category['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo $category['category_name']; ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo $category['description']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="category_read.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>