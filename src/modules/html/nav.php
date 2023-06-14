<?php
include_once "modules/html/init.php";

use modules\auth\AccountType;
use modules\auth\Session as sess;
use modules\auth\User;

global $page;

/* @var $user User
 */
$user = sess::parseUser();
?>

<nav>
    <div>
        <div>
            <a class="brand" href="/">
                <img src="/assets/img/favicon_dark.png" alt="LOGO">
                CONTINUUM SUPPORT
            </a>

            <ul class="navbar">
                <li class="navitem <?php if ($page == "dashboard") {
                    echo "active";
                } ?>"><a href="/"><span class="material-icons">speed</span>Dashboard</a></li>
                <li class="navitem <?php if ($page == "tickets") {
                    echo "active";
                } ?>"><a href="/tickets.php"><span class="material-icons">apps</span><?php

                        switch ($user->accountClass()) {
                            case AccountType::Supporter:
                            case AccountType::Admin:
                                echo "Alle";
                                break;
                            case AccountType::Teacher:
                                echo "Deine";
                                break;
                        }

                        ?> Tickets</a></li>
                <li class="navitem <?php if ($page == "newticket") {
                    echo "active";
                } ?>"><a href="/new_ticket.php"><span class="material-icons">edit_note</span>Neues Ticket</a></li>
                <?php if ($user->accountClass() == AccountType::Admin) { ?>
                    <li class="navitem <?php if ($page == "admin") {
                        echo "active";
                    } ?>"><a href="/manage.php"><span class="material-icons">settings</span>Verwalten</a></li>
                <?php } ?>
            </ul>
        </div>
        <div>
            <div class="avatar bg-cover me-2 bg-danger-lt"><?= User::prepareInitials($user->firstName, $user->lastName) ?></div>
            <div class="user">
                <span>
                    <?php
                    echo $user->firstName . " " . $user->lastName;
                    ?>
                </span>
                <span class="sm capitalize bean <?= $user->accountClass ?>">
                    <?php
                    echo $user->accountClass;
                    ?>
                </span>
            </div>
            <div class="user">
                <a href="/logout.php" class="logout"><span class="material-icons">logout</span></a>
            </div>
        </div>
    </div>
</nav>