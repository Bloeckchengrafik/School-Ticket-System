<?php
include_once "modules/html/init.php";

use modules\mail\Mailer;

exec("touch " . __DIR__ . "/mail.log");

function logMail($message): void
{
    $file = fopen(__DIR__ . "/mail.log", "a");
    fwrite($file, $message . "\n");
    fclose($file);
}

$id = $argv[1];
$idNum = intval($id);

logMail("Sending mail $idNum");
try {
    $ans = Mailer::sendId($idNum);
    logMail("Sent mail $idNum ($ans)");
} catch (\PHPMailer\PHPMailer\Exception $e) {
    logMail("Failed to send mail $idNum");
    logMail($e->getMessage());
}
