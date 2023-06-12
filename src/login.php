<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\db\Users;
use modules\mail\Mailer;

sess::authGuardInvert();

if (isset($_GET["key"])) {
    $key = $_GET["key"];
    $user = Users::byMagicKey($key);
    if ($user != null) {
        sess::pushUser($user);

        if (Users::canLoginUsingPassword($user))
            header("Location: .");
        else
            header("Location: update_password.php");
        exit();
    }
}

$isMagic = isset($_GET["magic"]);
?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <?php include_once "modules/html/head.php"; ?>
        <title>Login | Support</title>
    </head>
    <body>

    <div class="page page-center">
        <div class="container container-tight">
            <div class="text-center margin-bottom">
                <a href="." class="navbar-brand block"><img src="/assets/img/favicon_dark.png" alt="" height="36"></a>
                <a href="." class="navbar-brand block">CONTINUUM SUPPORT</a>
            </div>
            <?php
            $userError = false;
            $magicSent = false;
            if (isset($_POST["email"])) {
                // Got an auth request
                $email = $_POST["email"];
                $user = Users::byEmail($email);

                if ($user == null) {
                    $userError = true;
                } else {
                    // Check if a password was provided
                    if (isset($_POST["password"])) {
                        // Check if the password is correct
                        if ($user->checkPassword($_POST["password"])) {
                            // Password is correct
                            sess::pushUser($user);
                            header("Location: .");
                            exit();
                        } else {
                            // Password is incorrect
                            $userError = true;
                        }
                    } else {
                        // No password was provided
                        // Send magic link
                        $user->sendMagicLink();
                        $magicSent = true;
                    }
                }
            }

            if (!$magicSent) {
                ?>
                <div class="card">
                    <div class="card-header margin-bottom card-md">
                        <h2 class="h2 text-center">Anmeldung</h2>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="post">
                            <div class="margin-bottom-sm">
                                <label class="form-label">
                                    <span class="material-icons">mail</span> E-Mail-Adresse
                                    <input type="email" name="email" class="form-control"
                                           placeholder="email@example.com">
                                </label>
                                <?php if ($userError) { ?>
                                    <span class="invalid-feedback">Account wurde nicht gefunden</span>
                                <?php } ?>
                            </div>
                            <?php if (!$isMagic) { ?>
                                <div class="margin-bottom-sm">
                                    <label class="form-label">
                                        <span class="material-icons">key</span> Passwort
                                        <input type="password" name="password" class="form-control"
                                               placeholder="Passwort"
                                               autocomplete="off">
                                    </label>
                                    <span class="form-label-description"><a
                                            href="forgot-password.php">Passwort vergessen?</a></span>
                                </div>
                            <?php } ?>
                            <div class="form-footer text-center">
                                <button type="submit" class="btn btn-primary">
                                    <?php if ($isMagic): ?>
                                        Link senden
                                    <?php else: ?>
                                        Anmelden
                                    <?php endif; ?>
                                </button>
                            </div>
                        </form>
                        <div class="hr-text">oder</div>
                        <div class="text-center">
                            <?php if ($isMagic): ?>
                                <a href="?" class="btn btn-white" data-cold>Anmeldung per Nutzername und
                                    Passwort</a>
                            <?php else: ?>
                                <a href="?magic" class="btn btn-white" data-cold>Anmeldung per Link</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="text-center">
                    <div class="my-5">
                        <h2 class="h1">Link gesendet</h2>
                        <p class="fs-h3 text-muted">
                            Wir haben dir einen Link zum Anmelden gesendet. Bitte überprüfe deine E-Mails.
                        </p>

                        <a href=".?" class="btn btn-primary" data-cold>Zurück zur Anmeldung</a>
                    </div>
                </div>
            <?php } ?>
            <div class="text-center text-muted margin-top">
                &copy; 2023. Christian Bergschneider
            </div>
        </div>
    </div>

    <?php include_once "modules/html/scripts.php"; ?>
    </body>
    </html>

<?php
Mailer::sendAllLast();