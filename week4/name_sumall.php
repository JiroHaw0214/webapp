<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exercise Week 4 Question 4</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="form-group my-3">
                <label for="number">Enter a Number:</label>
                <input type="text" class="form-control" id="number" name="number">
            </div>
            <button type="submit" class="btn btn-primary mb-3" name="submit">Submit</button>
        </form>

        <p id="result">
            <?php
            if (isset($_POST['submit'])) {
                //检查是否点击了提交按钮。
                $number = $_POST['number'];
                //从表单中获取用户输入的数字。
                if (empty($number) || !is_numeric($number)) {
                    echo '<span class="text-danger">Please fill in a number.</span>';
                    //检查数字是否为空或非数值。如果是，显示错误消息 Please fill in a number.
                } else {
                    $sum = 0;
                    $number = intval($number);
                    // 将用户输入的数字转换为整数类型。
                    for ($i = $number; $i >= 1; $i--) {
                        //使用循环从用户输入的数字开始递减到1。
                        $sum += $i;
                    }

                    echo "{$number}+";
                    for ($i = $number - 1; $i >= 1; $i--) {
                        echo "{$i}";
                        if ($i > 1) {
                            echo "+";
                            //在数字之间添加"+"号，除非数字是1。
                        }
                    }

                    echo "={$sum}";
                }
            }
            ?>
        </p>
    </div>
</body>

</html>