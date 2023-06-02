<?php
namespace Database;

include_once "config/config.php";

use PDO;

$connection = new PDO(
    $GLOBALS["config"]["mysql"]["connection"],
    $GLOBALS["config"]["mysql"]["username"],
    $GLOBALS["config"]["mysql"]["password"]
);

function connection()
{
    global $connection;
    return $connection;
}