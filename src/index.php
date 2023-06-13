<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\db\Models\Ticket;

$page = "dashboard";

sess::authGuard(accountType::Supporter, accountType::Admin, accountType::Teacher);
$user = sess::parseUser();

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
        <div class="card-body">
            <?php

            $tickets = Ticket::allWithUnreadMessages($user->userID, $user->accountClass() == accountType::Teacher);
            foreach ($tickets as $ticket) {
                ?>
                <a class="card text-reset mt-3" href="/ticket.php?id=<?= $ticket->ticket_id ?>">
                    <div class="card-body d-flex gap-2">
                        <span
                            class="avatar avatar-sm rounded material-icons text-white <?= $ticket->status == "open" ? "bg-lime" : "bg-red" ?>"><span
                                class="material-icons"><?= $ticket->status == "open" ? "import_contacts" : "close" ?></span></span>
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
                    <div class='ribbon ribbon-bookmark'><span class='material-icons'>mark_email_unread</span> &nbsp;Neue
                        Nachrichten
                    </div>
                </a>
                <?php
            }

            if (count($tickets) == 0) {
            ?>
            <div class="empty">
                <span class="text-muted">
                    Es sind keine Events vorhanden.
                </span>
            </div>

            <?php
            }
            ?>
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>
