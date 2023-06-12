<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;

$page = "dashboard";

sess::authGuard(accountType::Supporter, accountType::Admin, accountType::Teacher);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Support</title>
</head>
<body class="page">
<?php include_once "modules/html/nav.php"; ?>

<div class="page-wrapper">
    <div class="container card container-lg card-full">
        <div class="card-header">
            <h1 class="mt0">Aktuelle Events</h1>
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>
