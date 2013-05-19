<?php
    include_once 'common/base.php';
    $title = $page;
    include_once 'common/header.php';

    if (empty($page)) {
        terminalError('Need to specify a page!');
    }

    include_once 'common/classes/Pages.php';
    $pages = new Pages($db);

    $revinfo = $pages->getPageInfo($page);
?>

<div class="page-header">
    <div class="page-tools">
        <?php if (!empty($revinfo['page_id'])): ?>
            <a href="edit.php?page=<?php echo $page; ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Edit page</a>
            <a href="history.php?page=<?php echo $page; ?>" class="btn"><i class="icon-th-list"></i> History</a>
            <?php if (in_array('admin', $mygroups)): ?>
                <button id="action-delete" class="btn btn-danger"><i class="icon-trash icon-white"></i></button>
            <?php endif; ?>
        <?php else: ?>
            <a href="edit.php?page=<?php echo $page; ?>" class="btn btn-success"><i class="icon-plus icon-white"></i> Create page</a>
        <?php endif; ?>
    </div>
    <h1><?php echo $page; ?></h1>
</div>

<div class="page-content">

<?php
    if (!empty($revinfo['page_id'])) {
        // page exists... so show it!
        include_once 'common/parser.php';
        echo parse($revinfo['rev_content']);
        echo '<div class="page-footer muted">';
        echo 'Last edited ' . formatTimestamp($revinfo['rev_timestamp']) . ' by ' . $revinfo['user_name'];
        echo '</div>';
    } else {
        // page doesn't exist
        echo 'This page does not exist.';
    }
?>
</div>


<?php include_once 'common/footer.php'; ?>

<script>
$('#action-delete').click(function() {
    $.post(
        'common/ajax.pages.php', 
        {
            action: 'delete',
            pageid: <?php echo $revinfo['page_id']; ?>
        },
        // success
        function(data) {
            document.location.reload(true);
        }
    );
});
</script>