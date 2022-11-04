<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            .header {
                overflow: auto;
                padding: 20px 20px;
            }

            .header a {
                float: left;
                color: #303245;
                text-align: center;
                padding: 10px;
                text-decoration: none;
                font-family: sans-serif;
                font-size: 20px;
                font-weight: bold;
                line-height:30px;
                border-radius: 2px;
            }

            img.logo {
                width: 100px;
            }

            .header a:hover {
                background-color: wheat;
                color: #505473;
            }

            .header-right {
                float: right;
                position: relative;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <a href=".">
                <img src="img/logo.png" class="logo">
            </a>
            <br>
            <div class="header-right">
                <a href=".">Home</a>
                <?php
                if (isset($_SESSION['user'])) {
                    $user = $_SESSION['user'];
                    $x = $user->getFName() . ' ' . $user->getLName();
                    $xHref = 'account';
                    $y = 'Log out';
                    $yHref = '.?logout';
                } else {
                    $x = 'Login';
                    $xHref = 'login';
                    $y = 'Register';
                    $yHref = 'register';
                }
                ?>
                <a href="<?php echo $xHref; ?>"><?php echo $x; ?></a>
                <a href="<?php echo $yHref; ?>"><?php echo $y; ?></a>
                <a href="support">Support</a>
            </div>
        </div>
    </body>
</html>
