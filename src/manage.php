<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\db\Users;
use modules\mail\Mailer;
use function Database\connection;

$page = "admin";

sess::authGuard(accountType::Admin);

$conn = connection();

$success = false;
$error = false;

$userSuccess = false;
$userError = false;

if (isset($_POST["building"]) && isset($_POST["id"])) {
    $stmt = $conn->prepare("INSERT INTO `Room` (`building`, room_id) VALUES (:name, :room)");
    $stmt->bindParam(":name", $_POST["building"]);
    $stmt->bindParam(":room", $_POST["id"]);
    try {
        $stmt->execute();
        $success = true;
    } catch (PDOException $e) {
        $error = true;
    }
}

if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["account_class"])) {
    $stmt = $conn->prepare("INSERT INTO `User` (`first_name`, `last_name`, `email`, `account_class`) VALUES (:first_name, :last_name, :email, :account_class)");
    $stmt->bindParam(":first_name", $_POST["first_name"]);
    $stmt->bindParam(":last_name", $_POST["last_name"]);
    $stmt->bindParam(":email", $_POST["email"]);
    $stmt->bindParam(":account_class", $_POST["account_class"]);
    try {
        $stmt->execute();

        $id = $conn->lastInsertId();

        $user = Users::byId($id);
        $user->sendMagicLink(true);

        $userSuccess = true;
    } catch (PDOException $e) {
        $userError = true;
    }
}

$stmt = $conn->prepare("SELECT * FROM `Room`");
$stmt->execute();
$rooms = $stmt->fetchAll();

$userStmt = $conn->prepare("SELECT * FROM `User`");
$userStmt->execute();
$users = $userStmt->fetchAll();
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
    <?php if ($success) { ?>
        <div class="alert alert-info">
            <h3 class="m0">Erfolgreich hinzugefügt</h3>
        </div>
    <?php } ?>
    <?php if ($error) { ?>
        <div class="alert alert-warning">
            <h3 class="m0">Fehler beim hinzufügen</h3>
        </div>
    <?php } ?>

    <?php if ($userSuccess) { ?>
        <div class="alert alert-info">
            <h3 class="m0">Erfolgreich hinzugefügt</h3>
        </div>
    <?php } ?>
    <?php if ($userError) { ?>
        <div class="alert alert-warning">
            <h3 class="m0">Fehler beim hinzufügen</h3>
        </div>
    <?php } ?>

    <form class="container card container-lg card-full" method="post" action="">
        <div class="card-header">
            <h1 class="mt0">Räume</h1>
        </div>
        <div class="card-body">
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Name
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rooms as $room) { ?>
                    <tr>
                        <td>
                            <?= $room["room_id"] ?>
                        </td>
                        <td>
                            <?= $room["building"] ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <label for="id"></label>
                        <input type="text" name="id" id="id" placeholder="ENTER ROOM ID">
                    </td>
                    <td>
                        <label for="building"></label>
                        <select name="building" id="building">
                            <option value="" disabled>SELECT BUILDING</option>
                            <option value="Hauptgebäude">Hauptgeb&auml;ude</option>
                            <option value="Westgebäude">Westgeb&auml;ude</option>
                            <option value="Q-Gebäude">Q-Geb&auml;ude</option>
                            <option value="Externes Gebäude">Externes Geb&auml;ude</option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="card-footer flex justify-end">
            <button class="btn btn-primary">Hinzufügen</button>
        </div>
    </form>
    <div class="container card container-lg card-full">
        <div class="card-header">
            <h1 class="mt0">Benutzerverwaltung</h1>
        </div>
        <form class="card-body" method="post" action="">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Account-Klasse</th>
                    <th>Erstellungszeit</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?= $user["user_id"] ?></td>
                        <td><?= $user["first_name"] ?> <?= $user["last_name"] ?></td>
                        <td><?= $user["email"] ?></td>
                        <td><?= $user["account_class"] ?></td>
                        <td><?= $user["created_at"] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <dialog id="create" class="form">
                <div class="form-control">
                    <label for="first_name">Vorname</label>
                    <input type="text" name="first_name" id="first_name">
                </div>
                <div class="form-control">
                    <label for="last_name">Nachname</label>
                    <input type="text" name="last_name" id="last_name">
                </div>
                <div class="form-control">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email">
                </div>
                <div class="form-control">
                    <label for="account_class">Account-Klasse</label>
                    <select name="account_class" id="account_class">
                        <option value="admin">Admin</option>
                        <option value="supporter">Support</option>
                        <option value="teacher">Lehrer</option>
                    </select>
                </div>
                <button class="btn btn-primary">Erstellen</button>
            </dialog>
        </form>

        <div class="card-footer flex justify-end">
            <button class="btn btn-primary" onclick="document.getElementById('create').showModal()">Neuen Erstellen
            </button>
        </div>
    </div>
</div>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>

<?php Mailer::sendAllLast(); ?>