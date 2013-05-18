<?php
    include_once 'common/base.php';
    $title = 'My settings';
    include_once 'common/header.php';

    if (!isset($_SESSION['LoggedIn'])) {
        terminalError('Need to be logged in first!');
    }
?>

<div class="page-header">
    <h1>User settings</h1>
</div>

<!-- submitted a new password? do the magic! -->
<?php
    if(!empty($_POST['newpassword'])) {
        if ($_POST['newpassword']==$_POST['newpassword2']) {
            include_once "common/class.Users.php";
            $users = new Users($db);
            echo $users->changePassword($_SESSION['Username'], $_POST['currentpassword'], $_POST['newpassword']);
        } else {
            terminalError('Passwords do not match!');
        }


    } else {
?>

<!-- else show the form -->

<h2>Change password</h2>
<form method="post" action="mysettings.php">
    <label for="currentpassword">Current password</label>
    <input type="password" name="currentpassword" id="currentpassword" />
    <label for="password">New password</label>
    <input type="password" name="newpassword" id="newpassword" />
    <label for="password2">Re-type password</label>
    <input type="password" name="newpassword2" id="newpassword2" />    
    <br/>
    <button class="btn" type="submit">Change password</button>
</form>

<?php 
    }
    include_once 'common/footer.php';
?>