<?php
namespace modules\auth;

class Session
{
    // This is the login session
    public static function start(): void
    {
        session_start();
    }

    public static function parseUser(): ?User
    {
        if (self::has("user_id")) {
            return new User(
                self::get("user_id"),
                self::get("first_name"),
                self::get("last_name"),
                self::get("email"),
                self::get("account_class"),
                self::get("created_at")
            );
        }

        return null;
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function pushUser(User $user): void
    {
        self::set("user_id", $user->userID);
        self::set("first_name", $user->firstName);
        self::set("last_name", $user->lastName);
        self::set("email", $user->email);
        self::set("account_class", $user->accountClass);
        self::set("created_at", $user->createdAt);
    }

    public static function authGuard(AccountType ...$requiredLevel): void
    {
        $user = self::parseUser();

        if ($user === null) {
            header("Location: /login.php");
            exit();
        }

        // Check if the user has the one of the possible account types
        if (in_array($user->accountClass, $requiredLevel, true)) {
            return;
        }

        header("Location: /");
    }

    public static function has($key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    private static function get($string): ?User
    {
        if (self::has($string)) {
            return $_SESSION[$string];
        }

        return null;
    }
}