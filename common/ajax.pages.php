<?php

session_start();

include_once 'constants.php';
include_once 'class.Pages.php';

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