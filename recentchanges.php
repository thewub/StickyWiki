<?php
    include_once 'common/base.php';
    $title = "Recent changes";
    include_once 'common/header.php';
?>

<div class="page-header">
    <h1>Recent changes</h1>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th style="width: 12em;">Timestamp</th>
            <th>Page</th>
            <th>User</th>
            <th>Comment</th>
            <th>Tags</th>
        </tr>
    </thead>
    <tbody>

<?php
    include_once 'common/class.Special.php';
    $special = new Special($db);
    $rc = $special->getRecentChanges();
    // TODO : allow limiting/paging

    include_once 'common/class.Pages.php';
    $pages = new Pages($db);

    foreach ($rc as $row) {
        echo '<tr>';
        echo sprintf('<td><a href="viewrev.php?revid=%1$s">%2$s</a></td>', $row['rev_id'], formatTimestamp($row['rev_timestamp']) );
        echo sprintf('<td><a href="view.php?page=%1$s">%1$s</a></td>', $row['page_title']);
        echo sprintf('<td><a href="user.php?user=%1$s">%1$s</a></td>', $row['user_name']);
        echo '<td>' . $row['rev_comment']   . '</td>';
        $revtags = $pages->getRevTags($row['rev_id']);
        echo '<td>' . $pages->formatTags($revtags) . '</td>';
        echo '</tr>';
    }
?>

    </tbody>
</table>

<?php include_once 'common/footer.php'; ?>