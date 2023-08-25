<?php
require_once 'session_check.php';
checkSession();
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>Products List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <?php
        include 'includes/navbar.php';
        ?>

        <div class="p-3">
            <h1>Products List</h1>
        </div>

        <form class="d-flex" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
            <input class="form-control me-2 mb-2" type="text" name="search" placeholder="Search" aria-label="Search" value="<?php echo isset($_GET['search_keyword']) ? htmlspecialchars($_GET['search_keyword'], ENT_QUOTES) : ''; ?>">
            <button class="btn btn-outline-success mb-2" type="submit">Search</button>
        </form>

        <!-- PHP code to read records will be here -->
        <?php
        // include database connection
        include 'config/database.php';
        $action = isset($_GET['action']) ? $_GET['action'] : "";

        // if it was redirected from delete.php
        if ($action == 'deleted') {
            echo "<div class='alert alert-success'>Record was deleted.</div>";
        }
        // delete message prompt will be here
        $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
        $query = "SELECT p.id, p.name, p.description, p.price, p.promotion_price, p.image, c.category_name 
                  FROM products p 
                  LEFT JOIN category c ON p.category_id = c.id";
        //   set p as alias for products table and c for category table
        // 这是LEFT JOIN查询的一部分。它基于共同的列将"products"表（"p"）与"category"表（"c"）连接起来
        if (!empty($searchKeyword)) {
            $query .= " WHERE p.name LIKE :keyword";
            $searchKeyword = "%{$searchKeyword}%";
        }
        $query .= " ORDER BY p.id DESC";
        $stmt = $con->prepare($query);
        if (!empty($searchKeyword)) {
            $stmt->bindParam(':keyword', $searchKeyword);
        }
        // select all data
        $stmt->execute();

        // this is how to get the number of rows returned
        $num = $stmt->rowCount();

        // link to create record form
        echo "<a href='product_create.php' class='btn btn-primary mb-3 mt-3 '>Add New Product</a>";

        // check if more than 0 records found
        if ($num > 0) {

            // data from the database will be here
            echo "<table class='table table-hover table-responsive table-bordered'>"; //start table

            // creating our table heading
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Name</th>";
            echo "<th>Description</th>";
            echo "<th>Price</th>";
            echo "<th>Category</th>";
            echo "<th>Image</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            // table body will be here
            // retrieve our table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['firstname'] to just $firstname only
                extract($row);
                // creating a new table row per record
                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td>{$name}</td>";
                echo "<td>{$description}</td>";

                echo "<td class='text-end'>";
                if (!empty($promotion_price)) {
                    // Display promotion price if available
                    echo "<div class='text-decoration-line-through'>" . number_format($price, 2) . "</div>";
                    echo number_format($promotion_price, 2);
                } else {
                    // Display regular price
                    echo number_format($price, 2);
                }
                echo "</td>";
                echo "<td>{$category_name}</td>"; // Display category name
                if ($image == "") {
                    echo '<td><img src="img/product.jpg" height="100px" alt=""></td>';
                } else {
                    echo "<td><img src='uploads/{$image}' class='img-fluid' alt='Product Image'></td>";
                }
                echo "<td>";
                // read one record
                echo "<a href='product_read_one.php?id={$id}' class='btn btn-info me-3 '>Read</a>";
                // we will use these links in the next part of this post
                echo "<a href='product_update.php?id={$id}' class='btn btn-primary me-3 mt-1'>Edit</a>";
                echo "<a href='#' onclick='product_delete({$id});'  class='btn btn-danger mt-1'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }

            // end table
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger'>No records found.</div>";
        }
        ?>

    </div> <!-- end .container -->
    <script type='text/javascript'>
        // confirm record deletion
        function product_delete(id) {

            if (confirm('Are you sure?')) {
                // if user clicked ok,
                // pass the id to delete.php and execute the delete query
                window.location = 'product_delete.php?id=' + id;
            }
        }
    </script>

    <!-- confirm delete record will be here -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>