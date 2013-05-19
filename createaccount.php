<?php
    include_once 'common/base.php';
    $title = "Create account";
    include_once 'common/header.php';
?>

<div class="page-header">
    <h1>Create account</h1>
</div>

<!-- submitted a username? do the magic! -->
<?php
    if(!empty($_POST['username'])) {
        if ($_POST['password']==$_POST['password2']) {
            include_once "common/classes/Users.php";
            $users = new Users($db);
            echo $users->createAccount($_POST['username'], $_POST['password']);
        } else {
            terminalError('Passwords do not match!');
        }


    } else {
?>

<!-- else show the form -->

<form method="post" action="createaccount.php">
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
    }
    include_once 'common/footer.php';
?>