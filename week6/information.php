<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exercise Week 6 Question 1</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- 引入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <!-- 创建一个 POST 方法的表单 -->
        <form method="POST" action="">
            <div class="row">
                <div class="col">
                     <!-- 选择日的下拉菜单 -->
                    <label for="day">Day</label>
                    <select class="form-select" aria-label="Default select example" name="day">
                        <?php
                        // 循环生成 1 到 31 的选项
                        for ($i = 1; $i <= 31; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                     <!-- 选择月的下拉菜单 -->
                    <label for="day">Month</label>
                    <select class="form-select" name="month" required>
                        <?php
                        // 创建一个包含月份名称的数组
                        $months = array(
                            'January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'
                        );
                        foreach ($months as $month) {
                            echo "<option value=\"$month\">$month</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <!-- 选择年的下拉菜单 -->
                    <label for="day">Year</label>
                    <select class="form-select" aria-label="Default select example" name="year">
                        <?php
                        // 获取当前年份
                        $currentYear = date('Y');
                        // 循环生成从 1900 年到当前年份的选项
                        for ($i = 1900; $i <= $currentYear; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <!-- 提交按钮 -->
            <button type="submit" class="btn btn-primary my-3" name="submit">Submit</button>
        </form>

        <?php
        // 检查表单是否被提交
        if (isset($_POST['submit'])) {
            // 获取表单提交的日、月、年的值
            $day = $_POST['day'];
            $month = $_POST['month'];
            $year = $_POST['year'];
            // 检查日期的有效性
            $isDateValid = checkDateValidity($day, $month, $year);
            if ($isDateValid) {
                echo "Valid Date: $day $month $year<br>";
                // 查找对应年份的生肖
                $zodiac = findChineseZodiac($year);
                // 查找对应日期的星座
                $starSign = findStarSign($month, $day);
                echo "Chinese Zodiac: $zodiac<br>";
                echo "Star Sign: $starSign";
            } else {
                echo "Invalid Date: $day $month $year";
            }
        }

        // 检查日期的有效性
        function checkDateValidity($day, $month, $year)
        {
            return checkdate(date('m', strtotime($month)), $day, $year);
        }

        // 查找对应年份的生肖
        function findChineseZodiac($year)
        {
            $zodiacAnimals = array(
                'Rat', 'Ox', 'Tiger', 'Rabbit', 'Dragon', 'Snake',
                'Horse', 'Goat', 'Monkey', 'Rooster', 'Dog', 'Pig'
            );
            $zodiacIndex = ($year - 1900) % 12;
            return $zodiacAnimals[$zodiacIndex];
        }

        // 查找对应日期的星座
        function findStarSign($month, $day)
        {
            $starSigns = array(
                'Aquarius' => array('start' => array('month' => 1, 'day' => 20), 'end' => array('month' => 2, 'day' => 18)),
                'Pisces' => array('start' => array('month' => 2, 'day' => 19), 'end' => array('month' => 3, 'day' => 20)),
                'Aries' => array('start' => array('month' => 3, 'day' => 21), 'end' => array('month' => 4, 'day' => 19)),
                'Taurus' => array('start' => array('month' => 4, 'day' => 20), 'end' => array('month' => 5, 'day' => 20)),
                'Gemini' => array('start' => array('month' => 5, 'day' => 21), 'end' => array('month' => 6, 'day' => 20)),
                'Cancer' => array('start' => array('month' => 6, 'day' => 21), 'end' => array('month' => 7, 'day' => 22)),
                'Leo' => array('start' => array('month' => 7, 'day' => 23), 'end' => array('month' => 8, 'day' => 22)),
                'Virgo' => array('start' => array('month' => 8, 'day' => 23), 'end' => array('month' => 9, 'day' => 22)),
                'Libra' => array('start' => array('month' => 9, 'day' => 23), 'end' => array('month' => 10, 'day' => 22)),
                'Scorpio' => array('start' => array('month' => 10, 'day' => 23), 'end' => array('month' => 11, 'day' => 21)),
                'Sagittarius' => array('start' => array('month' => 11, 'day' => 22), 'end' => array('month' => 12, 'day' => 21)),
                'Capricorn' => array('start' => array('month' => 12, 'day' => 22), 'end' => array('month' => 1, 'day' => 19))
            );
            foreach ($starSigns as $sign => $dates) {
                $start = $dates['start'];
                $end = $dates['end'];
                if (($month == $start['month'] && $day >= $start['day']) || ($month == $end['month'] && $day <= $end['day'])) {
                    return $sign;
                }
            }
        }
        ?>
    </div>
</body>

</html>