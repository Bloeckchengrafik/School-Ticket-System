<?php
include_once "modules/auth/Session.php";
include_once "modules/auth/AccountType.php";
include_once "modules/auth/User.php";
include_once "vendor/autoload.php";

use modules\auth\Session as sess;

sess::start();