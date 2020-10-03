<?php

require 'app/Libs/Helpers.php';

$link = openLink();

$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($page == 2) { second(); }
elseif ($page == 3) { third(); }
elseif ($page == 4) { fourth(); }
else { first(); }

/**
 * Get a query file and it's contents from our sql/ directory.
 */
function getQuery(string $query)
{
    $path = "resources/sql/install/{$query}.sql";

    if (is_readable($path)) {
        return file_get_contents($path);
    } else {
        throw new Exception('Query not found.');
    }
}

/**
 * Performs an install query. Just a convenient wrapper. Also
 * returns a result string.
 */
function doInstallQuery(string $query, string $table, string $verb = 'create')
{
    global $link;

    $query = getQuery($query);
    $result = query($query, $table, $link);

    if ($verb === 'create') {
        return $result ? "Created {$table} table." : "Failed to create {$table}.";
    } elseif ($verb === 'populate') {
        return $result ? "Populated {$table} table." : "Failed to populate {$table}.";
    } else {
        return $result ? 'Performed an install query.' : 'An install query failed.';
    }
}

// First page - show warnings and gather info.
function first()
{
    echo gettemplate('install/first');
}

// Second page - set up the database tables.
function second()
{
    global $link;

    $results = [];
    $tables = ['Babble', 'Control', 'Drops', 'Items', 'Forum', 'Levels', 'Monsters', 'News', 'Spells', 'Towns', 'Users'];
    $full = isset($_POST['complete']) ? true : false;
    
    foreach ($tables as $table) {
        $results[] = doInstallQuery("create{$table}", strtolower($table));
    }

    // Populate control table. This will always happen, regardless of the
    // installation type.
    $query = "insert into {{ table }} values (1, 'Dragon Knight', 250, 1, '', '', 1, '', 'Mage', 'Warrior', 'Paladin', 'Easy', '1', 'Medium', '1.2', 'Hard', '1.5', 1, 0, 1, 1, 1);";
    $results[] = query($query, 'control', $link) ? 'Populated '.prefix('control').' table.' : 'Failed to populate '.prefix('control').'.';

    // Populate the tables if "Complete" was selected
    if ($full) {
        $populated = ['Drops', 'Items', 'Levels', 'Monsters', 'Spells', 'Towns'];

        foreach ($populated as $key) {
            $results[] = doInstallQuery("pop{$key}", strtolower($key), 'populate');
        }
    }
    
    global $start;
    $time = round((getmicrotime() - $start), 4);

    $result = '';
    foreach ($results as $r) { $result .= "<li>{$r}</li>"; }
    $result = "<ul>{$result}</ul>";

    $page = gettemplate('install/second');
    echo parsetemplate($page, ['time' => $time, 'result' => $result]);
}

// The admin account form page!
function third(string $errors = '')
{
    $page = gettemplate('install/third');
    echo parsetemplate($page, ['errors' => $errors]);
}

// If all checks out, create the adming account and congratulate the player.
function fourth()
{
    global $link;

    $data = trimData($_POST);
    $errors = [];
    
    if (empty($data['username'])) { $errors[] = 'Username is required.'; }
    if (empty($data['password1']) || empty($data['password2'])) { $errors[] = 'Passwords are required.'; }
    if ($data['password1'] != $data['password2']) { $errors[] = "Passwords don't match."; }
    if (empty($data['email1']) || empty($data['email2'])) { $errors[] = 'Email addresses are required.'; }
    if ($data['email1'] != $data['email2']) { $errors[] = "Email addresses don't match."; }

    if (! empty($errors)) {
        $list = '';
        foreach ($errors as $error) { $list .= "<li>{$error}</li>"; }
        $list = "<ul>{$list}</ul>";

        third($list);

        return;
    }

    $password = password_hash($data['password1'], PASSWORD_DEFAULT);
    
    $query = "insert into {{ table }} set username=?, password=?, email=?, verify='1', charclass=?, regdate=now(), onlinetime=now(), authlevel='1';";
    quick($query, 'users', [
        $data['username'],
        $password,
        $data['email1'],
        $data['charclass'],
    ], $link);

    echo gettemplate('install/fourth');
}