<?php

namespace modules\mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$jobs = [];

class Mailer
{

    static function send($to, $subject, $body): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $GLOBALS["config"]["mail"]["host"];
            $mail->SMTPAuth = true;
            $mail->Username = $GLOBALS["config"]["mail"]["username"];
            $mail->Password = $GLOBALS["config"]["mail"]["password"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $GLOBALS["config"]["mail"]["port"];

            $mail->setFrom($GLOBALS["config"]["mail"]["from"], $GLOBALS["config"]["mail"]["fromName"]);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            global $jobs;
            $jobs[] = $mail;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    static function partialResponse(): void
    {
        flush();
    }

    static function sendAllLast(): void
    {
        Mailer::partialResponse();

        global $jobs;
        foreach ($jobs as $job) {
            $job->send();
        }
    }
}