    </div><!-- end of container -->

    <div id="footer">
        <hr/>
    	<ul>
<?php if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1): ?>
	        <li>
                Logged in as <strong><a href="user.php?user=<?php echo $_SESSION['Username']; ?>"><?php echo $_SESSION['Username']; ?></a></strong> 
                <?php echo $users->formatGroups($mygroups); ?>
            </li>
	        <li><a href="logout.php">Log out</a></li>
<?php else: ?>
	        <li><a href="login.php">Log in</a></li>
	        <li><a href="createaccount.php">Create account</a></li>
<?php endif; ?>
    	</ul>
	</div>

    <!-- Javascript - Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>