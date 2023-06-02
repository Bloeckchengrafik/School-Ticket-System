<?php

namespace modules\db;

use modules\auth\User;
use PDO;
use function Database\connection;

class Users
{
    static function byEmail($email): ?User
    {
        $db = connection();
        $stmt = $db->prepare("SELECT * FROM `User` WHERE `email` = :mail LIMIT 1") or die();
        $stmt->bindParam("mail", $email);
        return self::extractUser($stmt);
    }

    static function createMagicLinkCode($user): string
    {
        try {
            $code = bin2hex(random_bytes(32));

            $db = connection();
            $stmt = $db->prepare("INSERT INTO `UserMagicLinkKey` (user_id, code) VALUES (:user, :code)") or die();
            $stmt->bindParam("user", $user->userID);
            $stmt->bindParam("code", $code);
            $stmt->execute();

            return $code;
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function byId(int $id): ?User
    {
        $db = connection();
        $stmt = $db->prepare("SELECT * FROM `User` WHERE `user_id` = :id LIMIT 1") or die();
        $stmt->bindParam("id", $id);
        return self::extractUser($stmt);
    }

    public static function byMagicKey(string $key): ?User
    {
        $db = connection();
        $stmt = $db->prepare("SELECT * FROM `UserMagicLinkKey` WHERE `code` = :code LIMIT 1") or die();
        $stmt->bindParam("code", $key);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return null;
        }

        $user = self::byId($result[0]["user_id"]);
        // Drop the magic link code
        $stmt = $db->prepare("DELETE FROM `UserMagicLinkKey` WHERE `code` = :code") or die();
        $stmt->bindParam("code", $key);
        $stmt->execute();

        return $user;
    }

    /**
     * @param \PDOStatement $stmt
     * @return User|null
     */
    public static function extractUser(\PDOStatement $stmt): ?User
    {
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return null;
        }

        return new User(
            $result[0]["user_id"],
            $result[0]["first_name"],
            $result[0]["last_name"],
            $result[0]["email"],
            $result[0]["account_class"],
            $result[0]["created_at"]
        );
    }

    public static function canLoginUsingPassword(User $user): bool
    {
        $db = connection();
        $stmt = $db->prepare("SELECT * FROM `UserPassKey` WHERE `user_id` = :id LIMIT 1") or die();
        $stmt->bindParam("id", $user->userID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    public static function setPassword(User $user, string $password): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = connection();
        if (self::canLoginUsingPassword($user)) {
            $stmt = $db->prepare("UPDATE `UserPassKey` SET `password_hash` = :pass WHERE `user_id` = :id") or die();
        } else {
            $stmt = $db->prepare("INSERT INTO `UserPassKey` (user_id, password_hash) VALUES (:id, :pass)") or die();
        }
        $stmt->bindParam("id", $user->userID);
        $stmt->bindParam("pass", $hash);
        $stmt->execute();
    }
}