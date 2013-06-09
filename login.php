<?php
    include_once 'common/base.php';
    $title = "Log in";
    include_once 'common/header.php';
?>

    <div class="page-header">
        <h1>Log in</h1>
    </div>

<?php 
if (!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])):
    echo '<p>You are already logged in.</p>';
    echo '<p><a href="logout.php">Log out</a></p>';

elseif (!empty($_POST['username'])):
    include_once "common/classes/Users.php";
    $users = new Users($db);
    
    if($users->accountLogin($_POST['username'], $_POST['password'])===TRUE):
        echo '<meta http-equiv="refresh" content="0;view.php?page=Home">';
        // TODO : allow sending back to a different page
    else:
        echo 'Login failed. <a href="login.php">Try Again?</a>';
    endif;

else: ?>

    <!-- no username posted, so show the form -->
    <form method="post" action="login.php" name="loginform" id="loginform">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" autofocus="true" />
        <label for="password">Password</label>
        <input type="password" name="password" id="password" />
        <br />
        <button class="btn btn-primary" type="submit" name="login">Log in</button>
    </form>

<?php
    endif;
    include_once 'common/footer.php';
?>
