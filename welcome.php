<?php
// Initialize the session.
session_start();
/* Revised :  
This is to prevent Session hijacking where the attacker after logging in as themself 
changes the cookie to the victim's cookie value, and reloads welcome.php page.
The welcome.php refreshes automatically every 30 secs and every time it reloads a 
new session id is generated with session_regenerate_id. This makes sure the victim’s 
session id is changed every 30 secs. So the attacker only gets a 30 secs window to login as the victim 
if they can get to know the victim’s session id. This makes it impossible for the attacker to do. 
*/
session_regenerate_id(true);

// Check if the user is logged in. If not, then redirect them to the login page.
// $_SESSION is a superglobal variable built into PHP that contains session variables
// available to the current script. The "loggedin" session variable is true if the user
// is logged in. The built in isset function is used to determine if a variable is declared 
// and is different than NULL. It is used here to make sure that the loggedin variable is 
// set to anything at all.
// if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
//    header("location: login.php");
//    exit;
//}
/* Revised:
This code has been changed to allow only users who have logged in to access this page.
Otherwise the session is destroyed and the user is asked to re-login.
While this is more aggresive than using loggedin, it's not necessary. Using loggedin is also fine. 
I had to added this code while trying out options for preventing Session hijacking, and decided 
to keep it.
*/
if (!isset($_SESSION['username'])) {
    // Destroy session and redirect to login
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Echo the username.
echo "You are logged in as user: " . $_SESSION["display_username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<!-- https://stackoverflow.com/questions/32913226/auto-refresh-page-every-30-seconds -->
	<meta http-equiv="refresh" content="30"/>
	<p></p><a href="logout.php" class="btn btn-danger">Log Out</a></p>
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>OnePhoto</h1>
    </div>
    <p>
	<p><img src="A_Mystery_Location.jpg" alt="A Mystery Image" height="233" width="416"></p>
	<p>A mystery location photo.</p>
</body>
</html>
