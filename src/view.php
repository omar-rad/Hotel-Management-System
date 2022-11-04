<?php
require './class/User.php';
session_start();
if (ctype_digit($number = $_GET['number'])) {
    require './require/database.php';
    $result = $mysqli->query('SELECT * FROM room WHERE number = '
            . $number . ' AND available = 1');
    if ($result->num_rows) {
        require './class/Room.php';
        $room = new Room($result->fetch_row());
    } else {
        header('Location: error');
        exit;
    }
} else {
    header('Location: error');
    exit;
}
$number = $room->getNumber();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo 'Room ' . $number; ?></title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        require './require/header.php';
        $tPrice = date_diff(date_create($_SESSION['in']),
                        date_create($_SESSION['out']))->d * $room->getPrice();
        $dir = $room->getDir();
        ?>
        <div class="form">
            <form method="post" action="payment">
                <input type="text" name="rn" value="<?php echo $number; ?>"
                       style="display: none">
                <input type="text" name="tp" value="<?php echo $tPrice; ?>"
                       style="display: none">
                <div class="view-container">
                    <div class="title" style="color: #dc2f55">
                        Room <?php echo $number; ?></div>
                    <div class="view">
                        <div class="view-imgs">
                            <div class="img-display">
                                <div class="img-showcase">
                                    <img src="<?php echo $dir; ?>img0.png">
                                    <img src="<?php echo $dir; ?>img1.png">
                                    <img src="<?php echo $dir; ?>img2.png">
                                    <img src="<?php echo $dir; ?>img3.png">
                                </div>
                            </div>
                            <div class="img-select">
                                <div class="img-item">
                                    <a data-id="1">
                                        <img src="<?php echo $dir; ?>img0.png">
                                    </a>
                                </div>
                                <div class="img-item">
                                    <a data-id="2">
                                        <img src="<?php echo $dir; ?>img1.png">
                                    </a>
                                </div>
                                <div class="img-item">
                                    <a data-id="3">
                                        <img src="<?php echo $dir; ?>img2.png">
                                    </a>
                                </div>
                                <div class="img-item">
                                    <a data-id="4">
                                        <img src="<?php echo $dir; ?>img3.png">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="subtitle">
                            <?php echo $room->getDescription() ?>
                            <p style="margin-top: 25px">
                                Floor: <?php echo $room->getFloor(); ?></p>
                            <p>Type: <?php echo $room->getType(); ?></p>
                            <p>Capacity: <?php echo $room->getCapacity(); ?></p>
                            <p>Wi-Fi: <?php
                                echo ($room->hasWifi() ?
                                        'Available' : 'Not Available');
                                ?></p>
                            <p>Breakfast: <?php
                                echo ($room->hasBreakfast() ?
                                        'Available' : 'Not Available');
                                ?></p>
                            <p>Smoking: <?php
                                echo ($room->hasSmoking() ?
                                        'Available' : 'Not Available');
                                ?></p>
                            <p>Price per night: 
                                $<?php echo $room->getPrice(); ?></p>
                        </div>
                    </div>
                    <button class="submit">Book for 
                        $<?php echo $tPrice; ?></button>
                </div>
            </form>
        </div>
        <footer></footer>
        <script>
            const imgs = document.querySelectorAll(".img-select a");
            const imgBtns = [...imgs];
            let imgId = 1;
            imgBtns.forEach((imgItem) => {
                imgItem.addEventListener("click", (event) => {
                    event.preventDefault();
                    imgId = imgItem.dataset.id;
                    slideImage();
                });
            });
            function slideImage() {
                const displayWidth = document
                        .querySelector(".img-showcase img:first-child")
                        .clientWidth;
                document.querySelector(".img-showcase").style.transform =
                        `translateX(${-(imgId - 1) * displayWidth}px)`;
            }
            window.addEventListener("resize", slideImage);
        </script>
    </body>
</html>
