<?php
require './class/User.php';
session_start();
if (!isset($_SESSION['user']) or!isset($_GET['add']) and!isset($_POST['add'])
        and!isset($_SESSION['number']) and!isset($_SESSION['confirmation'])) {
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
        require './require/database.php';
        if (isset($_GET['add']) or isset($_POST['add'])
                or isset($_SESSION['number'])) {
            $nErr = $cErr = '';
            if (isset($_GET['add']) or isset($_POST['add'])) {
                $x = 1;
            } else {
                $x = 0;
                $cNumber = $_SESSION['number'];
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $number = $_POST['number'];
                $cap = $_POST['cap'];
                $wifi = isset($_POST['wifi']) ? 1 : 0;
                $brk = isset($_POST['brk']) ? 1 : 0;
                $smk = isset($_POST['smk']) ? 1 : 0;
                if ($mysqli->query('SELECT * FROM room WHERE number = '
                                . $number)->num_rows
                        and ($x or $number != $cNumber)) {
                    $nErr = 'There\'s already a room with number ' . $number;
                }
                if ($mysqli->query('SELECT * FROM room NATURAL JOIN booking '
                                . 'WHERE guests > ' . $cap)->num_rows) {
                    $cErr = 'Can\'t reduce the room capacity due to bookings';
                }
                if ($nErr == $cErr) {
                    if ($x) {
                        $stmt = $mysqli->prepare('INSERT INTO room '
                                . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,'
                                . ' DEFAULT)');
                    } else {
                        $stmt = $mysqli->prepare('UPDATE room SET number = ?, '
                                . 'floor = ?, type = ?, capacity = ?, '
                                . 'price = ?, wifi = ?, breakfast = ?, '
                                . 'smoking = ?, description = ?, dir = ? WHERE '
                                . 'number = ' . $cNumber);
                    }
                    $stmt->bind_param('iisidiiisi', $number,
                            $_POST['floor'], $_POST['type'], $_POST['cap'],
                            $_POST['price'], $wifi, $brk, $smk,
                            $_POST['desc'], $_POST['dir']);
                    $stmt->execute();
                    header('Location: account');
                    exit;
                }
            }
            echo '<div class="form">
            <div class="title">' . ($x ? 'Add' : 'Room ' . $cNumber) . '</div>'
            . ($x ? '<div class="subtitle">Insert your room</div>' : '')
            . '<form method="post"
                  action="' . htmlspecialchars(str_replace(
                            '.php', '', $_SERVER['PHP_SELF']))
            . '">
                <div class="input-container">
                    <input id="number" name="number" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="1" required>
                    <div class="cut" style="width: 63px"></div>
                    <label for="number" class="placeholder">Number</label>
                </div>
                <div class="input-container">
                    <input id="floor" name="floor" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="0" required>
                    <div class="cut" style="width: 50px"></div>
                    <label for="floor" class="placeholder">Floor</label>
                </div>
                <div class="input-container">
                    <input id="cap" name="cap" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="1" required>
                    <div class="cut" style="width: 67px"></div>
                    <label for="cap" class="placeholder">Capacity</label>
                </div>
                <div class="input-container">
                    <input id="price" name="price" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="0" step="any" required>
                    <div class="cut" style="width: 100px"></div>
                    <label for="price" class="placeholder">Price per night
                    </label>
                </div>
                <div class="input-container">
                    <input id="dir" name="dir" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="0" required>
                    <div class="cut" style="width: 70px"></div>
                    <label for="dir" class="placeholder">Directory</label>
                </div>
                <div class="input-container" style="height: 350px">
                    <textarea id="desc" name="desc" class="input"
                              style="padding: 20px" placeholder=" "
                              maxlength="65535" required></textarea>
                    <div class="cut" style="width: 80px"></div>
                    <label for="desc" class="placeholder">Description</label>
                </div>
                <div style="margin-top: 30px">
                    <div class="checkbox">
                        <input type="checkbox" id="wifi" class="checkbox-input"
                               name="wifi" value="1">
                        <label for="wifi" class="checkbox-label">Wi-Fi</label>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" id="brk" class="checkbox-input"
                               name="brk" value="1">
                        <label for="brk" class="checkbox-label">Breakfast
                        </label>
                    </div>
                    <div class="checkbox" style="margin-right: 47px">
                        <input type="checkbox" id="smk" class="checkbox-input"
                               name="smk" value="1">
                        <label for="smk" class="checkbox-label">Smoking</label>
                    </div>
                    <select name="type">
                        <option value="normal">Normal</option>
                        <option value="studio">Studio</option>
                        <option value="suite">Suite</option>
                    </select>' . ($x ? '<input type="text" name="add" value="1"
                       style="display: none">' : '')
            . '</div>
                <button class="submit">' . ($x ? 'Add' : 'Apply') . '</button>';
            if ($nErr != $cErr) {
                echo '<div class="err">';
                if (!empty($nErr)) {
                    echo '<p>' . $nErr . '</p>';
                }
                if (!empty($cErr)) {
                    echo '<p>' . $cErr . '</p>';
                }
                echo '</div>';
            }
            echo '</form></div>';
        } else {
            $confirmation = $_SESSION['confirmation'];
            $nErr = $dErr = $cErr = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $row = $mysqli->query(
                                'SELECT * FROM booking WHERE confirmation = '
                                . '\'' . $confirmation . '\'')->fetch_row();
                require './class/Booking.php';
                $booking = new Booking($row);
                $nights = date_diff(date_create($booking->getCin()),
                                date_create($booking->getCout()))->d;
                require './class/Room.php';
                $number = $booking->getNumber();
                $room = new Room($mysqli->query(
                                'SELECT * FROM room WHERE number = '
                                . $number)->fetch_row());
                $in = $_POST['in'];
                $out = $_POST['out'];
                if (date_diff(date_create($_POST['in']),
                                date_create($_POST['out']))->d > $nights) {
                    $nErr = 'Your booking is for ' . $nights . ' nights';
                } elseif ($mysqli->query('SELECT * FROM booking WHERE number = '
                                . $number . ' AND cin BETWEEN \'' . $in
                                . '\' AND \'' . $out . '\' OR cout BETWEEN \''
                                . $in . '\' AND \'' . $out . '\'')->num_rows and
                        $in != $booking->getCin() or
                        $out != $booking->getCout()) {
                    $dErr = 'The room isn\'t available between ' . $in . ' and '
                            . $out;
                }
                $cap = $room->getCapacity();
                $guests = $_POST['cap'];
                if ($guests > $cap) {
                    $cErr = 'The room capacity is ' . $cap;
                }
                if ($nErr == $dErr and $dErr == $cErr) {
                    $stmt = $mysqli->prepare('UPDATE booking SET cin = ?, '
                            . 'cout = ?, guests = ? WHERE confirmation = ?');
                    $stmt->bind_param('ssis', $in, $out, $guests,
                            $confirmation);
                    $stmt->execute();
                    unset($_SESSION['confirmation']);
                    header('Location: account');
                    exit;
                }
            }
            $minIn = date_format(date_add(date_create(),
                            date_interval_create_from_date_string('1 day')),
                    'Y-m-d');
            $minOut = date_format(date_add(date_create(),
                            date_interval_create_from_date_string('2 days')),
                    'Y-m-d');
            echo '<div class="form">
            <div class="title">' . $confirmation . '</div>
            <form method="post" action="' . htmlspecialchars(str_replace(
                            '.php', '', $_SERVER['PHP_SELF'])) . '">
                <div class="input-container">
                    <input id="in" name="in" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'date\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="' . $minIn . '" required>
                    <div class="cut" style="width: 70px"></div>
                    <label for="in" class="placeholder">Check-in</label>
                </div>
                <div class="input-container">
                    <input id="out" name="out" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'date\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="' . $minOut . '" required>
                    <div class="cut"></div>
                    <label for="out" class="placeholder">Check-out</label>
                </div>
                <div class="input-container">
                    <input id="cap" name="cap" class="input" type="text"
                           placeholder=" " onfocus="(this.type = \'number\')"
                           onblur="if (!this.value)
                                       this.type = \'text\'"
                           min="1" required>
                    <div class="cut" style="width: 60px"></div>
                    <label for="cap" class="placeholder">People</label>
                </div>
                <button class="submit">Apply</button>
            </form>';
            if ($nErr != $dErr or $dErr != $cErr) {
                echo '<div class="err">';
                if (!empty($nErr)) {
                    echo '<p>' . $nErr . '</p>';
                }
                if (!empty($dErr)) {
                    echo '<p>' . $dErr . '</p>';
                }
                if (!empty($cErr)) {
                    echo '<p>' . $cErr . '</p>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
        <footer></footer>
    </body>
</html>
