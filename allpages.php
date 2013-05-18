<?php
    include_once 'common/base.php';
    $title = 'All pages';
    include_once 'common/header.php';

    include_once 'common/class.Pages.php';
    $pages = new Pages($db);
    $allpages = $pages->getPageList();
?>

<div class="page-header">
    <h1>All pages</h1>
</div>

<ul>
	<?php
		foreach ($allpages as $i) {
        	echo sprintf('<li><a href="view.php?page=%1$s">%1$s</a></li>', $i['page_title']);
    	}
    ?>
</ul>

<?php include_once 'common/footer.php'; ?>