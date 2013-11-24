<?php
    include_once 'common/base.php';
    $title = 'Viewing old revision';
    include_once 'common/header.php';

    if (empty($_REQUEST['revid'])) {
        terminalError('Need to specify a revision!');
    }
    $revid = trim($_REQUEST['revid']);

    include_once 'common/classes/Pages.php';
    $pages = new Pages($db);

    $revinfo = $pages->getRevInfo($revid);
?>

<div class="page-header">
    <div class="page-tools">
        <a href="view.php?page=<?php echo $revinfo['page_title']; ?>"><i class="icon-arrow-up"></i> View current revision</a>
    </div>
    <h1><?php echo $revinfo['page_title']; ?> <small>as of <?php echo formatTimestamp($revinfo['rev_timestamp']); ?></small></h1>
</div>

<div class="page-content">

<?php
    include_once 'common/parser.php';
    echo parse($revinfo['rev_content']);
    echo '<div class="page-footer muted">';
    echo '</div>';
?>
</div>

<?php include_once 'common/footer.php'; ?>