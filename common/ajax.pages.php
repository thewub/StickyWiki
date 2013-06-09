<?php

session_start();

include_once 'config.php';
include_once 'common/classes/Pages.php';

if(!empty($_POST['action'])) {
	$pages = new Pages();

	switch($_POST['action']) {
		case 'delete':
			$pages->deletePage($_POST['pageid']);
			break;
	}

} else {
	die();
}



?>