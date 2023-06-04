<?php
include_once "modules/html/init.php";
use modules\auth\Session as sess;
global $page;
?>

<nav>
    <div>
        <div>
            <a class="brand" href="/">
                <img src="/assets/img/favicon_dark.png" alt="LOGO">
                CONTINUUM SUPPORT
            </a>

            <ul class="navbar">
                <li class="navitem <?php if ($page == "dashboard") { echo "active"; } ?>"><a href="#">Dashboard</a></li>
                <li class="navitem <?php if ($page == "tickets") { echo "active"; } ?>"><a href="#">Meine Tickets</a></li>
                <li class="navitem <?php if ($page == "newticket") { echo "active"; } ?>"><a href="#">Neues Ticket</a></li>
            </ul>
        </div>
        <div>
            <img class="profilepicture" src="<?php
            $user = sess::parseUser();

            if ($user == null) {
                echo "";
            } else {
                // Hash the email to prevent direct access to the profile picture
                $profilePictureSeed = md5($user->userID . $user->email);
                echo "https://rest.devstorage.eu/user/avatar/" .
                    $profilePictureSeed;
            }
            ?>" alt="PROFILE" height="32">
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