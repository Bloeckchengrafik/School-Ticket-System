<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\User;
use modules\db\Users;
use modules\mail\Mailer;

/* @var $user User */
$user = null;
$isInitial = false;

$alreadyHandled = false;
$oldWrong = false;

if (!isset($_GET["key"])) {
    sess::authGuard();
    $user = sess::parseUser();
    $isInitial = !Users::canLoginUsingPassword($user);
} else if (!isset($_POST["password"])) {
    $user = Users::byMagicKey($_GET["key"]);
    if ($user == null) {
        header("Location: /");
        echo "User not found";
        exit();
    }
    sess::pushUser($user);
    $isInitial = true;
}

if (isset($_POST["password"])) {
    $user = sess::parseUser();
    if ($isInitial) {
        Users::setPassword($user, $_POST["password"]);
        header("Location: .");
        exit();
    } else {
        // check password
        if (!$user->checkPassword($_POST["old_password"]) && !isset($_GET["key"])) {
            $oldWrong = true;
        } else {
            Users::setPassword($user, $_POST["password"]);
            sess::authGuard();
        }
    }
    $alreadyHandled = true;
}
?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <?php include_once "modules/html/head.php"; ?>
        <title>Dein Passwort | Support</title>
    </head>
    <body>

    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand d-block"><img src="/assets/img/favicon_dark.png" alt="" height="36"></a>
                <a href="." class="navbar-brand d-block">CONTINUUM SUPPORT</a>
            </div>
            <?php if ($alreadyHandled) { ?>
                <div class="alert alert-success">
                    <?php
                    if ($isInitial) {
                        echo "Dein Passwort wurde gesetzt. Du kannst dich nun einloggen.";
                    } else {
                        echo "Dein Passwort wurde geändert, bitte warte einen Moment";
                    }

                    sess::destroy();
                    sess::start();

                    header("Refresh: 3; url=/");
                    ?>
                </div>
            <?php } ?>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">
                        <?php
                        if ($isInitial) {
                            echo "Passwort setzen";
                        } else {
                            echo "Passwort ändern";
                        }
                        ?>
                    </h2>
                    <form action="" method="post">
                        <?php if (!$isInitial) { ?>
                            <div class="mb-2">
                                <label class="form-label">
                                    Altes Passwort
                                    <input type="password" name="old_password" class="form-control"
                                           placeholder="Passwort"
                                           autocomplete="off" required>
                                </label>
                                <?php if ($oldWrong) { ?>
                                    <div class="invalid-feedback d-block">
                                        Das alte Passwort ist falsch.
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="mb-2">
                            <label class="form-label">
                                Neues Passwort
                                <input type="password" name="password" class="form-control"
                                       placeholder="Passwort"
                                       autocomplete="off" required>
                            </label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                Neues Passwort wiederholen
                                <input type="password" name="password_again" class="form-control"
                                       placeholder="Passwort"
                                       autocomplete="off">
                            </label>
                        </div>
                        <div class="form-footer text-center">
                            <button type="submit" class="btn btn-primary">
                                <?php
                                if ($isInitial) {
                                    echo "Passwort setzen";
                                } else {
                                    echo "Passwort ändern";
                                }
                                ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                &copy; 2023. Christian Bergschneider
            </div>
        </div>
    </div>

    <?php include_once "modules/html/scripts.php"; ?>
    </body>
    </html>

<?php
Mailer::sendAllLast();