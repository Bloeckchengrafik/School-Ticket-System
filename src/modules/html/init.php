<?php
include_once "vendor/autoload.php";
include_once "modules/mysql.php";
include_once "modules/auth/Session.php";
include_once "modules/auth/AccountType.php";
include_once "modules/auth/User.php";
include_once "modules/db/Users.php";
include_once "modules/mail/View.php";
include_once "modules/mail/Mailer.php";

use modules\auth\Session as sess;

sess::start();