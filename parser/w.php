<?php

/*
 * w - A Wiki Software
 *
 * Copyright (c) 2009, 2010 Ivan Fomichev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 */

require_once('./creole.php');

define('MAIN_PAGE', 'Main Page');
define('MAIN_PAGE_DEFAULT_CONTENT', 'Welcome to **codeholic**\'s wiki.');
define('PAGE_NOT_FOUND', 'This page is not started yet.');
define('URL_FORMAT', '/w.php?id=%s');

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc())
{
    function stripslashes_array($array) {
        return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
    }

    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

function format_link($link) {
    return sprintf(URL_FORMAT, rawurlencode($link));
}

$dbh = sqlite_open('w.db', 0666);
@sqlite_exec($dbh, 'CREATE TABLE w (id TEXT PRIMARY KEY, content TEXT)');
sqlite_exec($dbh, 'INSERT OR IGNORE INTO w (id, content) VALUES' .
                  '(\'' . sqlite_escape_string(MAIN_PAGE) . '\',\'' .
                          sqlite_escape_string(MAIN_PAGE_DEFAULT_CONTENT) . '\')');

$id = isset($_GET['id']) ? $_GET['id'] : MAIN_PAGE;

session_start();

if (isset($_POST['content'])) {
    if (empty($_POST['email'])) {
        $content = $_POST['content'];
        sqlite_exec($dbh, 'INSERT OR REPLACE INTO w (id, content) VALUES' .
                          '(\'' . sqlite_escape_string($id) . '\',\'' .
                                  sqlite_escape_string($content) . '\')');
    }
    else {
        $error = 'You are probably a bot.';
    }
}

if (!isset($content)) {
    $res = sqlite_query($dbh, 'SELECT content FROM w ' .
                              'WHERE id = \'' . sqlite_escape_string($id) . '\'');
    if ($row = sqlite_fetch_array($res)) {
        $content = $row['content'];
    }
}

$creole = new creole(
    array(
        'link_format' => URL_FORMAT,
        'interwiki' => array(
            'WikiCreole' => 'http://www.wikicreole.org/wiki/%s',
            'Wikipedia' => 'http://en.wikipedia.org/wiki/%s'
        )
    )
);

echo('<?xml version="1.0" encoding="UTF-8"?>');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title><?php echo(htmlspecialchars($id)); ?></title>
<style type="text/css">
.error { color: red; }
</style>
</head>
<body>
<h1><?php echo(htmlspecialchars($id)); ?></h1>

<?php echo($creole->parse(isset($content) ? $content : PAGE_NOT_FOUND)); ?>

<hr/>

<p>You can edit this page by submitting the form below.</p>

<?php if (isset($error)) { ?>
<p class="error"><?php echo($error); ?></p>
<?php } ?>


<form action="<?php echo(format_link($id)); ?>" method="post">
<div style="display: none;"><input name="email"/></div>
<div>
<textarea name="content" style="width: 100%;" cols="80" rows="10"><?php
    if (isset($_POST['content'])) { echo(htmlspecialchars($_POST['content'])); }
    else if (isset($content)) { echo(htmlspecialchars($content)); }
?></textarea>
</div>
<div><input type="submit"/></div>
</form>

<h2>See also</h2>

<?php

$res = sqlite_query($dbh, 'SELECT id FROM w ORDER BY id');
while (($row = sqlite_fetch_array($res)) !== false) {

?>
<div><a href="<?php echo(format_link($row['id'])); ?>"><?php echo(htmlspecialchars($row['id'])); ?></a></div>
<?php

}

?>

</body>
</html>
