<?php
    include_once 'common/base.php';
    $title = "History: " . $page;
    include_once 'common/header.php';

    if (empty($page)) {
        terminalError('Need to specify a page!');
    }
?>

<div class="page-header">
    <div class="page-tools">
        <a href="view.php?page=<?php echo $page; ?>" class="btn"><i class="icon-file"></i> Back to page</a>
    </div>
    <h1>History: <?php echo $page; ?></h1>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th style="width: 12em;">Timestamp</th>
            <th>User</th>
            <th>Comment</th>
            <th>Tags</th>
        </tr>
    </thead>
    <tbody>

<?php
    include_once 'common/class.Pages.php';
    $pages = new Pages($db);
    $hist = $pages->getPageHistory($page);
    // TODO : allow limiting/paging

    foreach ($hist as $row) {
        echo '<tr>';
        echo sprintf('<td><a href="viewrev.php?revid=%1$s">%2$s</a></td>', $row['rev_id'], formatTimestamp($row['rev_timestamp']) );
        echo sprintf('<td><a href="user.php?user=%1$s">%1$s</a></td>', $row['user_name']);
        echo '<td>' . $row['rev_comment'] . '</td>';
        $revtags = $pages->getRevTags($row['rev_id']);
        echo '<td>' . $pages->formatTags($revtags) . '</td>';
        echo '</tr>';
    }
?>
    </tbody>
</table>

<?php include_once 'common/footer.php'; ?>