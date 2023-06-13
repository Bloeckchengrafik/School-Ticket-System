<?php

namespace modules\db\Models;

use DateTime;
use function Database\connection;

class Message
{
    public int $message_id;
    public string $content;
    public string $message_type;
    public string $created_at;
    public int $user_id;
    public int $ticket_id;

    public function __construct($message_id, $content, $message_type, $created_at, $user_id, $ticket_id)
    {
        $this->message_id = $message_id;
        $this->content = $content;
        $this->message_type = $message_type;
        $this->created_at = $created_at;
        $this->user_id = $user_id;
        $this->ticket_id = $ticket_id;
    }

    public static function byTicketId($ticket_id): array
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM Message WHERE ticket_id = :ticket_id");
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $messages = [];
        foreach ($result as $message) {
            $messages[] = new Message($message["message_id"], $message["content"], $message["message_type"], $message["created_at"], $message["user_id"], $message["ticket_id"]);
        }

        return $messages;
    }

    public static function send($content, $message_type, $user_id, $ticket_id): Message
    {
        $conn = connection();
        $stmt = $conn->prepare("INSERT INTO Message (content, message_type, user_id, ticket_id) VALUES (:content, :message_type, :user_id, :ticket_id)");
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":message_type", $message_type);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();

        return new Message($conn->lastInsertId(), $content, $message_type, "", $user_id, $ticket_id);
    }
}