<!DOCTYPE HTML>
<html>

<head>
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .price-cell {
            text-align: right;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Products List</h1>
        </div>

        <!-- Search form -->
        <div class="mb-3">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="form-inline">
                <!-- get 获取搜索关键字 -->
                <label for="search_keyword" class="form-label">Search Product:</label>
                <!-- 如果搜索关键字存在，你可以将它保存在变量 $search_keyword 中 -->
                <input type="text" name="search_keyword" class="form-control mx-sm-2" id="search_keyword" placeholder="Enter product name" value="<?php echo isset($_GET['search_keyword']) ? htmlspecialchars($_GET['search_keyword'], ENT_QUOTES) : ''; ?>">
                <!-- htmlspecialchars用于防止搜索中使用html -->
                <!-- 用于用户输入搜索关键字  -->
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- PHP code to read records will be here -->
        <?php
        // include database connection
        include 'config/database.php';

        // delete message prompt will be here

        // Check if search keyword is provided
        $search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';

        // select all data
        $query = "SELECT id, name, description, price, promotion_price FROM products";

        if (!empty($search_keyword)) {
            // Add the search condition to the query
            $query .= " WHERE name LIKE :search_keyword";
        }

        $query .= " ORDER BY id DESC";

        // prepare query statement
        $stmt = $con->prepare($query);

        // Bind the search keyword parameter if it exists
        if (!empty($search_keyword)) {
            $search_keyword = '%' . $search_keyword . '%';
            //可以搜索到product的开头，中间还有结尾。
            $stmt->bindParam(':search_keyword', $search_keyword);
        }

        // execute query
        $stmt->execute();

        // this is how to get number of rows returned
        $num = $stmt->rowCount();

        // link to create record form
        echo "<a href='create.php' class='btn btn-primary m-b-1em'>Create New Product</a>";

        // check if more than 0 record found
        if ($num > 0) {

            // data from database will be here
            echo "<table class='table table-hover table-responsive table-bordered'>"; //start table

            // creating our table heading
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Name</th>";
            echo "<th>Description</th>";
            echo "<th>Price</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            // table body will be here
            // retrieve our table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row// Check if promotion_price is set
                // this will make $row['firstname'] to just $firstname only
                extract($row);
                // creating new table row per record
                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td>{$name}</td>";
                echo "<td>{$description}</td>";
                //如果RM10 会display 10.00, Check if there is a promotion price
                echo "<td class='price-cell'>";
                if ($promotion_price > 0) {
                    echo "<del>" . number_format($price, 2) . "</del><br>";
                    echo number_format($promotion_price, 2);
                } else {
                    echo number_format($price, 2);
                }
                echo "</td>";
                echo "<td>";
                // read one record
                echo "<a href='product_read_one.php?id={$id}' class='btn btn-info me-3'>Read</a>";
                //me - 3 = margin right 3

                // we will use these links in the next part of this post
                echo "<a href='product_update.php?id={$id}' class='btn btn-primary me-3'>Edit</a>";

                // we will use this links on next part of this post
                echo "<a href='#' onclick='delete_product({$id});' class='btn btn-danger'>Delete</a>";
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

    <!-- confirm delete record will be here -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>