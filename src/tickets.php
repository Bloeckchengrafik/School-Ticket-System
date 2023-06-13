<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\db\Models\Ticket;
use function Database\connection;

$page = "tickets";

sess::authGuard();
$user = sess::parseUser();

$tickets = $user->accountClass() == accountType::Teacher ? Ticket::allFrom($user->userID) : Ticket::all();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Ticket erstellen | Support</title>
</head>
<body class="page">
<?php include_once "modules/html/nav.php"; ?>


<div class="page-wrapper d-flex">
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="avatar avatar-lg rounded material-icons text-white bg-azure"><span class="material-icons">apps</span></span>
                </div>
                <div class="col">
                    <h1 class="fw-bold"><?php

                        switch ($user->accountClass()) {
                            case AccountType::Supporter:
                            case AccountType::Admin:
                                echo "Alle";
                                break;
                            case AccountType::Teacher:
                                echo "Deine";
                                break;
                        }

                        ?> Tickets</h1>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a class="btn btn-primary" href="/new_ticket.php">
                            <span class="material-icons">add</span> &nbsp; Neues Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-lg flex-fill">
        <?php foreach ($tickets as $ticket) { ?>
            <a class="card text-reset mt-3" href="/ticket.php?id=<?= $ticket->ticket_id ?>">
                <div class="card-body d-flex gap-2">
                    <span class="avatar avatar-sm rounded material-icons text-white <?= $ticket->status == "open" ? "bg-lime" : "bg-red" ?>"><span class="material-icons"><?= $ticket->status == "open" ? "import_contacts" : "close" ?></span></span>
                    <div class="list-inline list-inline-dots text-muted">
                        <div class="list-inline-item">
                            <span class="h2 text-black"><?= $ticket->title ?></span>
                        </div>
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
                        <?php
                            if ($ticket->userIsMember($user->userID)) {
                                echo "<div class='list-inline-item'><span class='material-icons'>group</span> Du bist Mitglied</div>";
                            }
                        ?>
                    </div>
                </div>
                <?php
                if ($ticket->userHasNewMessages($user->userID)) {
                    echo "<div class='ribbon ribbon-bookmark'><span class='material-icons'>mark_email_unread</span> &nbsp;Neue Nachrichten</div>";
                }
                ?>
            </a>
        <?php }
            if (count($tickets) == 0) {
                echo "<div class='empty text-center'>Du hast noch keine Tickets. <br /> <div class='mt-2'><a href='/new_ticket.php' class='btn btn-primary'><span class='material-icons'>add</span> &nbsp; Erstelle jetzt eins!</a></div></div>";
            }
        ?>
    </div>
</div>


<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>

