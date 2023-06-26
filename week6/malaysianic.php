<!DOCTYPE html>
<html lang="en">

<head>
    <title>Malaysian IC Information</title>
    <meta charset="UTF-8">
    <!-- 确保网页在各种浏览器和操作系统上正确地显示字符，并避免乱码问题。 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 网页视口:控制网页在不同设备上的显示方式;将视口宽度设置为设备宽度;初始缩放比例设置为1.0，即不进行缩放，使网页内容按照实际大小显示。 -->
</head>

<body>
    <form method="POST" action="">
        <!-- POST方法将表单数据发送到服务器进行处理;目标URL为空，意味着表单数据将被提交到当前页面的相同URL。 -->
        <label for="ic">Malaysian IC Number:</label>
        <input type="text" name="ic" required>
        <button type="submit" name="submit">Submit</button>
    </form>
    <?php
    if (isset($_POST['submit'])) {
        // 检查是否存在名为 "submit" 的表单字段的值被提交
        // Get the input IC number
        $icNumber = $_POST['ic'];

        // Validate the IC number
        if (validateIC($icNumber)) {
            // Extract the date of birth from the IC number
            $dob = getDOB($icNumber);
            $formattedDOB = formatDate($dob);

            // Find the Chinese Zodiac and related image
            $chineseZodiac = findChineseZodiac($dob);
            $chineseZodiacImage = getChineseZodiacImage($chineseZodiac);

            // Find the Star Zodiac and related image
            $starZodiac = findStarZodiac($dob);
            $starZodiacImage = getStarZodiacImage($starZodiac);

            // Find the place of birth and related image
            $placeOfBirth = findPlaceOfBirth($icNumber);
            $placeOfBirthImage = getPlaceOfBirthImage($placeOfBirth);

            // Print the retrieved information
            echo "Date of Birth: $formattedDOB<br>";
            echo "Chinese Zodiac: $chineseZodiac<br>";
            echo "<img src=\"$chineseZodiacImage\"><br>";
            echo "Star Zodiac: $starZodiac<br>";
            echo "<img src=\"$starZodiacImage\"><br>";
            echo "Place of Birth: $placeOfBirth<br>";
            echo "<img src=\"$placeOfBirthImage\"><br>";
        } else {
            echo "Invalid Malaysian IC Number.";
        }
    }

    // Validate the IC number format
    function validateIC($icNumber)
    {
        // 执行正则表达式匹配,\d=表示一个数字
        return preg_match('/^\d{6}-\d{2}-\d{4}$/', $icNumber);
    }

    // Extract the date of birth from the IC number
    function getDOB($icNumber)
    {
        return substr($icNumber, 0, 6); // Assumes the date of birth is the first 6 digits
    }

    // Format the date of birth as "MMM DD, YYYY" (e.g., MAY 20, 2000)
    function formatDate($dob)
    {
        $dateObj = DateTime::createFromFormat('ymd', $dob);
        return $dateObj->format('M d, Y');
    }

    // Find the Chinese Zodiac based on the date of birth
    function findChineseZodiac($dob)
    {
        $dateObj = DateTime::createFromFormat('ymd', $dob);
        $year = intval($dateObj->format('Y'));
        $zodiacAnimals = array(
            'Rat', 'Ox', 'Tiger', 'Rabbit', 'Dragon', 'Snake',
            'Horse', 'Goat', 'Monkey', 'Rooster', 'Dog', 'Pig'
        );
        $zodiacIndex = ($year - 1900) % 12;
        return $zodiacAnimals[$zodiacIndex];
    }

    function getChineseZodiacImage($zodiac)
    {
        $zodiacImages = array(
            'Rat' => "img/rat.jpg",
            'Ox' => "img/ox.jpg",
            'Tiger' => "img/tiger.jpg",
            'Rabbit' => "img/rabbit.jpg",
            'Dragon' => "img/dragon.jpg",
            'Snake' => "img/snake.jpg",
            'Horse' => "img/hosre.jpg",
            'Goat' => "img/goat.jpg",
            'Monkey' => "img/monkey.jpg",
            'Rooster' => "img/chicken.jpg",
            'Dog' => "img/dog.jpg",
            'Pig' => "img/pig.jpg"
        );

        return 'http://localhost/wad/week6/' . $zodiacImages[$zodiac];
    }

    // Find the Star Zodiac based on the date of birth
    function findStarZodiac($dob)
    {
        $dateObj = DateTime::createFromFormat('ymd', $dob);
        $month = intval($dateObj->format('m'));
        $day = intval($dateObj->format('d'));
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
            $startMonth = $dates['start']['month'];
            $startDay = $dates['start']['day'];
            $endMonth = $dates['end']['month'];
            $endDay = $dates['end']['day'];

            if (($month === $startMonth && $day >= $startDay) || ($month === $endMonth && $day <= $endDay)) {
                return $sign;
            }
        }

        return 'Unknown';
    }

    function getStarZodiacImage($zodiac)
    {
        $zodiacImages = array(
            'Aquarius' => "img/aquarius.jpg",
            'Pisces' => "img/pisces.jpg",
            'Aries' => "img/aries.jpg",
            'Taurus' => "img/taurus.jpg",
            'Gemini' => "img/gemini.jpg",
            'Cancer' => "img/cancer.jpg",
            'Leo' => "img/leo.jpg",
            'Virgo' => "img/virgo.jpg",
            'Libra' => "img/libra.jpg",
            'Scorpio' => "img/scorpio.jpg",
            'Sagittarius' => "img/sagittarius.jpg",
            'Capricorn' => "img/capricorn.jpg"
        );

        return 'http://localhost/wad/week6/' . $zodiacImages[$zodiac];
    }

    // Find the place of birth based on the IC number
    function findPlaceOfBirth($icNumber)
    {
        $placeOfBirthCodes = array(
            '01' => 'Johor',
            '02' => 'Kedah',
            '03' => 'Kelantan',
            '04' => 'Melaka',
            '05' => 'Negeri Sembilan',
            '06' => 'Pahang',
            '07' => 'Pulau Pinang',
            '08' => 'Perak',
            '09' => 'Perlis',
            '10' => 'Selangor',
            '11' => 'Terengganu',
            '12' => 'Sabah',
            '13' => 'Sarawak',
            '14' => 'Wilayah Persekutuan Kuala Lumpur',
            '15' => 'Wilayah Persekutuan Labuan',
            '16' => 'Wilayah Persekutuan Putrajaya'
        );

        $placeOfBirthCode = substr($icNumber, 7, 2);

        if (isset($placeOfBirthCodes[$placeOfBirthCode])) {
            return $placeOfBirthCodes[$placeOfBirthCode];
        } else {
            return 'Not Found';
        }
    }

    function getPlaceOfBirthImage($placeOfBirth)
    {
        $placeOfBirthImages = array(
            'Johor' => "img/johor.png",
            'Kedah' => "img/kedah.png",
            'Kelantan' => "img/kelantan.png",
            'Melaka' => "img/melaka.png",
            'Negeri Sembilan' => "img/ns.png",
            'Pahang' => "img/pahang.png",
            'Pulau Pinang' => "img/pulaupinang.png",
            'Perak' => "img/perak.png",
            'Perlis' => "img/perlis.png",
            'Selangor' => "img/selangor.png",
            'Terengganu' => "img/terenganu.png",
            'Sabah' => "img/sabah.png",
            'Sarawak' => "img/sarawak.png",
            'Wilayah Persekutuan Kuala Lumpur' => "img/kl.png",
            'Wilayah Persekutuan Labuan' =>"img/labuan.png",
            'Wilayah Persekutuan Putrajaya' => "img/putrajaya.png"
        );

        return 'http://localhost/wad/week6/' .  $placeOfBirthImages[$placeOfBirth];
    }
    ?>
</body>

</html>