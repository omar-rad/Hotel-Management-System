<?php
require './class/User.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: error');
    exit;
}
if (isset($_SESSION['user'])) {
    $email = $_SESSION['user']->getEmail();
    $x = [];
    preg_match('/(?<=@)[^.]+(?=\.)/', $email, $x);
    if ($x[0] == 'crimson') {
        header('Location: error');
        exit;
    }
}
if (!isset($_SESSION['user'])) {
    $_SESSION['err'] = 'Login is required for bookings';
    header('Location: login');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $number = $_POST['rn'];
        $tPrice = $_POST['tp'];
        if (isset($_POST['ccn'])) {
            $exp = $_POST['exp'];
            $expy = intval(substr($exp, 3));
            $year = intval(date('y'));
            if ($expy < $year or $expy == $year and
                    intval(substr($exp, 0, 2)) <= intval(date('m'))) {
                $err = 'Your credit card is expired';
            } else {
                $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $num = '0123456789';
                $code = '';
                require './require/database.php';
                while (true) {
                    for ($i = 0; $i < 3; $i++) {
                        $code .= $alpha[rand(0, 25)];
                    }
                    for ($i = 0; $i < 3; $i++) {
                        $code .= $num[rand(0, 9)];
                    }
                    $code = str_shuffle($code);
                    $result = $mysqli->query('SELECT confirmation FROM booking '
                            . 'WHERE confirmation = \'' . $code . '\'');
                    if (!$result->num_rows) {
                        $stmt = $mysqli->prepare('INSERT INTO booking '
                                . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                        $email = $_SESSION['user']->getEmail();
                        $stmt->bind_param('ssissids', $code, $email, $number,
                                $_SESSION['in'], $_SESSION['out'],
                                $_SESSION['cap'], $tPrice, $_POST['ccn']);
                        $stmt->execute();
                        header('Location: account');
                        exit;
                    }
                }
            }
        }
        ?>
        <div class="form">
            <div class="title">Payment</div>
            <div class="subtitle" style="border: 3px solid #dc2f55;
                 border-radius: 12px; text-align: center; margin: 20px 250px">
                <p>For: Room <?php echo $number; ?></p>
                <p>Check-in: <?php echo $_SESSION['in']; ?></p>
                <p>Check-out: <?php echo $_SESSION['out']; ?></p>
                <p>Total price: $<?php echo $tPrice; ?></p>
            </div>
            <form method="post"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
                <input type="text" name="rn" value="<?php echo $number; ?>"
                       style="display: none">
                <input type="text" name="tp" value="<?php echo $tPrice; ?>"
                       style="display: none">
                <div class="input-container">
                    <input type="text" id="ccn" name="ccn" class="input"
                           placeholder=" " pattern="^\d{16}$" required>
                    <div class="cut" style="width: 125px"></div>
                    <label for="ccn" class="placeholder">Credit card number
                    </label>
                </div>
                <div class="input-container">
                    <input type="text" id="exp" name="exp" class="input"
                           placeholder=" "
                           pattern="^(0[1-9]|1[0-2])\/?([0-9]{2})$" required>
                    <div class="cut" style="width: 100px"></div>
                    <label for="exp" class="placeholder">Expiration date
                    </label>
                </div>
                <div class="input-container">
                    <input type="text" id="cvc" class="input" placeholder=" "
                           pattern="^\d{3}$" required>
                    <div class="cut" style="width: 47px"></div>
                    <label for="cvc" class="placeholder">CVC</label>
                </div>
                <button class="submit">Pay</button>
                <?php
                if (isset($err)) {
                    echo '<div class="err">'
                    . '<p>' . $err . '</p>'
                    . '</div>';
                }
                ?>
            </form>
        </div>
        <footer></footer>
    </body>
</html>
