<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
sess::destroy();
sess::start();

header("Location: login.php");