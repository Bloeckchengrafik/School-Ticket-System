<?php

namespace modules\db\Models;

use modules\auth\User;
use modules\db\Users;
use function Database\connection;

class Ticket
{
    public int $ticket_id;
    public string $title;
    public string $status;
    public int $userId;
    public string $roomId;
    public int $deviceId;

    public function __construct($ticket_id, $title, $status, $userId, $roomId, $deviceId)
    {
        $this->ticket_id = $ticket_id;
        $this->title = $title;
        $this->status = $status;
        $this->userId = $userId;
        $this->roomId = $roomId;
        $this->deviceId = $deviceId;
    }

    public function messages(): array
    {
        return Message::byTicketId($this->ticket_id);
    }

    public function room(): Room
    {
        return Room::byId($this->roomId);
    }

    public function device(): Device
    {
        return Device::byId($this->deviceId);
    }

    public function user(): User
    {
        return Users::byId($this->userId);
    }

    public function createdAt(): string
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT created_at FROM Ticket WHERE ticket_id = :ticket_id");
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result["created_at"];
    }

    public function stateTransition($newState): void
    {
        $conn = connection();
        $stmt = $conn->prepare("UPDATE Ticket SET status = :status WHERE ticket_id = :ticket_id");
        $stmt->bindParam(":status", $newState);
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->execute();

        $this->status = $newState;
    }

    public function members(): array {
        $conn = connection();
        $stmt = $conn->prepare("SELECT user_id FROM isMemberIn WHERE ticket_id = :ticket_id");
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $members = [];

        foreach ($result as $member) {
            $members[] = $member["user_id"];
        }

        return $members;
    }

    public function addMember($userId, $firstName, $lastName): void {
        $conn = connection();
        $stmt = $conn->prepare("INSERT INTO isMemberIn (user_id, ticket_id) VALUES (:user_id, :ticket_id)");
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->execute();

        $this->send($firstName . " " . $lastName . " ist beigetreten", $userId, "hr");
    }

    public function removeMember($userId, $firstName, $lastName): void {
        $conn = connection();
        $stmt = $conn->prepare("DELETE FROM isMemberIn WHERE user_id = :user_id AND ticket_id = :ticket_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->execute();
        $this->send($firstName . " " . $lastName . " hat das Ticket verlassen", $userId, "hr");
    }

    public function canView($userId): bool {
        // Check if the userid has admin or support role
        $conn = connection();
        $stmt = $conn->prepare("SELECT account_class FROM User WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();

        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        $account_class = $result["account_class"];

        if ($account_class == "admin" || $account_class == "supporter") {
            return true;
        }

        return in_array($userId, $this->members());
    }

    public function send($content, $user_id, $message_type = "standard"): Message
    {
        return Message::send($content, $message_type, $user_id, $this->ticket_id);
    }

    public static function create($title, $status, $userId, $roomId, $deviceId): Ticket
    {
        $conn = connection();
        $stmt = $conn->prepare("INSERT INTO Ticket (title, status, user_id, room_id, device_id) VALUES (:title, :status, :user_id, :room_id, :device_id)");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":room_id", $roomId);
        $stmt->bindParam(":device_id", $deviceId);
        $stmt->execute();

        return new Ticket($conn->lastInsertId(), $title, $status, $userId, $roomId, $deviceId);
    }

    public static function byId($id): ?Ticket
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM Ticket WHERE ticket_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return new Ticket($result["ticket_id"], $result["title"], $result["status"], $result["user_id"], $result["room_id"], $result["device_id"]);
    }

    public static function all(): array
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM Ticket");
        $stmt->execute();

        $result = $stmt->fetchAll();

        $tickets = [];

        foreach ($result as $ticket) {
            $tickets[] = new Ticket($ticket["ticket_id"], $ticket["title"], $ticket["status"], $ticket["user_id"], $ticket["room_id"], $ticket["device_id"]);
        }

        return $tickets;
    }
}