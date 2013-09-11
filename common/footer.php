    </div><!-- end of container -->

    <div id="footer">
<?php if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1): ?>
            Logged in as <strong><a href="user.php?user=<?php echo $_SESSION['Username']; ?>"><?php echo $_SESSION['Username']; ?></a></strong> 
            <?php echo $users->formatGroups($mygroups); ?><br />
	        <a href="mysettings.php">My settings</a> &middot; <a href="logout.php">Log out</a>
<?php else: ?>
	        <a href="login.php">Log in</a> &middot; <a href="createaccount.php">Create account</a>
<?php endif; ?>
	</div>

    <!-- Javascript - Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>