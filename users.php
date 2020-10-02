<?php // users.php :: Handles user account functions.

require 'app/Libs/Helpers.php';

$link = opendb();

$controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysql_fetch_array($controlquery);

$do = isset($_GET['do']) ? $_GET['do'] : 'login';

if ($do === 'register') { register(); }
elseif ($do === 'login') { login(); }
elseif ($do === 'verify') { verify(); }
elseif ($do === 'lostpassword') { lostpassword(); }
elseif ($do === 'changepassword') { changepassword(); }
elseif ($do === 'logout') { logout(); }
else { login(); }

/**
 * Either displays the login page, or handles the login process.
 */
function login()
{
    $error = false;
    
    if (isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $query = doquery("SELECT id, password FROM {{table}} WHERE username='{$username}' LIMIT 1", "users");

        if (mysql_num_rows($query) != 1) { $error = true; }

        $data = mysql_fetch_array($query);

        if (! password_verify($password, $data['password'])) { $error = true; }

        if (! $error) {
            if (isset($_POST["rememberme"])) { $expiretime = time() + 31536000; $rememberme = 1; } else { $expiretime = 0; $rememberme = 0; }
            $cookie = "{$data['id']} {$username} {$password} {$rememberme}";
            setcookie("dkgame", $cookie, $expiretime, "/", "", 0);
            header("Location: index.php");
        }
    }
    
    $page = gettemplate("login");
    $page = parsetemplate($page, ['error' => $error ? 'Invalid username or password. Try again.' : '']);
    display($page, 'Log in', false, false, false, false);
}

function register()
{   
    $controlquery = doquery("SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
    $controlrow = mysql_fetch_array($controlquery);
    
    if (isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $emailConfirm = trim($_POST['email_confirm']);
        $password = trim($_POST['password']);
        $passwordConfirm = trim($_POST['password_confirm']);
        
        $errors = [];
        
        // Process the username
        if (empty($username)) { $errors[] = 'A username is required.'; }
        if (! preg_match('/[^A-z0-9_\-]/', $username)) { $errors[] = 'Username must be alphanumeric.'; }
        $usernameQuery = doquery("SELECT username FROM {{table}} WHERE username='{$username}' LIMIT 1", "users");
        if (mysql_num_rows($usernameQuery) != 0) { $errors[] = "{$username} is already taken. Try another one."; }
    
        // Process the email address
        if (empty($email) || empty($emailConfirm)) { $errors[] = 'Email is required.'; }
        if ($email !== $emailConfirm) { $errors[] = 'Email addresses must match.'; }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Must provide valid email address.'; }
        $emailQuery = doquery("SELECT email FROM {{table}} WHERE email='{$email}' LIMIT 1", "users");
        if (mysql_num_rows($emailQuery) != 0) { $errors[] = "Email address is already in use. Try another one."; }
        
        // Process the password
        if (empty($password) || empty($passwordConfirm)) { $errors[] = 'Passwords are required.'; }
        if ($password !== $passwordConfirm) { $errors[] = 'Passwords must match.'; }
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        if (empty($errors)) {
            
            if ($controlrow["verifyemail"] == 1) {
                $verifycode = "";
                for ($i=0; $i<8; $i++) {
                    $verifycode .= chr(rand(65,90));
                }
            } else {
                $verifycode='1';
            }
            
            $query = doquery("INSERT INTO {{table}} SET id='',regdate=NOW(),verify='$verifycode',username='$username',password='$password',email='$email',charname='$username',charclass='{$_POST['charclass']}',difficulty='{$_POST['difficulty']}'", "users") or die(mysql_error());
            
            if ($controlrow["verifyemail"] == 1) {
                if (sendregmail($email, $verifycode) == true) {
                    $page = "Your account was created successfully.<br /><br />You should receive an Account Verification email shortly. You will need the verification code contained in that email before you are allowed to log in. Once you have received the email, please visit the <a href=\"users.php?do=verify\">Verification Page</a> to enter your code and start playing.";
                } else {
                    $page = "Your account was created successfully.<br /><br />However, there was a problem sending your verification email. Please check with the game administrator to help resolve this problem.";
                }
            } else {
                $page = "Your account was created succesfully.<br /><br />You may now continue to the <a href=\"users.php?do=login\">Login Page</a> and continue playing ".$controlrow["gamename"]."!";
            }
        } else {
            $errorList = '';
            foreach ($errors as $error) {
                $errorList .= "{$error} <br />";
            }

            $page = "The following error(s) occurred when your account was being made:<br /><span style=\"color:red;\">$errorList</span><br />Please go back and try again.";
        }
    } else {
        $page = gettemplate("register");
    }

    if ($controlrow["verifyemail"] == 1) { 
        $controlrow["verifytext"] = "<br /><span class=\"small\">A verification code will be sent to the address above, and you will not be able to log in without first entering the code. Please be sure to enter your correct email address.</span>";
    } else {
        $controlrow["verifytext"] = "";
    }

    $page = parsetemplate($page, $controlrow);
    
    $topnav = "<a href=\"users.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Register", false, false, false);
}

function verify()
{
    if (isset($_POST["submit"])) {
        extract($_POST);
        $userquery = doquery("SELECT username,email,verify FROM {{table}} WHERE username='$username' LIMIT 1","users");
        if (mysql_num_rows($userquery) != 1) { die("No account with that username."); }
        $userrow = mysql_fetch_array($userquery);
        if ($userrow["verify"] == 1) { die("Your account is already verified."); }
        if ($userrow["email"] != $email) { die("Incorrect email address."); }
        if ($userrow["verify"] != $verify) { die("Incorrect verification code."); }
        // If we've made it this far, should be safe to update their account.
        $updatequery = doquery("UPDATE {{table}} SET verify='1' WHERE username='$username' LIMIT 1","users");
        display("Your account was verified successfully.<br /><br />You may now continue to the <a href=\"users.php?do=login\">Login Page</a> and start playing the game.<br /><br />Thanks for playing!","Verify Email",false,false,false);
    }
    $page = gettemplate("verify");
    $topnav = "<a href=\"users.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Verify Email", false, false, false);
}

function lostpassword()
{    
    if (isset($_POST["submit"])) {
        extract($_POST);
        $userquery = doquery("SELECT email FROM {{table}} WHERE email='$email' LIMIT 1","users");
        if (mysql_num_rows($userquery) != 1) { die("No account with that email address."); }
        $newpass = "";
        for ($i=0; $i<8; $i++) {
            $newpass .= chr(rand(65,90));
        }
        $md5newpass = md5($newpass);
        $updatequery = doquery("UPDATE {{table}} SET password='$md5newpass' WHERE email='$email' LIMIT 1","users");
        if (sendpassemail($email,$newpass) == true) {
            display("Your new password was emailed to the address you provided.<br /><br />Once you receive it, you may <a href=\"users.php?do=login\">Log In</a> and continue playing.<br /><br />Thank you.","Lost Password",false,false,false);
        } else {
            display("There was an error sending your new password.<br /><br />Please check with the game administrator for more information.<br /><br />We apologize for the inconvience.","Lost Password",false,false,false);
        }
        die();
    }
    $page = gettemplate("lostpassword");
    $topnav = "<a href=\"users.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Lost Password", false, false, false);
}

function changepassword()
{
    if (isset($_POST["submit"])) {
        extract($_POST);
        $userquery = doquery("SELECT * FROM {{table}} WHERE username='$username' LIMIT 1","users");
        if (mysql_num_rows($userquery) != 1) { die("No account with that username."); }
        $userrow = mysql_fetch_array($userquery);
        if ($userrow["password"] != md5($oldpass)) { die("The old password you provided was incorrect."); }
        if (preg_match("/[^A-z0-9_\-]/", $newpass1)==1) { die("New password must be alphanumeric."); } // Thanks to "Carlos Pires" from php.net!
        if ($newpass1 != $newpass2) { die("New passwords don't match."); }
        $realnewpass = md5($newpass1);
        $updatequery = doquery("UPDATE {{table}} SET password='$realnewpass' WHERE username='$username' LIMIT 1","users");
        if (isset($_COOKIE["dkgame"])) { setcookie("dkgame", "", time()-100000, "/", "", 0); }
        display("Your password was changed successfully.<br /><br />You have been logged out of the game to avoid cookie errors.<br /><br />Please <a href=\"users.php?do=login\">log back in</a> to continue playing.","Change Password",false,false,false);
        die();
    }
    $page = gettemplate("changepassword");
    $topnav = "<a href=\"users.php?do=login\"><img src=\"images/button_login.gif\" alt=\"Log In\" border=\"0\" /></a><a href=\"users.php?do=register\"><img src=\"images/button_register.gif\" alt=\"Register\" border=\"0\" /></a><a href=\"help.php\"><img src=\"images/button_help.gif\" alt=\"Help\" border=\"0\" /></a>";
    display($page, "Change Password", false, false, false); 
}

function sendpassemail($emailaddress, $password)
{
    global $controlrow;
    
$email = <<<END
You or someone using your email address submitted a Lost Password application on the {$controlrow['gamename']} server, located at {$controlrow['gameurl']}. 

We have issued you a new password so you can log back into the game.

Your new password is: $password

Thanks for playing.
END;

    $status = mymail($emailaddress, "{$controlrow['gamename']} Lost Password", $email);
    return $status;
}

function sendregmail($emailaddress, $vercode)
{
    global $controlrow;

    $verurl = "{$controlrow['gameurl']}?do=verify";
    
$email = <<<END
You or someone using your email address recently signed up for an account on the {$controlrow['gamename']} server, located at {$controlrow['gameurl']}.

This email is sent to verify your registration email. In order to begin using your account, you must verify your email address. 
Please visit the Verification Page ({$verurl}) and enter the code below to activate your account.
Verification code: {$vercode}

If you were not the person who signed up for the game, please disregard this message. You will not be emailed again.
END;

    $status = mymail($emailaddress, "{$controlrow['gamename']} Account Verification", $email);
    return $status;
}

function mymail($to, $title, $body, $from = '')
{
    global $controlrow;
    
    $from = trim($from);

    if (!$from) {
        $from = '<'.$controlrow["adminemail"].'>';
    }

    $rp     = $controlrow["adminemail"];
    $org    = $controlrow['gameurl'];
    $mailer = 'PHP';

    $head   = '';
    $head  .= "Content-Type: text/plain \r\n";
    $head  .= "Date: ". date('r'). " \r\n";
    $head  .= "Return-Path: $rp \r\n";
    $head  .= "From: $from \r\n";
    $head  .= "Sender: $from \r\n";
    $head  .= "Reply-To: $from \r\n";
    $head  .= "Organization: $org \r\n";
    $head  .= "X-Sender: $from \r\n";
    $head  .= "X-Priority: 3 \r\n";
    $head  .= "X-Mailer: $mailer \r\n";

    $body  = str_replace("\r\n", "\n", $body);
    $body  = str_replace("\n", "\r\n", $body);

    return mail($to, $title, $body, $head);
}

/**
 * Sets the "dkgame" cookie to a time in the past in order to delete it. Is meant
 * to log the user out.
 */
function logout()
{
    setcookie("dkgame", "", time() - 100000, "/", "", 0);
    header("Location: users.php?do=login");
}