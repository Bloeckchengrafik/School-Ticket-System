<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body {
            font-family: sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto 0;
        }

        .logo {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo img {
            height: 50px;
        }

        .logo span {
            font-size: 20px;
            font-weight: bold;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        p {
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0069d9;
        }

        .btn-container {
            text-align: center;
        }

    </style>
</head>
<body>
<!-- email template for magic link authentication on registration -->
<?php

use modules\auth\User;

/* @var $user User */
/* @var $magicLink string */
?>
<div class="container">
    <div class="logo"><img src="<?php
        // get current host
        $root = $_SERVER['HTTP_HOST'];
        // get current protocol
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
        echo $protocol . '://' . $root . '/assets/img/favicon_dark.png';
        ?>" alt="logo"><br/>
        <span>CONTINUUM SUPPORT</span>
    </div>
    <h1>Hallo <?= $user->firstName ?>,</h1>
    <p>
        Dir wurde ein Benuzterkonto auf Continuum Support erstellt. Um dich einzuloggen, klicke auf den folgenden Link:
    </p>
    <p class="btn-container">
        <a href="<?= $magicLink ?>" target="_blank" class="btn">Login</a>
    </p>

    <p>
        Nach dem ersten Login kannst du dein Passwort &auml;ndern.
    </p>

    <p>
        Continuum Support
    </p>
</div>

</body>
</html>