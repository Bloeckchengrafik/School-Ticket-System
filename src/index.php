<?php
include_once "modules/html/init.php";
use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;

sess::authGuard(accountType::Supporter, accountType::Admin, accountType::Teacher);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Support</title>
</head>
<body>

<div class="page">
    Dashboard
    <a href="logout.php">Logout</a>
</div>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>
