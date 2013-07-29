<?php
    /**
     * Starts a session, and creates a database connection using PDO (PHP Data Objects)
     */

    set_include_path('..');

    // bump this every time there is a change to the database schema
    $latestdbversion = 3;

    // Set the error reporting level
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
 
    // Start a PHP session
    session_start();
 
    // Include site constants
    include_once 'config.php';
 
    // Create a database object
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        // database connection failed - go go gadget setup script
        header('Location: setup/setup.php');
        die();
    }

    $sql = "SELECT * FROM siteinfo";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $siteinfo = $stmt->fetch();

    if ( (empty($siteinfo['db_version'])) or ($siteinfo['db_version']<$latestdbversion) ) {
        // Database exists but not yet initialised/updated - go go gadget setup script
        header('Location: setup/setup.php');
        die();
    }

    // get some parameters from URL (for convenience)
    if (isset($_REQUEST['page'])) {
        $page = trim($_REQUEST['page']);
    } else {
        $page = '';
    }
    if (isset($_REQUEST['user'])) {
        $user = trim($_REQUEST['user']);
    } else {
        $user = '';
    }

    // check your privilege
    include_once 'common/classes/Users.php';
    $users = new Users($db);
    if (isset($_SESSION['LoggedIn'])) {
        $mygroups = $users->getUserGroups($_SESSION['UserID']);
    } else {
        $mygroups = [];
    }

    /**
     * Utility function to deal with terminal errors
     * @param string $message : a description of the error, html is allowed
     * @return void : it doesn't matter, this kills the wiki
     */
    function terminalError($message) {
        echo '<p class="text-error">Error: ' . $message . '</p>';
        include_once 'common/footer.php';
        die();
    }

    /**
     * Utility function to nicely format timestamps
     * @param string $timestamp : an ugly timestamp e.g. from the database
     * @return string : nicely formatted 
     */
    function formatTimestamp($timestamp) {
        return date(SW_DATETIME_FORMAT, strtotime($timestamp));
    }
?>