<!DOCTYPE html>
<html>

<head>
    <title>Registration Form</title>
    <style>
        .error-message {
            color: red;
        }
    </style>
    <script>
        function checkPasswordMatch() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;
            // 获取密码和确认密码输入框的值
            var errorElement = document.getElementById("passwordError");

            if (password !== confirmPassword) {
                document.getElementById("confirmPassword").classList.add("error");
                errorElement.style.display = "block";
            } else {
                document.getElementById("confirmPassword").classList.remove("error");
                errorElement.style.display = "none";
            }
            //如果不匹配，将确认密码输入框的样式设置为错误样式，并显示错误消息。如果匹配，将确认密码输入框的错误样式移除，并隐藏错误消息。
        }
    </script>
</head>

<body>
    <h1>Registration Form</h1>
    <form method="POST" action="">
        <div>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>

        <div>
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>
        </div>

        <div>
            <label>Date of Birth:</label>
            <select name="day" required>
                <option value="" disabled selected>Day</option>
                <?php
                for ($i = 1; $i <= 31; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                }
                ?>
            </select>
            <select name="month" required>
                <option value="" disabled selected>Month</option>
                <?php
                $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                foreach ($months as $month) {
                    echo "<option value=\"$month\">$month</option>";
                }
                ?>
            </select>
            <select name="year" required>
                <option value="" disabled selected>Year</option>
                <?php
                $currentYear = date('Y');
                for ($i = 1900; $i <= $currentYear; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="gender">Gender:</label>
            <input type="radio" id="male" name="gender" value="male" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female">Female</label>
        </div>

        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" pattern="[a-zA-Z_\-][a-zA-Z0-9_\-]{5,}" title="Minimum 6 characters, starts with a letter, and allows only _ or - in between." required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9]{6,}" title="Minimum 6 characters, at least 1 capital letter, 1 small letter, and 1 number." required>
        </div>

        <div>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" oninput="checkPasswordMatch()" required>
            <!-- 设置了一个事件处理函数"checkPasswordMatch()"，该函数会在用户输入时被调用，用于实时检查密码的匹配情况。 -->
            <span id="passwordError" class="error-message" style="display: none;">Passwords do not match.</span>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <button type="submit" name="submit">Register</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $gender = $_POST['gender'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $email = $_POST['email'];
        $registrationSuccessful = true;
        if ($registrationSuccessful) {
            echo '<p>Registration successful! Thank you for signing up.</p>';
        }
    }
    ?>

</body>

</html>