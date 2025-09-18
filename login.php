<?php
// Initialize the session.
session_start();
/* Revised :  
The welcome.php refreshes automatically every 30 secs and every time it reloads a 
new session id is generated. This makes sure the victim’s session id is changed 
every 30 secs. So the attacker only gets a 30 secs window to login as the victim 
if they can get to know the victim’s session id. This makes it impossible for the attacker to do.
*/
session_regenerate_id(true);
//$_SESSION['user_id'] = $user_id;
//$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

/* Revised :
Removed this code so that user is forced to go through login every time. The attacker will 
not be able to login because even though they have the cookie, they don't have username/password.
*/
// Check if the user is already logged in. If yes, then redirect them to the welcome page.
// $_SESSION is a superglobal variable built into PHP that contains session variables
// available to the current script. The "loggedin" session variable is true if the user
// is logged in. The built in isset function is used to determine if a variable is declared 
// and is different than NULL. It is used here to make sure that the loggedin variable is 
// set to anything at all.
//if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
//     header("location: welcome.php");
//     exit;
//}

// Include config file.
require_once "config.php";

// Define variables and initialize with empty strings.
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted.
// $_SERVER is an array containing information such as headers, paths, and script 
// locations. The entries in this array are created by the web server.
// $_SERVER, $_POST, and similarly styled variables are superglobal variables
// in PHP. Unlike normal variables that you assign yourself, PHP has some built-in 
// variables that are always available in your script and in all scopes. These are 
// called Superglobals (awesome name right?) and are predefined in PHP.
// https://teamtreehouse.com/community/why-post-is-all-with-capital-letters
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Set variables to the values entered by the user and available to the script
    // for later insertion into the SQL statement below.

    /* Revised :
    This whole section has been re-written for adding prepared statements for 
    preventing SQL injection and password hashing. The null coalescing operator (??) 
    makes sure $user and $pass are always defined. The mysqli_stmt_bind_param makes 
    sure the $user is a string when passed to the sql query in mysqli_stmt_execute.
    
    The password_verify hashes the $pass enetered by the user and compares with the 
    stored value of the hashed password.
    */
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
   
    // Create a PHP variable with a SQL Select statement.
    //$sql = "SELECT * FROM users_table WHERE username = ? and password = '$pass'";
    $sql = "SELECT * FROM users_table WHERE username = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
          // Secure password check
          // if ($pass == $row['password']) {
          if (password_verify($pass, $row['password'])) {
            echo "Start session";
            session_start();
            session_regenerate_id(true);
            $_SESSION['username'] = $row['username'];
            $_SESSION["loggedin"] = true;
    		$_SESSION["id"] = $id;
    		$_SESSION["display_username"] = $user;
            header("Location: welcome.php");
            exit;
          } else {
            $password_err = "The password you entered was not valid.";
            // echo "Invalid password.";
          }
        } else {
          $password_err = "The username you entered was not valid.";
          // echo "User not found.";
        }
        mysqli_stmt_close($stmt);
    }


    // Close connection.
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Welcome to OnePhoto!</h1>
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <!-- htmlspecialchars is a built-in PHP function to convert special characters to HTML entities.
        $_SERVER is an array built into PHP containing information such as headers, paths, and script 
        locations. The entries in this array are created by the web server. With $_SERVER["PHP_SELF"] we
        are echoing the complete path, including all parameters, to our site. PHP_SELF is a variable that 
        returns the name and path of the current file (from the root folder). However, without htmlspcialchars 
        we would have an XSS vulnerability (see https://www.webadminblog.com/index.php/2010/02/23/a-xss-vulnerability-in-almost-every-php-form-ive-ever-written/).
        Various errors related to the user-entered strings (or omissions of those) are echoed to the
        output of the form for the user to see. -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="index.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>

