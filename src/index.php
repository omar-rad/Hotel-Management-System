<?php
require './class/User.php';
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Crimson Creek Hotel</title>
        <link rel="stylesheet" href="styles.css"/>
    </head>
    <body>
        <?php
        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header('Location: .');
            exit;
        }
        require './require/header.php';
        $minIn = date_format(date_add(date_create(),
                        date_interval_create_from_date_string('1 day')),
                'Y-m-d');
        $minOut = date_format(date_add(date_create(),
                        date_interval_create_from_date_string('2 days')),
                'Y-m-d');
        ?>
        <div class="form">
            <div class="title">Search</div>
            <div class="subtitle">Find your room</div>
            <form method="get" action="search">
                <div class="input-container">
                    <input id="in" name="in" class="input" type="text"
                           placeholder=" " onfocus="(this.type = 'date')"
                           onblur="if (!this.value)
                                       this.type = 'text'"
                           min="<?php echo $minIn ?>" required>
                    <div class="cut" style="width: 70px"></div>
                    <label for="in" class="placeholder">Check-in</label>
                </div>
                <div class="input-container">
                    <input id="out" name="out" class="input" type="text"
                           placeholder=" " onfocus="(this.type = 'date')"
                           onblur="if (!this.value)
                                       this.type = 'text'"
                           min="<?php echo $minOut ?>" required>
                    <div class="cut"></div>
                    <label for="out" class="placeholder">Check-out</label>
                </div>
                <div class="input-container">
                    <input id="cap" name="cap" class="input" type="text"
                           placeholder=" " onfocus="(this.type = 'number')"
                           onblur="if (!this.value)
                                       this.type = 'text'"
                           min="1" required>
                    <div class="cut" style="width: 60px"></div>
                    <label for="cap" class="placeholder">People</label>
                </div>
                <button class="submit">Search</button>
                <?php
                if (isset($_SESSION['err'])) {
                    echo '<div class="err">'
                    . '<p>' . $_SESSION['err'] . '</p>'
                    . '</div>';
                    unset($_SESSION['err']);
                }
                ?>
            </form>
        </div>
        <footer></footer>
    </body>
</html>
