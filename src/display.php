<?php
require './class/User.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: error');
    exit;
}
$email = $_SESSION['user']->getEmail();
$x = [];
preg_match('/(?<=@)[^.]+(?=\.)/', $email, $x);
if ($x[0] != 'crimson') {
    header('Location: error');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Records</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        ?>
        <div class="form filter">
            <div class="subtitle" style="margin-bottom: 35px">
                Choose your records type</div>
            <form method="get"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
                <select name="type" class="type">
                    <option value="">Bookings</option>
                    <option value="g">Guests</option>
                </select>
                <button class="submit">Apply</button>
            </form>
        </div>
        <?php
        require './require/database.php';
        $y = 1;
        if (isset($_GET['type']) and $_GET['type'] == 'g') {
            $result = $mysqli->query('SELECT * FROM user WHERE email NOT LIKE '
                    . '\'%crimson%\'');
            if ($result->num_rows) {
                $y = 0;
                echo '<div class="form filter" style="margin-top: 30px">
                <table>
                    <tr>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>Birth date</th>
                    </tr>';
                $rows = $result->fetch_all();
                foreach ($rows as $row) {
                    $guest = new User($row);
                    echo '<tr>
                        <td>' . $guest->getFName() . '</td>
                        <td>' . $guest->getLName() . '</td>
                        <td>' . $guest->getEmail() . '</td>
                        <td>' . $guest->getPhone() . '</td>
                        <td>' . $guest->getBirth() . '</td>
                    </tr>';
                }
                echo '</table></div>';
            }
        } else {
            $result = $mysqli->query('SELECT * FROM booking');
            if ($result->num_rows) {
                $y = 0;
                echo '<div class="form filter" style="width: 1000px;
                    margin-top: 30px">
                <table>
                    <tr>
                        <th>Confirmation</th>
                        <th>Email</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Paid ($)</th>
                        <th>Credit card</th>
                    </tr>';
                $rows = $result->fetch_all();
                require './class/Booking.php';
                foreach ($rows as $row) {
                    $booking = new Booking($row);
                    echo '<tr>
                        <td>' . $booking->getConfirmation() . '</td>
                        <td>' . $booking->getEmail() . '</td>
                        <td>' . $booking->getNumber() . '</td>
                        <td>' . $booking->getCin() . '</td>
                        <td>' . $booking->getCout() . '</td>
                        <td>' . $booking->getGuests() . '</td>
                        <td>' . $booking->getPrice() . '</td>
                        <td>' . $booking->getCcn() . '</td>
                    </tr>';
                }
                echo '</table></div>';
            }
        }
        if ($y) {
            echo '<div class="form filter" style="margin-top: 30px">
                    <div class="title" style="color: #dc2f55; margin: 0">
                        No record found
                    </div>
                </div>';
        }
        ?>
        <footer></footer>
    </body>
</html>
