<?php
    include_once 'common/base.php';
    $title = 'All pages';
    include_once 'common/header.php';

    include_once 'common/classes/Pages.php';
    $pages = new Pages($db);
    $allpages = $pages->getPageList();
?>

<div class="page-header">
    <h1>All pages</h1>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Page</th>
            <th>Views</th>
        </tr>
    </thead>

    <tbody>

        <?php
            foreach ($allpages as $i) {
                echo '<tr>';
                echo sprintf('<td><a href="view.php?page=%1$s">%1$s</a></td>', $i['page_title']);
                echo '<td>' . $i['page_views'] . '</td>';
                echo '</tr>';
            }
        ?>

    </tbody>

</table>

<?php include_once 'common/footer.php'; ?>