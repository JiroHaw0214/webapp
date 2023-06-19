<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exercise Week 4 Question 1</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <form method="POST" action=""> <!-- 使用 POST 方法提交数据到当前页面的表单 -->
            <div class="form-group my-3">
                <label for="firstName">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName">
            </div>
            <div class="form-group my-3">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName">
            </div>
            <button type="submit" class="btn btn-primary mb-3" name="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['submit'])) { // 如果用户点击了提交按钮
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            // 获取用户输入的名字和姓氏
            $formattedFirstName = ucwords(strtolower($firstName));
            $formattedLastName = ucwords(strtolower($lastName));
            // 将名字和姓氏格式化为首字母大写，其余字母小写的形式
            echo "Name: " . $formattedLastName . " " . $formattedFirstName;
            // 显示格式化后的名字
        }
        ?>
    </div>
</body>

</html>