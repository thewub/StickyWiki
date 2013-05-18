<?php
/**
 * Wrapper function to parse using creole parser
 *
 * @param string $input : markup to parse
 * @return string : rendered HTML
 **/
function parse($input) {
	include_once 'parser/creole.php';
	$creole = new creole(
	    array(
	        'link_format' => 'view.php?page=%s'
	    )
	);
	return $creole->parse($input);
}
?>