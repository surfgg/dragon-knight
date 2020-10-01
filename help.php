<?php

require 'app/Libs/Lib.php'; 

$link = opendb();
$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);

$with = isset($_GET['with']) ? $_GET['with'] : 'main';
$pages = ['main', 'items', 'monsters', 'spells', 'levels'];

if (! in_array($with, $pages)) { $with = 'main'; }

ob_start();

require "templates/help/{$with}.php";

echo ob_get_clean();