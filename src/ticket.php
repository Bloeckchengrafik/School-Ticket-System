<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\auth\User;
use modules\db\Models\Message;
use modules\db\Models\Ticket;
use modules\db\Users;
use function Database\connection;

$page = "tickets";

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

if (isset($_GET["join"])) {
    $ticket->addMember($user->userID, $user->firstName, $user->lastName);
}

if (isset($_GET["leave"])) {
    $ticket->removeMember($user->userID, $user->firstName, $user->lastName);
}

if (isset($_GET["close"])) {
    $ticket->stateTransition("closed");
}

if (isset($_GET["open"])) {
    $ticket->stateTransition("open");
}

if (isset($_POST["message"])) {
    $ticket->send($_POST["message"], $user->userID);
}

$ticket->markRead($user->userID);
$messages = $ticket->messages();

$is_member = in_array($user->userID, $ticket->members())

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
                    <span
                        class="avatar avatar-lg rounded material-icons text-white <?= $ticket->status == "open" ? "bg-lime" : "bg-red" ?>"><span
                            class="material-icons"><?= $ticket->status == "open" ? "import_contacts" : "close" ?></span></span>
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
                        <?php
                        if ($is_member) {
                            ?>
                            <a href="?id=<?= $ticket->ticket_id ?>&leave"
                               class="btn btn-red">
                                <span class="material-icons">person_remove</span> &nbsp;
                                Verlassen
                            </a>
                            <?php
                        } else {
                            ?>
                            <a href="?id=<?= $ticket->ticket_id ?>&join"
                               class="btn btn-primary">
                                <span class="material-icons">person_add</span> &nbsp;
                                Beitreten
                            </a>
                            <?php
                        }
                        ?>
                        <?php
                        if ($user->accountClass() == accountType::Admin || $user->accountClass() == accountType::Supporter) {

                            if ($ticket->status == "open") {
                                ?>
                                <a href="?id=<?= $ticket->ticket_id ?>&close"
                                   class="btn btn-red">
                                    <span class="material-icons">close</span> &nbsp;
                                    Schlie&szlig;en
                                </a>
                                <?php
                            } else {
                                ?>
                                <a href="?id=<?= $ticket->ticket_id ?>&open"
                                   class="btn btn-green">
                                    <span class="material-icons">import_contacts</span> &nbsp;
                                    &Ouml;ffnen
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row g3">
                <div class="col">
                    <div class="col">
                        <ul class="timeline">
                            <?php
                            foreach ($messages

                                     as $message) {
                                /* @var $message Message */

                                $userOfMessage = Users::byId($message->user_id);
                                ?>
                                <li class="timeline-event">
                                    <div
                                        class="timeline-event-icon avatar bg-danger-lt"><?= User::prepareInitials($user->firstName, $user->lastName) ?></div>

                                    <?php

                                    if ($message->message_type == "standard") {
                                        ?>
                                        <div class="card timeline-event-card">
                                            <div class="card-body">
                                                <div class="text-muted float-end"><?= $message->created_at ?></div>
                                                <h4><?= $userOfMessage->firstName ?> <?= $userOfMessage->lastName ?></h4>
                                                <p class="text-muted"><?= $message->content ?></p>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="timeline-event-card">
                                            <div class="flex-fill">
                                                <div class="hr-text pt-3"><?= $message->content ?></div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }

                            if ($is_member && $ticket->status == "open") {
                                ?>
                                <li class="timeline-event">
                                    <div
                                        class="timeline-event-icon avatar bg-cover bg-danger-lt"><?= User::prepareInitials($user->firstName, $user->lastName) ?></div>
                                    <div class="card timeline-event-card">
                                        <div class="card-body">
                                            <form action="" method="post">
                                                <div class="input-group gap-2">
                                                    <label class="form-label flex-grow">
                                                        <textarea id="tinymce-default" class="flex-grow"
                                                                  name="message"></textarea>
                                                    </label>

                                                    <button class="btn btn-primary" type="submit">Senden</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let options = {
            selector: '#tinymce-default',
            height: 300,
            menubar: false,
            statusbar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
        }
        tinyMCE.init(options);
    })
</script>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>

