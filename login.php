<?php

/**
 * This script handles logging users in and out.
 */

require 'lib/lib.php';

// Determine what our action is.
$do = isset($_GET['do']) ? $_GET['do'] : 'login';

if ($do == 'login') { login(); }
elseif ($do == 'logout') { logout(); }

/**
 * Either displays the login page, or handles the login process.
 */
function login() {
    $link = opendb();
    $error = false;
    
    if (isset($_POST)) {
        $query = doquery("SELECT id, password FROM {{table}} WHERE username='{$_POST['username']}' LIMIT 1", "users");

        if (mysql_num_rows($query) != 1) { $error = true; }

        $data = mysql_fetch_array($query);

        if (! password_verify($_POST['password'], $data['password'])) { $error = true; }

        if (! $error) {
            if (isset($_POST["rememberme"])) { $expiretime = time() + 31536000; $rememberme = 1; } else { $expiretime = 0; $rememberme = 0; }
            $cookie = "{$data['id']} {$_POST['username']} {$rememberme}";
            setcookie("dkgame", $cookie, $expiretime, "/", "", 0);
            header("Location: index.php");
        }
    }
    
    $page = gettemplate("login");
    $page = parsetemplate($page, ['error' => $error ? 'Invalid username or password. Try again.' : '']);
    display($page, 'Log in', false, false, false, false);
}

/**
 * Sets the "dkgame" cookie to a time in the past in order to delete it. Is meant
 * to log the user out.
 */
function logout() {
    setcookie("dkgame", "", time() - 100000, "/", "", 0);
    header("Location: login.php?do=login");
}