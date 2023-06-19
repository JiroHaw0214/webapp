<!DOCTYPE html>
<html>

<body>

    <div class="container">
        <?php
        // Get today's date
        $today = date("Y-m-d");

        // Extract year, month, and day from today's date
        $year = date("Y", strtotime($today));
        $month = date("m", strtotime($today));
        $day = date("d", strtotime($today));
        ?>

        <select class="form-select form-select-lg mb-3" aria-label="Day">
            <?php
            // Generate options for day
            for ($d = 1; $d <= 31; $d++) {
                $selected = ($d == $day) ? 'selected' : '';
                echo "<option value='$d' $selected>$d</option>";
            }
            ?>
        </select>

        <select class="form-select form-select-lg mb-3" aria-label="Month">
            <?php
            // Generate options for month
            $months = array(
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            );

            foreach ($months as $m => $monthName) {
                $selected = ($m == $month) ? 'selected' : '';
                echo "<option value='$m' $selected>$monthName</option>";
            }
            ?>
        </select>

        <select class="form-select form-select-lg mb-3" aria-label="Year">
            <?php
            // Generate options for year
            $currentYear = date("Y");
            $startYear = 1900;

            for ($y = $startYear; $y <= $currentYear; $y++) {
                $selected = ($y == $year) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>
    </div>

</body>

</html>