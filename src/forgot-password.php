<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\User;
use modules\db\Users;
use modules\mail\Mailer;

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
            <?php if (!isset($_POST["mail"])) { ?>
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">
                            Passwort zurücksetzen
                        </h2>
                        <form action="" method="post">
                            <div class="mb-2">
                                <label class="form-label">
                                    E-Mail-Adresse
                                    <input type="email" name="mail" class="form-control"
                                           placeholder="Email" required>
                                </label>
                            </div>
                            <div class="form-footer text-center">
                                <button type="submit" class="btn btn-primary">
                                    Link senden
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } else {

                $user = Users::byEmail($_POST["mail"]);
                $user?->sendForgotPasswordLink();

                ?>
                <div class="text-center">
                    <div class="my-5">
                        <h2 class="h1">Link gesendet</h2>
                        <p class="fs-h3 text-muted">
                            Wir haben dir einen Link zum Zurücksetzen deines Passwortes gesendet. Bitte überprüfe deine E-Mails.
                        </p>

                        <a href=".?" class="btn btn-primary" data-cold>Zurück</a>
                    </div>
                </div>
                <?php
            }
            ?>
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