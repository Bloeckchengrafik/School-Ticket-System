<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\db\Models\Ticket;
use function Database\connection;

$page = "ticket";

sess::authGuard(accountType::Teacher, accountType::Admin);
$user = sess::parseUser();

$conn = connection();

$ticketId = $_GET["id"];

if (!isset($ticketId)) {
    header("Location: /");
    exit();
}

$ticket = Ticket::byId($ticketId);
if (!$ticket->canView($user->userID)) {
    header("Location: /");
    exit();
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Ticket | Support</title>
</head>
<body class="page">
<?php include_once "modules/html/nav.php"; ?>


<div class="page-wrapper">
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-lg rounded material-icons text-white <?= $ticket->status == "open" ? "bg-lime" : "bg-red" ?>"><span class="material-icons"><?= $ticket->status == "open" ? "import_contacts" : "close" ?></span></span>
                </div>
                <div class="col">
                    <h1 class="fw-bold"><?= $ticket->title ?></h1>
                    <div class="list-inline list-inline-dots text-muted">
                        <div class="list-inline-item">
                            Ticket #<?= $ticket->ticket_id ?>
                        </div>
                        <div class="list-inline-item">
                            <span class="material-icons">person</span>
                            Von <?= $ticket->user() ?>
                        </div>
                        <div class="list-inline-item">
                            <span class="material-icons">schedule</span>
                            <?= $ticket->createdAt() ?>
                        </div>
                    </div>
                    <div class="list-inline list-inline-dots text-muted">
                        <div class="list-inline-item">
                            Raum <?= $ticket->room() ?>
                        </div>
                        <div class="list-inline-item">
                            <span class="material-icons">person</span>
                            Ger&auml;t: <?= $ticket->device()->device_name ?>
                        </div>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a href="?id=<?= $ticket->ticket_id ?><?= in_array($user->userID, $ticket->members()) ? "" : "&join" ?>" class="btn btn-primary">
                            <span class="material-icons">person_add</span> &nbsp;
                            <?= in_array($user->userID, $ticket->members()) ? "Beigetreten" : "Beitreten" ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>

