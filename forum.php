<?php

/**
 * This script handles all the functionality of the internal forum.
 */

require 'app/Libs/Helpers.php';

$link = openLink();
$control = getControl($link);

// If the user isn't logged in, redirect to the login page
if (! checkcookies($link)) { redirect('users.php?do=login'); }

// Get the user's data based on the cookie.
$user = getUserFromCookie($link);

// Force verify if the user isn't verified yet.
if ($control['verifyemail'] == 1 && $user["verify"] != 1) { redirect('users.php?do=verify'); }

// Kill the script if the user has been banned.
if ($user["authlevel"] == 2) { die('You\'ve been banned. Try again later.'); }

// Get the requested action, or default to the user's current action.
$do = isset($_GET['do']) ? explode(':', $_GET['do']) : 'default';

// If the game is closed, set our action to the correct endpoint.
if ($control['gameopen'] == 0) { $do = 'gameClosed'; }

if ($do[0] == "thread") { showthread($do[1], $do[2]); }
elseif ($do[0] == "new") { newthread(); }
elseif ($do[0] == "reply") { reply(); }
elseif ($do[0] == "list") { donothing($do[1]); }
else { doDefault(); }

function doDefault()
{
    global $link;

    $query = query("select * from {{ table }} where parent='0' order by newpostdate desc limit 20", 'forum', $link);
    $page = "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center><a href=\"forum.php?do=new\">New Thread</a></center></th></tr><tr><th width=\"50%\" style=\"background-color:#dddddd;\">Thread</th><th width=\"10%\" style=\"background-color:#dddddd;\">Replies</th><th style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
    $count = 1;
    $threads = $query->fetchAll();

    if (! $threads || count($threads) === 0) { 
        $page .= "<tr><td style=\"background-color:#ffffff;\" colspan=\"3\"><b>No threads in forum.</b></td></tr>\n";
    } else {
        foreach ($threads as $row) {
        	if ($count == 1) {
            	$page .= "<tr><td style=\"background-color:#ffffff;\"><a href=\"forum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
            	$count = 2;
            } else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\"><a href=\"forum.php?do=thread:".$row["id"].":0\">".$row["title"]."</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
                $count = 1;
            }
        }
    }

    $page .= "</table></td></tr></table>";
    
    display($page, "Forum");
}

function showthread($id, $start)
{
    global $link;

    $query = prepare("select * from {{ table }} where id=? or parent=? order by id limit {$start},15", 'forum', $link);
    $query2 = prepare('select title from {{ table }} where id=? limit 1', 'forum', $link);
    $row2 = execute($query2, [$id])->fetch();
    $rows = execute($query, [$id, $id]);
    $page = "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"forum.php\">Forum</a> :: ".safe($row2["title"])."</b></td></tr>\n";
    $count = 1;
    foreach ($rows->fetchAll() as $row) {
        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><b>".$row["author"]."</b><br /><br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#ffffff; vertical-align:top;\">".nl2br(safe($row["content"]))."</td></tr>\n";
            $count = 2;
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><b>".$row["author"]."</b><br /><br />".prettyforumdate($row["postdate"])."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">".nl2br(safe($row["content"]))."</td></tr>\n";
            $count = 1;
        }
    }
    $page .= "</table></td></tr></table><br />";
    $page .= "<table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"forum.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".safe($row2["title"])."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
    
    display($page, "Forum");
}

function reply()
{
    global $user, $link;

    $data = trimData($_POST);

    $query = prepare('insert into {{ table }} set postdate=now(), newpostdate=now(), author=?, parent=?, title=?, content=?', 'forum', $link);
    $query2 = prepare('update {{ table }} set newpostdate=now(), replies = replies + 1 where id=?', 'forum', $link);

    execute($query, [$user['username'], $data['parent'], $data['title'], $data['content']]);
    execute($query2, [$data['parent']]);
    
	redirect("forum.php?do=thread:{$data['parent']}:0");
}

function newthread()
{
    global $user, $link;
    
    if (isset($_POST["submit"])) {
        $data = trimData($_POST);
        $query = prepare("insert into {{ table }} set postdate=now(), newpostdate=now(), author=?, parent='0', title=?, content=?", 'forum', $link);

        execute($query, [$user['username'], $data['title'], $data['content']]);

        redirect('forum.php');
    }
    
    $page = "<table width=\"100%\"><tr><td><b>Make A New Post:</b><br /><br/ ><form action=\"forum.php?do=new\" method=\"post\">Title:<br /><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /></form></td></tr></table>";
    display($page, "Forum");
}