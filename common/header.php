<!DOCTYPE html>
<html>
    <head>
        <title>
<?php
if(!empty($title)) {
    echo $title;
} else {
    echo 'StickyWiki';
}
?>
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
        <!-- custom css -->
        <link href="main.css" rel="stylesheet">
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <script src="common/jquery-1.10.1.min.js"></script>
    </head>

    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="view.php?page=Home">StickyWiki</a>
                    <div class="nav-collapse collapse">

                        <form method="get" action="view.php" class="navbar-form pull-right">
                            <input type="text" name="page" placeholder="search" class="span4">
                        </form>

                        <ul class="nav">
                            <li><a href="recentchanges.php">recent changes</a></li>
                            <li><a href="allpages.php">all pages</a></li>
                            <li><a href="allusers.php">all users</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

        <script>
            // highlight the current page
            $(document).ready(function() {
                var currentpage = window.location.pathname.split('/')[2];
                $('.nav li a[href$="' + currentpage + '"]').parent().addClass("active");
            })
        </script>

        <div class="container main-container">