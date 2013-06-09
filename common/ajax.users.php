<?php

session_start();

include_once 'config.php';
include_once 'common/classes/Users.php';

if(!empty($_POST['action'])) {
	$users = new Users();

	switch($_POST['action']) {
		case 'block':
			$users->addUserGroup($_POST['userid'], 'blocked');
			break;
		case 'unblock':
			$users->removeUserGroup($_POST['userid'], 'blocked');
			break;
		case 'promote':
			$users->addUserGroup($_POST['userid'], 'admin');
			break;
		case 'demote':
			$users->removeUserGroup($_POST['userid'], 'admin');
			break;
	}

} else {
	die();
}



?>