<?php
require './class/User.php';
session_start();
if (!isset($_GET['in']) or!isset($_GET['out']) or!isset($_GET['cap'])
        or $_GET['in'] <= date('Y-m-d')) {
    header('Location: error');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Search</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $in = $_GET['in'];
        $out = $_GET['out'];
        $cap = $_GET['cap'];
        ?>
        <div class="form filter">
            <div class="subtitle" style="margin-bottom: 35px">
                Choose your filtering and sorting options</div>
            <form method="get"
                  action="<?php
                  echo htmlspecialchars(str_replace(
                                  '.php', '', $_SERVER['PHP_SELF']));
                  ?>">
                <input type="text" name="in" value="<?php echo $in; ?>"
                       style="display: none">
                <input type="text" name="out" value="<?php echo $out; ?>"
                       style="display: none">
                <input type="text" name="cap" value="<?php echo $cap; ?>"
                       style="display: none">
                <div class="checkbox">
                    <input type="checkbox" id="wifi" class="checkbox-input"
                           name="wifi" value="1">
                    <label for="wifi" class="checkbox-label">Wi-Fi</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="brk" class="checkbox-input"
                           name="brk" value="1">
                    <label for="brk" class="checkbox-label">Breakfast</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="smk" class="checkbox-input"
                           name="smk" value="1">
                    <label for="smk" class="checkbox-label">Smoking</label>
                </div>
                <select name="sort">
                    <option value="">Sort by</option>
                    <option value="lp">Lowest price</option>
                    <option value="hp">Highest price</option>
                    <option value="lc">Lowest Capacity</option>
                    <option value="hc">Highest Capacity</option>
                </select>
                <button class="submit">Apply</button>
            </form>
        </div>
        <?php
        if ($in >= $out) {
            $_SESSION['err'] = 'Invalid date range';
            header('Location: .');
            exit;
        }
        $wifi = isset($_GET['wifi']) ? ' AND wifi = 1' : '';
        $breakfast = isset($_GET['brk']) ? ' AND breakfast = 1' : '';
        $smoking = isset($_GET['smk']) ? ' AND smoking = 1' : '';
        $sort = '';
        if (isset($_GET['sort']) and!empty($_GET['sort'])) {
            $sort = ' ORDER BY ';
            switch ($_GET['sort']) {
                case 'lp':
                    $sort .= 'price';
                    break;
                case 'hp':
                    $sort .= 'price DESC';
                    break;
                case 'lc':
                    $sort .= 'capacity';
                    break;
                case 'hc':
                    $sort .= 'capacity DESC';
            }
        }
        require './require/database.php';
        $_SESSION['in'] = $in;
        $_SESSION['out'] = $out;
        $_SESSION['cap'] = $cap;
        $result = $mysqli->query('SELECT * FROM room WHERE number NOT IN '
                . '(SELECT number FROM booking WHERE cin BETWEEN \'' . $in
                . '\' AND \'' . $out . '\' OR cout BETWEEN \'' . $in
                . '\' AND \'' . $out . '\') AND capacity >= ' . $cap
                . ' AND available = 1' . $wifi . $breakfast . $smoking . $sort);
        if ($result->num_rows) {
            $rows = $result->fetch_all();
            require './class/Room.php';
            foreach ($rows as $row) {
                $room = new Room($row);
                $number = $room->getNumber();
                echo '<br>
                    <form method="get" action="view">
            <div class="result">
                <div class="result-info">
                    <div class="title" style="color: #dc2f55">Room '
                . $number . '</div>
                    <div class="subtitle" style="margin-top: 30px">
                        <p>Wi-Fi: '
                . ($room->hasWifi() ? 'Available' : 'Not Available') . '</p>
                        <p>Breakfast: '
                . ($room->hasBreakfast() ? 'Available' : 'Not Available')
                . '</p>
                        <p>Price per night: $' . $room->getPrice() . '</p>
                    </div>
                    <button class="submit" name="number" value="' . $number
                . '" style="margin-top: 30px">View
                    </button>
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
        ?>
        <footer></footer>
    </body>
</html>
