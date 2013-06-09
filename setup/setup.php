<?php

    /**
     * Setup database
     */

    set_include_path('..');

    // bump this every time there is a change to the database schema
    $latestdbversion = 2;

    // Set the error reporting level
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
 
    // Start a PHP session
    session_start();
 
    // Include site constants
    include_once 'config.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            StickyWiki setup
        </title>
        <!-- Bootstrap -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="icon" href="../favicon.ico" type="image/x-icon" />
    </head>

    <body>

        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="#">StickyWiki setup</a>
                </div>
            </div>
        </div>

        <div class="container">

<?php
    // Create a database object
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo '<p>Database connection failed: ' . $e->getMessage() . '</p>';
        echo '<p>Please check the database credentials in /config.php</p>';
        exit;
    }

    $sql = "SELECT * FROM siteinfo";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $siteinfo = $stmt->fetch();

    // if they have submitted the account creation form...
    if(!empty($_POST['username'])) {
        if ($_POST['password']==$_POST['password2']) {
            $u = $_POST['username'];
            $p = $_POST['password'];
            include_once 'common/classes/Users.php';
            $users = new Users();
            if ( $users->adminExists() ) {
                echo 'An admin account already exists!';
                die();
            }
            $users->createAdminAccount($u, $p);
            echo '<meta http-equiv="refresh" content="0;../view.php?page=Home">';
            exit;
        } else {
            echo 'Passwords do not match!';
            exit;
        }
    }

    if ($siteinfo['db_version'] == $latestdbversion) {
        echo '<p>Database (' . DB_NAME . ') already exists and is up to date! You can <a href="../view.php?page=Home">use the wiki</a>.</p>';
        exit;
    }

    if (empty($siteinfo['db_version'])) {
        echo 'Initialising database ' . DB_NAME . '... ';
        $sql = file_get_contents('initialise db v1.sql');
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
        echo 'done';
        // need to create an admin account next
?>
            <form method="post" action="setup.php">
                <legend>Create admin account</legend>
                <label>Username</label>
                <input type="text" size="10" name="username" autofocus="true"/>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" />
                <label for="password2">Re-type password</label>
                <input type="password" name="password2" id="password2" />    
                <br/>
                <button class="btn" type="submit">Create account</button>
            </form>
<?php
    } else if ($siteinfo < $latestdbversion) {
        echo '<p>Database exists but is not up to date.</p>';
    }
?>

        </div>
    </body>
</html>