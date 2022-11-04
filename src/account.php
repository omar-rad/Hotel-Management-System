<?php
require './class/User.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: error');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Account</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $email = $_SESSION['user']->getEmail();
        $x = [];
        preg_match('/(?<=@)[^.]+(?=\.)/', $email, $x);
        require './require/database.php';
        require './class/Room.php';
        if ($x[0] == 'crimson') {
            if (isset($_GET['add'])) {
                header('Location: edit?add=1');
                exit;
            } elseif (isset($_GET['display'])) {
                header('Location: display');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['del'])) {
                    $number = $_POST['del'];
                    $rows = $mysqli->query('SELECT email, confirmation FROM'
                                    . ' booking WHERE number = ' . $number
                                    . ' AND cout >= \'' . date('Y-m-d')
                                    . '\'')->fetch_all();
                    $mysqli->query('DELETE FROM room WHERE number = '
                            . $number);
                    unset($_SESSION['number']);
                } elseif (isset($_POST['able'])) {
                    $x = explode(' ', $_POST['able']);
                    $number = $x[0];
                    $available = $x[1];
                    $mysqli->query('UPDATE room SET available = '
                            . $available . ' WHERE number = ' . $number);
                    $rows = $mysqli->query('SELECT email, confirmation FROM'
                                    . ' booking WHERE number = ' . $number
                                    . ' AND cout >= \'' . date('Y-m-d')
                                    . '\'')->fetch_all();
                    if (!intval($available)) {
                        $mysqli->query('DELETE FROM booking WHERE number = '
                                . $number . ' AND cout > \'' . date('Y-m-d')
                                . '\'');
                    }
                } else {
                    $_SESSION['number'] = $_POST['edit'];
                    header('Location: edit');
                    exit;
                }
                foreach ($rows as $row) {
                    $stmt = $mysqli->prepare('INSERT INTO notification '
                            . '(email, booking) VALUES (?, ?)');
                    $stmt->bind_param('ss', $row[0], $row[1]);
                    $stmt->execute();
                }
            }
            echo '<form method="get" action="' . htmlspecialchars(str_replace(
                            '.php', '', $_SERVER['PHP_SELF'])) . '">
                <div class="form filter">
                    <button class="submit" name="add" value="1" 
                    style="margin: 0">Add Room</button>
                    <button class="submit" name="display" value="1">
                    Display Records</button>
                </div>
            </form>';
            $result = $mysqli->query('SELECT * FROM room');
            if ($result->num_rows) {
                $rows = $result->fetch_all();
                foreach ($rows as $row) {
                    $room = new Room($row);
                    $number = $room->getNumber();
                    $available = $room->isAvailable();
                    echo '<br>'
                    . '<form method="post" action="'
                    . htmlspecialchars(str_replace(
                                    '.php', '', $_SERVER['PHP_SELF'])) . '">
                <div class="result">
                    <div class="result-info">
                        <div class="title" style="color: #dc2f55; margin: 0">
                        Room ' . $number . '</div>
                        <div class="subtitle" style="margin-top: 30px">
                            <p>Floor: ' . $room->getFloor() . '</p>
                            <p>Type: ' . $room->getType() . '</p>
                            <p>Capacity: ' . $room->getCapacity() . '</p>
                            <p>Price per night: $' . $room->getPrice() . '</p>
                            <p>Directory: ' . $room->getDir() . '</p>
                        </div>
                        <div style="display: flex">
                            <button class="submit" name="edit" value="'
                    . $number
                    . '" style="flex: 1; margin: 20px 7px 0 0">Edit</button>
                        <button class="submit" name="able" value="'
                    . $number . ' ' . ($available ? 0 : 1)
                    . '" style="flex: 1; margin: 20px 3.5px 0 3.5px">'
                    . ($available ? 'Disable' : 'Enable') . '</button>
                            <button class="submit" name="del" value="' . $number
                    . '" style="flex: 1; margin: 20px 0 0 7px;
                        background-color: #dc2f55">Remove</button>
                        </div>
                    </div>
                    <div class="result-img">
                        <img src="' . $room->getDir() . 'img0.png">
                        <div class="result-desc">
                        <div class="subtitle" style="margin: 30px">'
                    . $room->getDescription() . '</div>
                        </div>
                    </div>
                </div>
            </form>';
                }
            } else {
                echo '<div class="form filter" style="margin-top: 30px">
                    <div class="title" style="color: #dc2f55; margin: 0">
                        No room found
                    </div>
                </div>';
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['del'])) {
                    $mysqli->query('DELETE FROM booking WHERE confirmation = \''
                            . $_POST['del'] . '\'');
                    unset($_SESSION['confirmation']);
                } else {
                    $_SESSION['confirmation'] = $_POST['edit'];
                    header('Location: edit');
                    exit;
                }
            }

            $notifications = $mysqli->query('SELECT * FROM notification WHERE'
                    . ' email = \'' . $email . '\'');
            if ($notifications->num_rows) {
                echo '<div class="form filter" style="margin-bottom: 30px">
                    <div class="title" style="margin: 0; color: #dc2f55">
                        Refunded Bookings
                    </div>';
                $rows = $notifications->fetch_all();
                foreach ($rows as $row) {
                    echo '<p class="title">' . $row[2] . '</p>';
                }
                echo '</div>';
                $mysqli->query('DELETE FROM notification WHERE email = \''
                        . $email . '\'');
            }
            echo '<div class="form filter" style="margin-bottom: 30px">
                    <div class="title" style="margin: 0">
                        Your Bookings
                    </div>
                </div>';
            $result = $mysqli->query('SELECT * FROM booking WHERE email = \''
                    . $email . '\' AND cout > \'' . date('Y-m-d') . '\'');
            require './class/Booking.php';
            if ($result->num_rows) {
                $rows = $result->fetch_all();
                foreach ($rows as $row) {
                    $booking = new Booking($row);
                    $confirmation = $booking->getConfirmation();
                    $number = $booking->getNumber();
                    $room = new Room($mysqli->query('SELECT * FROM room WHERE '
                                    . 'number = ' . $number)->fetch_row());
                    echo '<form method="post" action="'
                    . htmlspecialchars(str_replace(
                                    '.php', '', $_SERVER['PHP_SELF'])) . '">
                <div class="result">
                    <div class="result-info">
                        <div class="title" style="color: #dc2f55; margin: 0">'
                    . $confirmation . '</div>
                        <div class="subtitle" style="margin-top: 30px">
                            <p>Room: ' . $number . '</p>
                            <p>Check-in: ' . $booking->getCin() . '</p>
                            <p>Check-out: ' . $booking->getCout() . '</p>
                            <p>Guests: ' . $booking->getGuests() . '</p>
                            <p>Paid: $' . $booking->getPrice() . '</p>
                        </div>
                        <div style="display: flex">
                            <button class="submit" name="edit" value="'
                    . $confirmation
                    . '" style="flex: 1; margin: 20px 7px 0 0">Edit</button>
                            <button class="submit" name="del" value="'
                    . $confirmation
                    . '" style="flex: 1; margin: 20px 0 0 7px;
                        background-color: #dc2f55">Cancel</button>
                        </div>
                    </div>
                    <div class="result-img">
                        <img src="' . $room->getDir() . 'img0.png">
                    </div>
                </div>
            </form>
            <br>';
                }
            } else {
                echo '<div class="form filter" style="margin-top: 30px">
                    <div class="title" style="color: #dc2f55; margin: 0">
                        No booking found
                    </div>
                </div>';
            }
            $resultX = $mysqli->query('SELECT * FROM booking WHERE email = \''
                    . $email . '\' AND cout <= \'' . date('Y-m-d') . '\'');
            if ($resultX->num_rows) {
                echo '<div class="form filter" style="margin: 30px auto">
                    <div class="title" style="margin: 0">
                        Your Previous Bookings
                    </div>
                </div>';
                $rows = $resultX->fetch_all();
                foreach ($rows as $row) {
                    $booking = new Booking($row);
                    $confirmation = $booking->getConfirmation();
                    $number = $booking->getNumber();
                    $room = new Room($mysqli->query('SELECT * FROM room WHERE '
                                    . 'number = ' . $number)->fetch_row());
                    echo '<form method="post" action="'
                    . htmlspecialchars(str_replace(
                                    '.php', '', $_SERVER['PHP_SELF'])) . '">
                <div class="result">
                    <div class="result-info">
                        <div class="title" style="color: #dc2f55">'
                    . $confirmation . '</div>
                        <div class="subtitle" style="margin-top: 30px">
                            <p>Room: ' . $number . '</p>
                            <p>Check-in: ' . $booking->getCin() . '</p>
                            <p>Check-out: ' . $booking->getCout() . '</p>
                            <p>Guests: ' . $booking->getGuests() . '</p>
                            <p>Paid: $' . $booking->getPrice() . '</p>
                        </div>
                    </div>
                    <div class="result-img">
                        <img src="' . $room->getDir() . 'img0.png">
                    </div>
                </div>
            </form>
            <br>';
                }
            }
        }
        ?>
        <footer></footer>
    </body>
</html>
