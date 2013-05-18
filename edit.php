<?php
    include_once 'common/base.php';
    $title = "Editing: " . $page;
    include_once 'common/header.php';

    if (empty($page)) {
        terminalError('Need to specify a page!');
    }
    if (!isset($_SESSION['LoggedIn'])) {
        terminalError('Need to be logged in to edit pages!');
    }
    if (in_array('blocked', $mygroups)) {
        terminalError('Sorry, you have been blocked from editing.');
    }
?>

<div class="page-header">
    <h1>Editing: <?php echo $page; ?></h1>
</div>

<?php
    include_once 'common/class.Pages.php';
    $pages = new Pages($db);
    $revinfo = $pages->getPageInfo($page); // if editing we need the page_content, if saving we need page_id
    $newcontent = $revinfo['rev_content'];
    $comment = '';

    if(isset($_POST['save'])) {
        // save, then go back to view
        $pages->saveEdit($revinfo['page_id'], $page, trim($_POST['edit-area']), trim($_POST['edit-comment']));
        echo '<meta http-equiv="refresh" content="0;view.php?page=' . $page . '">';
    } else {

        if(isset($_POST['preview'])) {
            // preview
            $newcontent = trim($_POST['edit-area']); // update so changes are shown in edit area when refreshed
            $comment = trim($_POST['edit-comment']);
            include_once 'common/parser.php';
            echo parse($newcontent);
            echo '<hr/>';
        }

    // just show the form below
?>

<form method="post" action="edit.php?page=<?php echo $page; ?>" name="editform" id="editform">
    <textarea id="edit-area" name="edit-area" rows="10" autofocus="true" spellcheck="true"><?php echo $newcontent; ?></textarea>
    <label for="edit-comment-input">Comment</label>
    <input id="edit-comment-input" name="edit-comment" type="text" placeholder="Summarise your changes here" value="<?php echo $comment; ?>" />
    <div class="edit-buttons">
        <button class="btn btn-primary" name="save" type="submit"><i class="icon-ok icon-white"></i> Save</button>
        <button class="btn" name="preview" type="submit"><i class="icon-search"></i> Preview</button>
        <a class="btn btn-danger" href="view.php?page=<?php echo $page; ?>"><i class="icon-remove icon-white"></i> Cancel</a>
    </div>
</form>

<?php 
    }
    include_once 'common/footer.php';
?>