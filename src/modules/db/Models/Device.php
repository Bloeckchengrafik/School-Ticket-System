<?php

namespace modules\db\Models;

use function Database\connection;

class Device
{
    public int $device_id;
    public string $device_name;
    public string $device_description;

    public function __construct($device_id, $device_name, $device_description)
    {
        $this->device_id = $device_id;
        $this->device_name = $device_name;
        $this->device_description = $device_description;
    }

    public static function byId($device_id): Device
    {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM Device WHERE device_id = :device_id");
        $stmt->bindParam(":device_id", $device_id);
        $stmt->execute();

        $result = $stmt->fetch();

        return new Device($result["device_id"], $result["device_name"], $result["device_description"]);
    }
}