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
        <title>Login</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = strtolower($_POST['email']);
            require './require/database.php';
            $result = $mysqli->query('SELECT * FROM user WHERE email = \''
                    . $email . '\'');
            $password = $_POST['password'];
            if ($result->num_rows) {
                $row = $result->fetch_row();
                if (password_verify($password, $row[3])) {
                    require './class/User.php';
                    $user = new User($row);
                    $_SESSION['user'] = $user;
                    header('Location: .');
                    exit;
                } else {
                    $err = 'Invalid email or password';
                }
            } else {
                $err = 'Invalid email or password';
            }
        }
        ?>
        <div class="form">
            <div class="title">Login</div>
            <div class="subtitle">Sign in with your account</div>
            <form method="post"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
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
                <button class="submit">Log in</button>
                <?php
                if (isset($err)) {
                    echo '<div class="err">'
                    . '<p>' . $err . '</p>'
                    . '</div>';
                }
                if (isset($_SESSION['err'])) {
                    echo '<div class="err">'
                    . '<p>' . $_SESSION['err'] . '</p>'
                    . '</div>';
                    unset($_SESSION['err']);
                }
                ?>
            </form>
        </div>
    </body>
</html>
