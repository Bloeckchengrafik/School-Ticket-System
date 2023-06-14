<?php
include_once "modules/html/init.php";

use modules\auth\Session as sess;
use modules\auth\AccountType as accountType;
use modules\db\Models\Ticket;
use function Database\connection;

$page = "newticket";

sess::authGuard(accountType::Teacher, accountType::Admin);
$user = sess::parseUser();

$conn = connection();

if (isset($_POST["title"]) && isset($_POST["building"]) && isset($_POST["room"]) && isset($_POST["device"]) && isset($_POST["message"])) {
    $title = $_POST["title"];
    $room = $_POST["room"];
    $device = intval($_POST["device"]);
    $message = $_POST["message"];

    $ticket = Ticket::create(
        $title,
        "open",
        $user->userID,
        $room,
        $device
    );

    $ticket->addMember($user->userID, $user->firstName, $user->lastName);
    $ticket->send($message, $user->userID);

    header("Location: ticket.php?id=" . $ticket->ticket_id);

    exit();
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include_once "modules/html/head.php"; ?>
    <title>Ticket erstellen | Support</title>
</head>
<body class="page">
<?php include_once "modules/html/nav.php"; ?>


<div class="page-wrapper">
    <form class="container card container-lg card-full" method="post" action="">
        <div class="card-header">
            <h1 class="mt0">Neues Ticket</h1>
        </div>
        <div class="card-body">
            <div class="margin-bottom-sm">
                <label class="form-label">
                    <span class="material-icons">edit_note</span> Titel
                    <input type="text" name="title" class="form-control"
                           placeholder="Titel einfügen..." required>
                </label>
            </div>
            <div class="margin-bottom-sm lr row">
                <label class="form-label flex-grow col">
                    <span class="material-icons">apartment</span> Geb&auml;ude
                    <select name="building" class="form-control" id="building" onchange="changeBuilding()" required>
                        <option value="" disabled>SELECT BUILDING</option>
                        <option value="Hauptgebäude">Hauptgeb&auml;ude</option>
                        <option value="Westgebäude">Westgeb&auml;ude</option>
                        <option value="Q-Gebäude">Q-Geb&auml;ude</option>
                        <option value="Externes Gebäude">Externes Geb&auml;ude</option>
                    </select>
                </label>
                <label class="form-label flex-grow col">
                    <span class="material-icons">room</span> Raum
                    <select name="room" class="form-control" required>
                        <option value="" disabled>SELECT BUILDING FIRST</option>
                        <?php
                        $rooms = $conn->query("SELECT * FROM Room");
                        foreach ($rooms as $data) {
                            ?>
                            <option value="<?= $data["room_id"] ?>"
                                    class="<?= $data["building"][0] ?>"><?= $data["room_id"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
            </div>
            <div class="margin-bottom-sm lr row">
                <label class="form-label flex-grow col">
                    <span class="material-icons">devices</span> Ger&auml;t
                    <select name="device" class="form-control" id="device" onchange="changeDevice()" required>
                        <?php
                        $devices = $conn->query("SELECT device_id, device_name FROM Device");
                        foreach ($devices as $device) {
                            ?>
                            <option value="<?= $device["device_id"] ?>"><?= $device["device_name"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
                <label class="form-label flex-grow col">
                    <span class="material-icons">report</span> Fehler
                    <select class="form-control" id="preset" onchange="changePreset()">
                        <option value="">Eigener Fehler</option>
                        <?php
                        $msgs = $conn->query("SELECT * FROM PresetMessage");
                        foreach ($msgs as $data) {
                            ?>
                            <option value="<?= $data["content"] ?>"
                                    class="<?= $data["device_id"] ?>"><?= $data["name"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
            </div>
            <div class="margin-bottom-sm lr flex-grow d-flex">
                <label class="form-label flex-grow">
                    Nachricht
                    <textarea id="tinymce-default" class="flex-grow" name="message"></textarea>
                </label>
            </div>
        </div>

        <div class="card-footer flex justify-end">
            <button class="btn btn-primary">Neuen Erstellen
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
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

    function changeBuilding() {
        let code = document.getElementById('building').value.substring(0, 1);
        let allCodes = ["H", "W", "Q", "E"];
        for (let i = 0; i < allCodes.length; i++) {
            let rooms = document.getElementsByClassName(allCodes[i]);
            for (let j = 0; j < rooms.length; j++) {
                rooms[j].disabled = true;
            }
        }
        let rooms = document.getElementsByClassName(code);
        for (let i = 0; i < rooms.length; i++) {
            rooms[i].disabled = false;
        }
    }

    function changeDevice() {
        let code = document.getElementById('device').value.substring(0, 1);
        let allCodes = [
            <?php
            $devices = $conn->query("SELECT device_id FROM Device");
            foreach ($devices as $device) {
                echo "'".$device["device_id"]."',";
            }
            ?>
        ];
        for (let i = 0; i < allCodes.length; i++) {
            let rooms = document.getElementsByClassName(allCodes[i]);
            for (let j = 0; j < rooms.length; j++) {
                rooms[j].style.display = "none";
            }
        }
        let rooms = document.getElementsByClassName(code);
        for (let i = 0; i < rooms.length; i++) {
            rooms[i].style.display = "block";
        }
    }

    function changePreset() {
        let preset = document.getElementById('preset').value;
        if (preset !== "") {
            tinyMCE.activeEditor.setContent(preset);
        }
    }

    changeBuilding()
    changeDevice()
</script>

<span class="text-muted text-center margin-bottom-sm">&copy; 2023. Christian Bergschneider</span>

<?php include_once "modules/html/scripts.php"; ?>
</body>
</html>

