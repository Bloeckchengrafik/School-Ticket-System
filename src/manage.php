<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;

$page = "dashboard";

sess::authGuard(accountType::Admin);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Manage | Support</title>
</head>
<body class="page">
<?php include_once "modules/html/nav.php"; ?>

<div class="page-wrapper">
    <div class="container card container-lg card-full">
        <div class="card-header">
            <h4 class="m0 text-muted caps sm">Admin</h4>
            <h1 class="card-title mt0">Räume</h1>
        </div>
        <div class="card-body">

        </div>
    </div>
    <div class="container card container-lg card-full">
        <div class="card-header">
            <h4 class="m0 text-muted caps sm">Admin</h4>
            <h1 class="card-title mt0">Benutzerverwaltung</h1>
        </div>
        <div class="card-body">
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>
