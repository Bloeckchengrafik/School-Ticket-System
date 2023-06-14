<?php

namespace modules\auth;

use modules\db\Users;
use modules\mail\Mailer;
use modules\mail\View;
use PDO;
use function Database\connection;

class User
{
    // This is the user
    public int $userID;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $accountClass;
    public string $createdAt;

    function __construct($userID, $firstName, $lastName, $email, $accountClass, $createdAt)
    {
        $this->userID = $userID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->accountClass = $accountClass;
        $this->createdAt = $createdAt;
    }

    public function sendMagicLink($initial = false): void
    {
        $root = $_SERVER["HTTP_HOST"];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
        $code = Users::createMagicLinkCode($this);
        $magicLink = "$protocol://$root/login.php?key=$code";

        $template = $initial ? "magiclink_initial" : "magiclink";

        $view = new View($template);
        $rendered = $view->render([
            "user" => $this,
            "magicLink" => $magicLink
        ]);

        $mail = new Mailer();
        $mail->send($this->email, "Continuum Magic Link", $rendered);
    }

    public function accountClass(): ?AccountType
    {
        return match ($this->accountClass) {
            "admin" => AccountType::Admin,
            "teacher" => AccountType::Teacher,
            "supporter" => AccountType::Supporter,
            default => null
        };
    }

    public function checkPassword(mixed $password)
    {
        // Get a password passkey from the database
        $db = connection();
        $stmt = $db->prepare("SELECT `password_hash` FROM `UserPassKey` WHERE `user_id` = :id LIMIT 1") or die();
        $stmt->bindParam("id", $this->userID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If there is no password passkey, return false
        if (count($result) == 0) {
            return false;
        }

        // Check the password against the passkey
        return password_verify($password, $result[0]["password_hash"]);
    }

    public function sendForgotPasswordLink(): void
    {
        $root = $_SERVER["HTTP_HOST"];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
        $code = Users::createMagicLinkCode($this);
        $magicLink = "$protocol://$root/update_password.php?key=$code";

        $view = new View("forgot_password");
        $rendered = $view->render([
            "user" => $this,
            "link" => $magicLink
        ]);

        $mail = new Mailer();
        $mail->send($this->email, "Continuum Password Reset", $rendered);
    }

    public function __toString(): string
    {
        return $this->firstName . " " . $this->lastName;
    }

    public static function prepareInitials(string $firstName, string $lastName): string
    {
        $initials = substr($firstName, 0, 1) . substr($lastName, 0, 1);
        return strtoupper($initials);
    }
}