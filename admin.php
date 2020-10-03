<?php // admin.php :: primary administration script.

require 'app/Libs/Helpers.php';

$link = openLink();
$control = getControl($link);

// If the user isn't logged in, redirect to the login page
if (! checkcookies($link)) { redirect('users.php?do=login'); }

// Get the user's data based on the cookie.
$user = getUserFromCookie($link);

// Force verify if the user isn't verified yet.
if ($control['verifyemail'] == 1 && $user["verify"] != 1) { redirect('users.php?do=verify'); }

// If the user isn't an admin, redirect... then kill the script, just in case.
if ($user["authlevel"] != 1) { redirect('index.php'); die(); }

// Get the requested action, or default to the user's current action.
$do = isset($_GET['do']) ? explode(':', $_GET['do']) : 'default';

if ($do[0] == "main") { main(); }
elseif ($do[0] == "items") { items(); }
elseif ($do[0] == "edititem") { edititem($do[1]); }
elseif ($do[0] == "drops") { drops(); }
elseif ($do[0] == "editdrop") { editdrop($do[1]); }
elseif ($do[0] == "towns") { towns(); }
elseif ($do[0] == "edittown") { edittown($do[1]); }
elseif ($do[0] == "monsters") { monsters(); }
elseif ($do[0] == "editmonster") { editmonster($do[1]); }
elseif ($do[0] == "levels") { levels(); }
elseif ($do[0] == "editlevel") { editlevel(); }
elseif ($do[0] == "spells") { spells(); }
elseif ($do[0] == "editspell") { editspell($do[1]); }
elseif ($do[0] == "users") { users(); }
elseif ($do[0] == "edituser") { edituser($do[1]); }
elseif ($do[0] == "news") { addnews(); }
else { doDefault(); }

function doDefault()
{    
    $page = "Welcome to the Dragon Knight Administration section. Use the links on the left bar to control and edit various elements of the game.<br /><br />Please note that the control panel has been created mostly as a shortcut for certain individual settings. It is meant for use primarily with editing one thing at a time. If you need to completely replace an entire table (say, to replace all stock monsters with your own new ones), it is suggested that you use a more in-depth database tool such as <a href=\"http://www.phpmyadmin.net\" target=\"_new\">phpMyAdmin</a>. Also, you may want to have a copy of the Dragon Knight development kit, available from the <a href=\"http://dragon.se7enet.com/dev.php\">Dragon Knight homepage</a>.<br /><br />Also, you should be aware that certain portions of the DK code are dependent on the formatting of certain database results (for example, the special attributes on item drops). While I have attempted to point these out throughout the admin script, you should definitely pay attention and be careful when editing some fields, because mistakes in the database content may result in script errors or your game breaking completely.";
    admindisplay($page, "Admin Home");
}

function main()
{
    global $control, $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errorlist = "";
        if ($gamename == "") { $errorlist .= "Game name is required.<br />"; }
        if (($gamesize % 5) != 0) { $errorlist .= "Map size must be divisible by five.<br />"; }
        if (!is_numeric($gamesize)) { $errorlist .= "Map size must be a number.<br />"; }
        if ($forumtype == 2 && $forumaddress == "") { $errorlist .= "You must specify a forum address when using the External setting.<br />"; }
        if ($class1name == "") { $errorlist .= "Class 1 name is required.<br />"; }
        if ($class2name == "") { $errorlist .= "Class 2 name is required.<br />"; }
        if ($class3name == "") { $errorlist .= "Class 3 name is required.<br />"; }
        if ($diff1name == "") { $errorlist .= "Difficulty 1 name is required.<br />"; }
        if ($diff2name == "") { $errorlist .= "Difficulty 2 name is required.<br />"; }
        if ($diff3name == "") { $errorlist .= "Difficulty 3 name is required.<br />"; }
        if ($diff2mod == "") { $errorlist .= "Difficulty 2 value is required.<br />"; }
        if ($diff3mod == "") { $errorlist .= "Difficulty 3 value is required.<br />"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Main Settings");
        }

        $query = prepare("update {{ table }} set gamename=?, gamesize=?, forumtype=?, forumaddress=?, class1name=?, class2name=?, class3name=?, diff1name=?, diff2name=?, diff3name=?, diff2mod=?, diff3mod=?, gameopen=?, verifyemail=?, gameurl=?, adminemail=?, shownews=?, showonline=?, showbabble=? WHERE id='1'", 'control', $link);
        execute($query, [
            $gamename,
            $gamesize,
            $forumtype,
            $forumaddress,
            $class1name,
            $class2name,
            $class3name,
            $diff1name,
            $diff2name,
            $diff3name,
            $diff2mod,
            $diff3mod,
            $gameopen,
            $verifyemail,
            $gameurl,
            $adminemail,
            $shownews,
            $showonline,
            $showbabble
        ]);

        admindisplay("Settings updated.","Main Settings");
    }
    
    $page = gettemplate('admin/main');

    if ($control["forumtype"] == 0) { $control["selecttype0"] = "selected=\"selected\" "; } else { $control["selecttype0"] = ""; }
    if ($control["forumtype"] == 1) { $control["selecttype1"] = "selected=\"selected\" "; } else { $control["selecttype1"] = ""; }
    if ($control["forumtype"] == 2) { $control["selecttype2"] = "selected=\"selected\" "; } else { $control["selecttype2"] = ""; }
    if ($control["compression"] == 0) { $control["selectcomp0"] = "selected=\"selected\" "; } else { $control["selectcomp0"] = ""; }
    if ($control["compression"] == 1) { $control["selectcomp1"] = "selected=\"selected\" "; } else { $control["selectcomp1"] = ""; }
    if ($control["verifyemail"] == 0) { $control["selectverify0"] = "selected=\"selected\" "; } else { $control["selectverify0"] = ""; }
    if ($control["verifyemail"] == 1) { $control["selectverify1"] = "selected=\"selected\" "; } else { $control["selectverify1"] = ""; }
    if ($control["shownews"] == 0) { $control["selectnews0"] = "selected=\"selected\" "; } else { $control["selectnews0"] = ""; }
    if ($control["shownews"] == 1) { $control["selectnews1"] = "selected=\"selected\" "; } else { $control["selectnews1"] = ""; }
    if ($control["showonline"] == 0) { $control["selectonline0"] = "selected=\"selected\" "; } else { $control["selectonline0"] = ""; }
    if ($control["showonline"] == 1) { $control["selectonline1"] = "selected=\"selected\" "; } else { $control["selectonline1"] = ""; }
    if ($control["showbabble"] == 0) { $control["selectbabble0"] = "selected=\"selected\" "; } else { $control["selectbabble0"] = ""; }
    if ($control["showbabble"] == 1) { $control["selectbabble1"] = "selected=\"selected\" "; } else { $control["selectbabble1"] = ""; }
    if ($control["gameopen"] == 1) { $control["open1select"] = "selected=\"selected\" "; } else { $control["open1select"] = ""; }
    if ($control["gameopen"] == 0) { $control["open0select"] = "selected=\"selected\" "; } else { $control["open0select"] = ""; }

    $page = parsetemplate($page, $control);
    admindisplay($page, "Main Settings");
}

function items()
{
    global $link;

    $query = query('select id, name from {{ table }}', 'items', $link);
    $page = "<b><u>Edit Items</u></b><br />Click an item's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $items = $query->fetchAll();

    if (count($items) > 0) {
        foreach ($items as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=edititem:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=edititem:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No items found.</td></tr>\n";
    }
    
    $page .= "</table>";
    admindisplay($page, "Edit Items");
}

function edititem($id)
{
    global $link;

    if (isset($_POST["submit"])) {
        $data = trimData($_POST);
        
        $errorlist = "";

        if ($data['name'] == "") { $errorlist .= "Name is required.<br />"; }
        if ($data['buycost'] == "") { $errorlist .= "Cost is required.<br />"; }
        if (!is_numeric($data['buycost'])) { $errorlist .= "Cost must be a number.<br />"; }
        if ($data['attribute'] == "") { $errorlist .= "Attribute is required.<br />"; }
        if (!is_numeric($data['attribute'])) { $errorlist .= "Attribute must be a number.<br />"; }

        if ($data['special'] == "" || $data['special'] == " ") { $data['special'] = "X"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Items");
        }

        $query = prepare('update {{ table }} set name=?, type=?, buycost=?, attribute=?, special=? where id=?', 'items', $link);
        execute($query, [$data['name'], $data['type'], $data['buycost'], $data['attribute'], $data['special'], $id]);
        
        admindisplay("Item updated.","Edit Items");
    }   
        
    $row = quick('select * from {{ table }} where id=?', 'items', [$id], $link)->fetch();

    $page = gettemplate('admin/editItem');
    
    if ($row["type"] == 1) { $row["type1select"] = "selected=\"selected\" "; } else { $row["type1select"] = ""; }
    if ($row["type"] == 2) { $row["type2select"] = "selected=\"selected\" "; } else { $row["type2select"] = ""; }
    if ($row["type"] == 3) { $row["type3select"] = "selected=\"selected\" "; } else { $row["type3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Items");
}

function drops()
{
    global $link;

    $query = query('select id, name from {{ table }}', 'drops', $link);
    $page = "<b><u>Edit Drops</u></b><br />Click an item's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $drops = $query->fetchAll();

    if ($drops && count($drops) > 0) {
        foreach ($drops as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editdrop:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editdrop:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No drops found.</td></tr>\n";
    }

    $page .= "</table>";
    admindisplay($page, "Edit Drops");
}

function editdrop($id)
{
    global $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errorlist = "";

        if ($name == "") { $errorlist .= "Name is required.<br />"; }
        if ($mlevel == "") { $errorlist .= "Monster level is required.<br />"; }
        if (!is_numeric($mlevel)) { $errorlist .= "Monster level must be a number.<br />"; }
        if ($attribute1 == "" || $attribute1 == " " || $attribute1 == "X") { $errorlist .= "First attribute is required.<br />"; }

        if ($attribute2 == "" || $attribute2 == " ") { $attribute2 = "X"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Drops");
        }

        $query = prepare('update {{ table }} set name=?, mlevel=?, attribute1=?, attribute2=? where id=?', 'drops', $link);
        execute($query, [$name, $mlevel, $attribute1, $attribute2, $id]);
        
        admindisplay("Drop updated.","Edit Drops");
    }
    
    $row = quick('select * from {{ table }} where id=?', 'drops', [$id], $link)->fetch();

    $page = gettemplate('admin/editDrop');
    $page = parsetemplate($page, $row);

    admindisplay($page, "Edit Drops");
}

function towns()
{
    global $link;

    $query = query('select id, name from {{ table }}', 'towns', $link);
    $page = "<b><u>Edit Towns</u></b><br />Click a town's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $towns = $query->fetchAll();

    if ($towns && count($towns) > 0) {
        foreach ($towns as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=edittown:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=edittown:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n";
    }
    
    $page .= "</table>";
    admindisplay($page, "Edit Towns");
}

function edittown($id)
{
    global $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($latitude == "") { $errors++; $errorlist .= "Latitude is required.<br />"; }
        if (!is_numeric($latitude)) { $errors++; $errorlist .= "Latitude must be a number.<br />"; }
        if ($longitude == "") { $errors++; $errorlist .= "Longitude is required.<br />"; }
        if (!is_numeric($longitude)) { $errors++; $errorlist .= "Longitude must be a number.<br />"; }
        if ($innprice == "") { $errors++; $errorlist .= "Inn Price is required.<br />"; }
        if (!is_numeric($innprice)) { $errors++; $errorlist .= "Inn Price must be a number.<br />"; }
        if ($mapprice == "") { $errors++; $errorlist .= "Map Price is required.<br />"; }
        if (!is_numeric($mapprice)) { $errors++; $errorlist .= "Map Price must be a number.<br />"; }

        if ($travelpoints == "") { $errors++; $errorlist .= "Travel Points is required.<br />"; }
        if (!is_numeric($travelpoints)) { $errors++; $errorlist .= "Travel Points must be a number.<br />"; }
        if ($itemslist == "") { $errors++; $errorlist .= "Items List is required.<br />"; }
        
        if ($errors == 0) {
            $query = prepare('update {{ table }} set name=?, latitude=?, longitude=?, innprice=?, mapprice=?, travelpoints=?, itemslist=? where id=?', 'towns', $link);
            execute($query, [$name, $latitude, $longitude, $innprice, $mapprice, $travelpoints, $itemslist, $id]);

            admindisplay("Town updated.","Edit Towns");
        }
        
        admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Towns");
    }
    
    $row = quick('select * from {{ table }} where id=?', 'towns', [$id], $link)->fetch();

    $page = gettemplate('admin/editTown');
    $page = parsetemplate($page, $row);

    admindisplay($page, "Edit Towns");
}

function monsters()
{
    global $control, $link;
    
    $statrow = query('select * from {{ table }} order by level desc limit 1', 'monsters', $link)->fetch();
    $query = query('select id, name from {{ table }}', 'monsters', $link);

    $page = "<b><u>Edit Monsters</u></b><br />";
    
    if (($control["gamesize"] / 5) != $statrow["level"]) {
        $page .= "<span class=\"highlight\">Note:</span> Your highest monster level does not match with your entered map size. Highest monster level should be ".($control["gamesize"]/5).", yours is ".$statrow["level"].". Please fix this before opening the game to the public.<br /><br />";
    } else { $page .= "Monster level and map size match. No further actions are required for map compatibility.<br /><br />"; }
    
    $page .= "Click a monster's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $monsters = $query->fetchAll();

    if($monsters && count($monsters) > 0) {
        foreach ($monsters as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editmonster:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editmonster:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No towns found.</td></tr>\n";
    }

    $page .= "</table>";

    admindisplay($page, "Edit Monster");
}

function editmonster($id)
{
    global $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($maxhp == "") { $errors++; $errorlist .= "Max HP is required.<br />"; }
        if (!is_numeric($maxhp)) { $errors++; $errorlist .= "Max HP must be a number.<br />"; }
        if ($maxdam == "") { $errors++; $errorlist .= "Max Damage is required.<br />"; }
        if (!is_numeric($maxdam)) { $errors++; $errorlist .= "Max Damage must be a number.<br />"; }
        if ($armor == "") { $errors++; $errorlist .= "Armor is required.<br />"; }
        if (!is_numeric($armor)) { $errors++; $errorlist .= "Armor must be a number.<br />"; }
        if ($level == "") { $errors++; $errorlist .= "Monster Level is required.<br />"; }
        if (!is_numeric($level)) { $errors++; $errorlist .= "Monster Level must be a number.<br />"; }
        if ($maxexp == "") { $errors++; $errorlist .= "Max Exp is required.<br />"; }
        if (!is_numeric($maxexp)) { $errors++; $errorlist .= "Max Exp must be a number.<br />"; }
        if ($maxgold == "") { $errors++; $errorlist .= "Max Gold is required.<br />"; }
        if (!is_numeric($maxgold)) { $errors++; $errorlist .= "Max Gold must be a number.<br />"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit monsters");
        }

        $query = prepare('update {{ table }} set name=?, maxhp=?, maxdam=?, armor=?, level=?, maxexp=?, maxgold=?, immune=? where id=?', 'monsters', $link);
        execute($query, [$name, $maxhp, $maxdam, $armor, $level, $maxexp, $maxgold, $immune, $id]);
        
        admindisplay("Monster updated.","Edit monsters");
    }   
        
    $row = quick('select * from {{ table }} where id=?', 'monsters', [$id], $link)->fetch();
    $page = gettemplate('admin/editMonster');
    
    if ($row["immune"] == 1) { $row["immune1select"] = "selected=\"selected\" "; } else { $row["immune1select"] = ""; }
    if ($row["immune"] == 2) { $row["immune2select"] = "selected=\"selected\" "; } else { $row["immune2select"] = ""; }
    if ($row["immune"] == 3) { $row["immune3select"] = "selected=\"selected\" "; } else { $row["immune3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Monsters");
}

function spells()
{
    global $link;

    $query = query('select id, name from {{ table }}', 'spells', $link);
    $page = "<b><u>Edit Spells</u></b><br />Click a spell's name to edit it.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $spells = $query->fetchAll();

    if ($spells && count($spells) > 0) {
        foreach ($spells as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=editspell:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=editspell:".$row["id"]."\">".$row["name"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No spells found.</td></tr>\n";
    }

    $page .= "</table>";

    admindisplay($page, "Edit Spells");
}

function editspell($id)
{
    global $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";

        if ($name == "") { $errors++; $errorlist .= "Name is required.<br />"; }
        if ($mp == "") { $errors++; $errorlist .= "MP is required.<br />"; }
        if (!is_numeric($mp)) { $errors++; $errorlist .= "MP must be a number.<br />"; }
        if ($attribute == "") { $errors++; $errorlist .= "Attribute is required.<br />"; }
        if (!is_numeric($attribute)) { $errors++; $errorlist .= "Attribute must be a number.<br />"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Spells");
        }

        $query = prepare('update {{ table }} set name=?, mp=?, attribute=?, type=? where id=?', 'spells', $link);
        execute($query, [$name, $mp, $attribute, $type, $id]);
        
        admindisplay("Spell updated.","Edit Spells");
    }   
        
    $row = quick('select * from {{ table }} where id=?', 'spells', [$id], $link)->fetch();

    $page = gettemplate('admin/editSpell');

    if ($row["type"] == 1) { $row["type1select"] = "selected=\"selected\" "; } else { $row["type1select"] = ""; }
    if ($row["type"] == 2) { $row["type2select"] = "selected=\"selected\" "; } else { $row["type2select"] = ""; }
    if ($row["type"] == 3) { $row["type3select"] = "selected=\"selected\" "; } else { $row["type3select"] = ""; }
    if ($row["type"] == 4) { $row["type4select"] = "selected=\"selected\" "; } else { $row["type4select"] = ""; }
    if ($row["type"] == 5) { $row["type5select"] = "selected=\"selected\" "; } else { $row["type5select"] = ""; }
    
    $page = parsetemplate($page, $row);

    admindisplay($page, "Edit Spells");
}

function levels()
{
    global $link;

    $row = query('select id from {{ table }} order by id desc limit 1', 'levels', $link)->fetch();
    
    $options = "";
    for($i = 2; $i < $row["id"]; $i++) {
        $options .= "<option value=\"{$i}\">{$i}</option>\n";
    }
    
    $page = gettemplate('admin/levelsDropdown');
    $page = parsetemplate($page, ['options' => $options]);

    admindisplay($page, "Edit Levels");
}

function editlevel()
{
    global $link, $control;

    if (!isset($_POST["level"])) { admindisplay("No level to edit.", "Edit Levels"); }
    $id = $_POST["level"];
    
    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($_POST["one_exp"] == "") { $errors++; $errorlist .= "Class 1 Experience is required.<br />"; }
        if ($_POST["one_hp"] == "") { $errors++; $errorlist .= "Class 1 HP is required.<br />"; }
        if ($_POST["one_mp"] == "") { $errors++; $errorlist .= "Class 1 MP is required.<br />"; }
        if ($_POST["one_tp"] == "") { $errors++; $errorlist .= "Class 1 TP is required.<br />"; }
        if ($_POST["one_strength"] == "") { $errors++; $errorlist .= "Class 1 Strength is required.<br />"; }
        if ($_POST["one_dexterity"] == "") { $errors++; $errorlist .= "Class 1 Dexterity is required.<br />"; }
        if ($_POST["one_spells"] == "") { $errors++; $errorlist .= "Class 1 Spells is required.<br />"; }
        if (!is_numeric($_POST["one_exp"])) { $errors++; $errorlist .= "Class 1 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["one_hp"])) { $errors++; $errorlist .= "Class 1 HP must be a number.<br />"; }
        if (!is_numeric($_POST["one_mp"])) { $errors++; $errorlist .= "Class 1 MP must be a number.<br />"; }
        if (!is_numeric($_POST["one_tp"])) { $errors++; $errorlist .= "Class 1 TP must be a number.<br />"; }
        if (!is_numeric($_POST["one_strength"])) { $errors++; $errorlist .= "Class 1 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["one_dexterity"])) { $errors++; $errorlist .= "Class 1 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["one_spells"])) { $errors++; $errorlist .= "Class 1 Spells must be a number.<br />"; }

        if ($_POST["two_exp"] == "") { $errors++; $errorlist .= "Class 2 Experience is required.<br />"; }
        if ($_POST["two_hp"] == "") { $errors++; $errorlist .= "Class 2 HP is required.<br />"; }
        if ($_POST["two_mp"] == "") { $errors++; $errorlist .= "Class 2 MP is required.<br />"; }
        if ($_POST["two_tp"] == "") { $errors++; $errorlist .= "Class 2 TP is required.<br />"; }
        if ($_POST["two_strength"] == "") { $errors++; $errorlist .= "Class 2 Strength is required.<br />"; }
        if ($_POST["two_dexterity"] == "") { $errors++; $errorlist .= "Class 2 Dexterity is required.<br />"; }
        if ($_POST["two_spells"] == "") { $errors++; $errorlist .= "Class 2 Spells is required.<br />"; }
        if (!is_numeric($_POST["two_exp"])) { $errors++; $errorlist .= "Class 2 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["two_hp"])) { $errors++; $errorlist .= "Class 2 HP must be a number.<br />"; }
        if (!is_numeric($_POST["two_mp"])) { $errors++; $errorlist .= "Class 2 MP must be a number.<br />"; }
        if (!is_numeric($_POST["two_tp"])) { $errors++; $errorlist .= "Class 2 TP must be a number.<br />"; }
        if (!is_numeric($_POST["two_strength"])) { $errors++; $errorlist .= "Class 2 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["two_dexterity"])) { $errors++; $errorlist .= "Class 2 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["two_spells"])) { $errors++; $errorlist .= "Class 2 Spells must be a number.<br />"; }
                
        if ($_POST["three_exp"] == "") { $errors++; $errorlist .= "Class 3 Experience is required.<br />"; }
        if ($_POST["three_hp"] == "") { $errors++; $errorlist .= "Class 3 HP is required.<br />"; }
        if ($_POST["three_mp"] == "") { $errors++; $errorlist .= "Class 3 MP is required.<br />"; }
        if ($_POST["three_tp"] == "") { $errors++; $errorlist .= "Class 3 TP is required.<br />"; }
        if ($_POST["three_strength"] == "") { $errors++; $errorlist .= "Class 3 Strength is required.<br />"; }
        if ($_POST["three_dexterity"] == "") { $errors++; $errorlist .= "Class 3 Dexterity is required.<br />"; }
        if ($_POST["three_spells"] == "") { $errors++; $errorlist .= "Class 3 Spells is required.<br />"; }
        if (!is_numeric($_POST["three_exp"])) { $errors++; $errorlist .= "Class 3 Experience must be a number.<br />"; }
        if (!is_numeric($_POST["three_hp"])) { $errors++; $errorlist .= "Class 3 HP must be a number.<br />"; }
        if (!is_numeric($_POST["three_mp"])) { $errors++; $errorlist .= "Class 3 MP must be a number.<br />"; }
        if (!is_numeric($_POST["three_tp"])) { $errors++; $errorlist .= "Class 3 TP must be a number.<br />"; }
        if (!is_numeric($_POST["three_strength"])) { $errors++; $errorlist .= "Class 3 Strength must be a number.<br />"; }
        if (!is_numeric($_POST["three_dexterity"])) { $errors++; $errorlist .= "Class 3 Dexterity must be a number.<br />"; }
        if (!is_numeric($_POST["three_spells"])) { $errors++; $errorlist .= "Class 3 Spells must be a number.<br />"; }

        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Spells");
        }

        $query = "update {{ table }} set
            1_exp=?, 1_hp=?, 1_mp=?, 1_tp=?, 1_strength=?, 1_dexterity=?, 1_spells=?,
            2_exp=?, 2_hp=?, 2_mp=?, 2_tp=?, 2_strength=?, 2_dexterity=?, 2_spells=?,
            3_exp=?, 3_hp=?, 3_mp=?, 3_tp=?, 3_strength=?, 3_dexterity=?, 3_spells=?
            WHERE id=?";
        $data = [
            $one_exp, $one_hp, $one_mp, $one_tp, $one_strength, $one_dexterity, $one_spells,
            $two_exp, $two_hp, $two_mp, $two_tp, $two_strength, $two_dexterity, $two_spells,
            $three_exp, $three_hp, $three_mp, $three_tp, $three_strength, $three_dexterity, $three_spells,
            $id
        ];
        
        quick($query, 'levels', $data, $link);

        admindisplay("Level updated.","Edit Levels");
    }   
        
    $row = quick('select * from {{ table }} where id=?', 'levels', [$id], $link)->fetch();

    $row['class1name'] = $control["class1name"];
    $row['class2name'] = $control["class2name"];
    $row['class3name'] = $control["class3name"];

    $page = gettemplate('admin/editLevel');
    $page = parsetemplate($page, $row);

    admindisplay($page, "Edit Levels");
}

function users()
{
    global $link;

    $query = query('select id, username from {{ table }}', 'users', $link);
    $page = "<b><u>Edit Users</u></b><br />Click a username to edit the account.<br /><br /><table width=\"50%\">\n";
    $count = 1;
    $users = $query->fetchAll();

    if ($users && count($users) > 0) {
        foreach ($users as $row) {
            if ($count == 1) { $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">".$row["id"]."</td><td style=\"background-color: #eeeeee;\"><a href=\"admin.php?do=edituser:".$row["id"]."\">".$row["username"]."</a></td></tr>\n"; $count = 2; }
            else { $page .= "<tr><td width=\"8%\" style=\"background-color: #ffffff;\">".$row["id"]."</td><td style=\"background-color: #ffffff;\"><a href=\"admin.php?do=edituser:".$row["id"]."\">".$row["username"]."</a></td></tr>\n"; $count = 1; }
        }
    } else {
        $page .= "<tr><td width=\"8%\" style=\"background-color: #eeeeee;\">No spells found.</td></tr>\n";
    }

    $page .= "</table>";
    admindisplay($page, "Edit Users");
}

function edituser($id)
{
    global $link, $control;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($email == "") { $errors++; $errorlist .= "Email is required.<br />"; }
        if ($verify == "") { $errors++; $errorlist .= "Verify is required.<br />"; }
        if ($authlevel == "") { $errors++; $errorlist .= "Auth Level is required.<br />"; }
        if ($latitude == "") { $errors++; $errorlist .= "Latitude is required.<br />"; }
        if ($longitude == "") { $errors++; $errorlist .= "Longitude is required.<br />"; }
        if ($difficulty == "") { $errors++; $errorlist .= "Difficulty is required.<br />"; }
        if ($charclass == "") { $errors++; $errorlist .= "Character Class is required.<br />"; }
        if ($currentaction == "") { $errors++; $errorlist .= "Current Action is required.<br />"; }
        if ($currentfight == "") { $errors++; $errorlist .= "Current Fight is required.<br />"; }
        
        if ($currentmonster == "") { $errors++; $errorlist .= "Current Monster is required.<br />"; }
        if ($currentmonsterhp == "") { $errors++; $errorlist .= "Current Monster HP is required.<br />"; }
        if ($currentmonstersleep == "") { $errors++; $errorlist .= "Current Monster Sleep is required.<br />"; }
        if ($currentmonsterimmune == "") { $errors++; $errorlist .= "Current Monster Immune is required.<br />"; }
        if ($currentuberdamage == "") { $errors++; $errorlist .= "Current Uber Damage is required.<br />"; }
        if ($currentuberdefense == "") { $errors++; $errorlist .= "Current Uber Defense is required.<br />"; }
        if ($currenthp == "") { $errors++; $errorlist .= "Current HP is required.<br />"; }
        if ($currentmp == "") { $errors++; $errorlist .= "Current MP is required.<br />"; }
        if ($currenttp == "") { $errors++; $errorlist .= "Current TP is required.<br />"; }
        if ($maxhp == "") { $errors++; $errorlist .= "Max HP is required.<br />"; }

        if ($maxmp == "") { $errors++; $errorlist .= "Max MP is required.<br />"; }
        if ($maxtp == "") { $errors++; $errorlist .= "Max TP is required.<br />"; }
        if ($level == "") { $errors++; $errorlist .= "Level is required.<br />"; }
        if ($gold == "") { $errors++; $errorlist .= "Gold is required.<br />"; }
        if ($experience == "") { $errors++; $errorlist .= "Experience is required.<br />"; }
        if ($goldbonus == "") { $errors++; $errorlist .= "Gold Bonus is required.<br />"; }
        if ($expbonus == "") { $errors++; $errorlist .= "Experience Bonus is required.<br />"; }
        if ($strength == "") { $errors++; $errorlist .= "Strength is required.<br />"; }
        if ($dexterity == "") { $errors++; $errorlist .= "Dexterity is required.<br />"; }
        if ($attackpower == "") { $errors++; $errorlist .= "Attack Power is required.<br />"; }

        if ($defensepower == "") { $errors++; $errorlist .= "Defense Power is required.<br />"; }
        if ($weaponid == "") { $errors++; $errorlist .= "Weapon ID is required.<br />"; }
        if ($armorid == "") { $errors++; $errorlist .= "Armor ID is required.<br />"; }
        if ($shieldid == "") { $errors++; $errorlist .= "Shield ID is required.<br />"; }
        if ($slot1id == "") { $errors++; $errorlist .= "Slot 1 ID is required.<br />"; }
        if ($slot2id == "") { $errors++; $errorlist .= "Slot 2 ID is required.<br />"; }
        if ($slot3id == "") { $errors++; $errorlist .= "Slot 3 ID is required.<br />"; }
        if ($weaponname == "") { $errors++; $errorlist .= "Weapon Name is required.<br />"; }
        if ($armorname == "") { $errors++; $errorlist .= "Armor Name is required.<br />"; }
        if ($shieldname == "") { $errors++; $errorlist .= "Shield Name is required.<br />"; }

        if ($slot1name == "") { $errors++; $errorlist .= "Slot 1 Name is required.<br />"; }
        if ($slot2name == "") { $errors++; $errorlist .= "Slot 2 Name is required.<br />"; }
        if ($slot3name == "") { $errors++; $errorlist .= "Slot 3 Name is required.<br />"; }
        if ($dropcode == "") { $errors++; $errorlist .= "Drop Code is required.<br />"; }
        if ($spells == "") { $errors++; $errorlist .= "Spells is required.<br />"; }
        if ($towns == "") { $errors++; $errorlist .= "Towns is required.<br />"; }
        
        if (!is_numeric($authlevel)) { $errors++; $errorlist .= "Auth Level must be a number.<br />"; }
        if (!is_numeric($latitude)) { $errors++; $errorlist .= "Latitude must be a number.<br />"; }
        if (!is_numeric($longitude)) { $errors++; $errorlist .= "Longitude must be a number.<br />"; }
        if (!is_numeric($difficulty)) { $errors++; $errorlist .= "Difficulty must be a number.<br />"; }
        if (!is_numeric($charclass)) { $errors++; $errorlist .= "Character Class must be a number.<br />"; }
        if (!is_numeric($currentfight)) { $errors++; $errorlist .= "Current Fight must be a number.<br />"; }
        if (!is_numeric($currentmonster)) { $errors++; $errorlist .= "Current Monster must be a number.<br />"; }
        if (!is_numeric($currentmonsterhp)) { $errors++; $errorlist .= "Current Monster HP must be a number.<br />"; }
        if (!is_numeric($currentmonstersleep)) { $errors++; $errorlist .= "Current Monster Sleep must be a number.<br />"; }
        
        if (!is_numeric($currentmonsterimmune)) { $errors++; $errorlist .= "Current Monster Immune must be a number.<br />"; }
        if (!is_numeric($currentuberdamage)) { $errors++; $errorlist .= "Current Uber Damage must be a number.<br />"; }
        if (!is_numeric($currentuberdefense)) { $errors++; $errorlist .= "Current Uber Defense must be a number.<br />"; }
        if (!is_numeric($currenthp)) { $errors++; $errorlist .= "Current HP must be a number.<br />"; }
        if (!is_numeric($currentmp)) { $errors++; $errorlist .= "Current MP must be a number.<br />"; }
        if (!is_numeric($currenttp)) { $errors++; $errorlist .= "Current TP must be a number.<br />"; }
        if (!is_numeric($maxhp)) { $errors++; $errorlist .= "Max HP must be a number.<br />"; }
        if (!is_numeric($maxmp)) { $errors++; $errorlist .= "Max MP must be a number.<br />"; }
        if (!is_numeric($maxtp)) { $errors++; $errorlist .= "Max TP must be a number.<br />"; }
        if (!is_numeric($level)) { $errors++; $errorlist .= "Level must be a number.<br />"; }
        
        if (!is_numeric($gold)) { $errors++; $errorlist .= "Gold must be a number.<br />"; }
        if (!is_numeric($experience)) { $errors++; $errorlist .= "Experience must be a number.<br />"; }
        if (!is_numeric($goldbonus)) { $errors++; $errorlist .= "Gold Bonus must be a number.<br />"; }
        if (!is_numeric($expbonus)) { $errors++; $errorlist .= "Experience Bonus must be a number.<br />"; }
        if (!is_numeric($strength)) { $errors++; $errorlist .= "Strength must be a number.<br />"; }
        if (!is_numeric($dexterity)) { $errors++; $errorlist .= "Dexterity must be a number.<br />"; }
        if (!is_numeric($attackpower)) { $errors++; $errorlist .= "Attack Power must be a number.<br />"; }
        if (!is_numeric($defensepower)) { $errors++; $errorlist .= "Defense Power must be a number.<br />"; }
        if (!is_numeric($weaponid)) { $errors++; $errorlist .= "Weapon ID must be a number.<br />"; }
        if (!is_numeric($armorid)) { $errors++; $errorlist .= "Armor ID must be a number.<br />"; }
        
        if (!is_numeric($shieldid)) { $errors++; $errorlist .= "Shield ID must be a number.<br />"; }
        if (!is_numeric($slot1id)) { $errors++; $errorlist .= "Slot 1 ID  must be a number.<br />"; }
        if (!is_numeric($slot2id)) { $errors++; $errorlist .= "Slot 2 ID must be a number.<br />"; }
        if (!is_numeric($slot3id)) { $errors++; $errorlist .= "Slot 3 ID must be a number.<br />"; }
        if (!is_numeric($dropcode)) { $errors++; $errorlist .= "Drop Code must be a number.<br />"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Edit Users");
        }

        $query = "update {{ table }} set
            email=?, verify=?, authlevel=?, latitude=?, longitude=?, difficulty=?, charclass=?, currentaction=?, currentfight=?,
            currentmonster=?, currentmonsterhp=?, currentmonstersleep=?, currentmonsterimmune=?, currentuberdamage=?,
            currentuberdefense=?, currenthp=?, currentmp=?, currenttp=?, maxhp=?, maxmp=?, maxtp=?, level=?, gold=?, experience=?,
            goldbonus=?, expbonus=?, strength=?, dexterity=?, attackpower=?, defensepower=?, weaponid=?, armorid=?, shieldid=?, slot1id=?,
            slot2id=?, slot3id=?, weaponname=?, armorname=?, shieldname=?, slot1name=?, slot2name=?, slot3name=?, dropcode=?, spells=?,
            towns=? WHERE id=?";
        $data = [
            $email, $verify, $authlevel, $latitude, $longitude, $difficulty, $charclass, $currentaction, $currentfight,
            $currentmonster, $currentmonsterhp, $currentmonstersleep, $currentmonsterimmune, $currentuberdamage,
            $currentuberdefense, $currenthp, $currentmp, $currenttp, $maxhp, $maxmp, $maxtp, $level, $gold, $experience,
            $goldbonus, $expbonus, $strength, $dexterity, $attackpower, $defensepower, $weaponid, $armorid, $shieldid, $slot1id,
            $slot2id, $slot3id, $weaponname, $armorname, $shieldname, $slot1name, $slot2name, $slot3name, $dropcode, $spells,
            $towns, $id
        ];

        quick($query, 'users', $data, $link);
        admindisplay("User updated.","Edit Users");
    }   
    
    $row = quick('select * from {{ table }} where id=?', 'users', [$id], $link)->fetch();

    $page = gettemplate('admin/editUser');

    $row['diff1name'] = $control["diff1name"];
    $row['diff2name'] = $control["diff2name"];
    $row['diff3name'] = $control["diff3name"];
    $row['class1name'] = $control["class1name"];
    $row['class2name'] = $control["class2name"];
    $row['class3name'] = $control["class3name"];

    if ($row["authlevel"] == 0) { $row["auth0select"] = "selected=\"selected\" "; } else { $row["auth0select"] = ""; }
    if ($row["authlevel"] == 1) { $row["auth1select"] = "selected=\"selected\" "; } else { $row["auth1select"] = ""; }
    if ($row["authlevel"] == 2) { $row["auth2select"] = "selected=\"selected\" "; } else { $row["auth2select"] = ""; }
    if ($row["charclass"] == 1) { $row["class1select"] = "selected=\"selected\" "; } else { $row["class1select"] = ""; }
    if ($row["charclass"] == 2) { $row["class2select"] = "selected=\"selected\" "; } else { $row["class2select"] = ""; }
    if ($row["charclass"] == 3) { $row["class3select"] = "selected=\"selected\" "; } else { $row["class3select"] = ""; }
    if ($row["difficulty"] == 1) { $row["diff1select"] = "selected=\"selected\" "; } else { $row["diff1select"] = ""; }
    if ($row["difficulty"] == 2) { $row["diff2select"] = "selected=\"selected\" "; } else { $row["diff2select"] = ""; }
    if ($row["difficulty"] == 3) { $row["diff3select"] = "selected=\"selected\" "; } else { $row["diff3select"] = ""; }
    
    $page = parsetemplate($page, $row);
    admindisplay($page, "Edit Users");
}

function addnews()
{
    global $link;

    if (isset($_POST["submit"])) {
        extract($_POST);
        $errors = 0;
        $errorlist = "";
        if ($content == "") { $errors++; $errorlist .= "Content is required.<br />"; }
        
        if (! empty($errorlist)) {
            admindisplay("<b>Errors:</b><br /><div style=\"color:red;\">$errorlist</div><br />Please go back and try again.", "Add News");
        }

        $query = prepare('insert into {{ table }} set postdate=now(), content=?', 'news', $link);
        execute($query, [$content]);

        admindisplay("News post added.","Add News");
    }   
        
    $page = gettemplate('admin/addNews');
    
    admindisplay($page, "Add News");
}