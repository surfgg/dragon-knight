<?php

/**
 * This script contains the function(s) that handle exploring the map, i.e. chances
 * to fight and other events
 */

function move()
{
    global $userrow, $controlrow;

    $gSize = $controlrow['gamesize'];
    
    if ($userrow["currentaction"] == "Fighting") { header("Location: index.php?do=fight"); die(); }
    
    $latitude = $userrow["latitude"];
    $longitude = $userrow["longitude"];
    if (isset($_POST["north"])) { $latitude++; if ($latitude > $gSize) { $latitude = $gSize; } }
    if (isset($_POST["south"])) { $latitude--; if ($latitude < ($gSize * -1)) { $latitude = ($gSize * -1); } }
    if (isset($_POST["east"])) { $longitude++; if ($longitude > $gSize) { $longitude = $gSize; } }
    if (isset($_POST["west"])) { $longitude--; if ($longitude < ($gSize * -1)) { $longitude = ($gSize * -1); } }
    
    $townquery = doquery("SELECT id FROM {{table}} WHERE latitude='$latitude' AND longitude='$longitude' LIMIT 1", "towns");
    if (mysql_num_rows($townquery) > 0) {
        $townrow = mysql_fetch_array($townquery);
        require 'app/Libs/Town.php';
        travelto($townrow["id"], false);
    }
    
    $chancetofight = rand(1,5);
    if ($chancetofight == 1) { 
        $action = "currentaction='Fighting', currentfight='1',";
    } else {
        $action = "currentaction='Exploring',";
    }

    
    doquery("UPDATE {{table}} SET $action latitude='$latitude', longitude='$longitude', dropcode='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    header("Location: index.php");
}