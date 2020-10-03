<?php

/**
 * This script contains the function(s) that handle exploring the map, i.e. chances
 * to fight and other events
 */

function displayExplore()
{
    return gettemplate('explore');
}

function move()
{
    global $user, $control, $testLink;

    $gSize = $control['gamesize'];
    
    if ($user["currentaction"] == "Fighting") { redirect("index.php?do=fight"); }
    
    $latitude = $user["latitude"];
    $longitude = $user["longitude"];
    if (isset($_POST["north"])) { $latitude++; if ($latitude > $gSize) { $latitude = $gSize; } }
    if (isset($_POST["south"])) { $latitude--; if ($latitude < ($gSize * -1)) { $latitude = ($gSize * -1); } }
    if (isset($_POST["east"])) { $longitude++; if ($longitude > $gSize) { $longitude = $gSize; } }
    if (isset($_POST["west"])) { $longitude--; if ($longitude < ($gSize * -1)) { $longitude = ($gSize * -1); } }
    
    if (townExists($latitude, $longitude, $testLink)) {
        $town = getTown($latitude, $longitude, $testLink);
        travelto($town["id"], false);
    }
    
    $chanceToFight = rand(1, 5);
    if ($chanceToFight == 1) { 
        $action = "currentaction='Fighting', currentfight='1'";
    } else {
        $action = "currentaction='Exploring'";
    }

    $update = prepare("update {{ table }} set {$action}, latitude=?, longitude=?, dropcode='0' where id=?", 'users');
    execute($update, [$latitude, $longitude, $user['id']]);

    header("Location: index.php");
}