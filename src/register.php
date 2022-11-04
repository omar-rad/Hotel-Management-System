<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: .');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Registration</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $nameErr = $passwordErr = '';
        $max = date_format(date_sub(date_create(),
                        date_interval_create_from_date_string('13 years')),
                'Y-m-d');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fName = $_POST['fname'];
            $lName = $_POST['lname'];
            if (!(preg_match('/^[a-zA-Z]+$/', $fName) &&
                    preg_match('/^[a-zA-Z]+$/', $lName))) {
                $nameErr = 'Only letters are allowed for names';
            } else {
                $fName = ucfirst(strtolower($fName));
                $lName = ucfirst(strtolower($lName));
            }
            $email = strtolower($_POST['email']);
            $password = $_POST['password'];
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])'
                            . '(?=.*\d)[a-zA-Z\d\w\W]{8,}$/', $password)) {
                $passwordErr = 'Passwords must have a minimum of '
                        . 'eight characters, '
                        . 'one uppercase letter, '
                        . 'one lowercase letter and one number';
            } else {
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
            $phone = $_POST['phone'];
            if (empty($phone)) {
                $phone = null;
            }
            $birth = $_POST['birth'];
            if (empty($birth)) {
                $birth = null;
            }
            if ($nameErr == $passwordErr) {
                require './require/database.php';
                if ($mysqli->query('SELECT email FROM user '
                                . 'WHERE email = \''
                                . $email . '\'')->num_rows) {
                    $nameErr = 'Email is already registered';
                } else {
                    $stmt = $mysqli->prepare('INSERT INTO user '
                            . 'VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('ssssss', $email, $fName, $lName,
                            $password, $phone, $birth);
                    $stmt->execute();
                    $row = [$email, $fName, $lName];
                    require './class/User.php';
                    $user = new User($row);
                    $_SESSION['user'] = $user;
                    header('Location: .');
                    exit;
                }
            }
        }
        ?>
        <div class="form">
            <div class="title">Registration</div>
            <div class="subtitle">Create your account</div>
            <form method="post"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
                <div class="input-container">
                    <input type="text" id="fname" name="fname" class="input"
                           placeholder=" " maxlength="35" required>
                    <div class="cut"></div>
                    <label for="fname" class="placeholder">First name</label>
                </div>
                <div class="input-container">
                    <input type="text" id="lname" class="input" name="lname"
                           placeholder=" " maxlength="35" required>
                    <div class="cut"></div>
                    <label for="lname" class="placeholder">Last name</label>
                </div>
                <div class="input-container">
                    <input id="email" name="email" class="input" type="email"
                           placeholder=" " required>
                    <div class="cut" style="width: 50px"></div>
                    <label for="email" class="placeholder">Email</label>
                </div>
                <div class="input-container">
                    <input id="password" name="password" class="input"
                           type="password" placeholder=" " required>
                    <div class="cut"></div>
                    <label for="password" class="placeholder">Password</label>
                </div>
                <div class="input-container">
                    <input id="phone" name="phone" class="input" type="tel"
                           placeholder=" " pattern="[0-9]{8,15}">
                    <div class="cut" style="width: 100px"></div>
                    <label for="phone" class="placeholder">Phone number</label>
                </div>
                <div class="input-container">
                    <input id="birth" name="birth" class="input" type="text"
                           placeholder=" " onfocus="(this.type = 'date')"
                           onblur="if (!this.value)
                                       this.type = 'text'"
                           max="<?php echo $max ?>">
                    <div class="cut"></div>
                    <label for="birth" class="placeholder">Birth date</label>
                </div>
                <button class="submit">Register</button>
                <?php
                if ($nameErr != $passwordErr) {
                    echo '<div class="err">';
                    if (!empty($nameErr)) {
                        echo '<p>' . $nameErr . '</p>';
                    }
                    if (!empty($passwordErr)) {
                        echo '<p>' . $passwordErr . '</p>';
                    }
                    echo '</div>';
                }
                ?>
            </form>
        </div>
        <footer></footer>
    </body>
</html>
