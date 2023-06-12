<?php

namespace modules\db\Models;

use function Database\connection;

class Room
{
    public string $room_id;
    public string $building;

    public function __construct($room_id, $building)
    {
        $this->room_id = $room_id;
        $this->building = $building;
    }

    public static function byId($room_id): Room
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM Room WHERE room_id = :room_id");
        $stmt->bindParam(":room_id", $room_id);
        $stmt->execute();

        $result = $stmt->fetch();

        return new Room($result["room_id"], $result["building"]);
    }

    public function __toString(): string
    {
        return $this->room_id . " (" . $this->building . ")";
    }
}