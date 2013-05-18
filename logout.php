<?php
 
    session_start();
 
    unset($_SESSION['LoggedIn']);
    unset($_SESSION['Username']);
    unset($_SESSION['UserID']);
 
?>
 
<meta http-equiv="refresh" content="0;view.php?page=Home">