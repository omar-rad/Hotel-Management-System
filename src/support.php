<?php
require './class/User.php';
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Support</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $dEmail = '';
        if (isset($_SESSION['user'])) {
            $dEmail = $_SESSION['user']->getEmail();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require './require/database.php';
            $row = $mysqli->query('SHOW TABLE STATUS LIKE '
                            . '\'support\'')->fetch_assoc();
//            mail('support@crimson.com', 'CASE #' . $row['Auto_increment'],
//                    wordwrap($_POST['msg'], 70, '\r\n'));
            $stmt = $mysqli->prepare('INSERT INTO support '
                    . '(email, message, open) VALUES (?, ?, ?)');
            $open = date('Y-m-d');
            $stmt->bind_param('sss', $_POST['email'], $_POST['msg'], $open);
            $stmt->execute();
        }
        ?>
        <div class="form">
            <div class="title">Support</div>
            <div class="subtitle">Contact us</div>
            <form method="post"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
                <div class="input-container">
                    <input id="email" name="email" class="input" type="email"
                           value="<?php echo $dEmail; ?>" placeholder=" "
                           required>
                    <div class="cut" style="width: 50px"></div>
                    <label for="email" class="placeholder">Email</label>
                </div>
                <div class="input-container" style="height: 350px">
                    <textarea id="msg" name="msg" class="input"
                              style="padding: 20px" placeholder=" "
                              maxlength="65535" required></textarea>
                    <div class="cut" style="width: 70px"></div>
                    <label for="msg" class="placeholder">Message</label>
                </div>
                <button class="submit">Send</button>
            </form>
        </div>
        <footer></footer>
    </body>
</html>
