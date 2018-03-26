<?php
/** 
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 * redPRODUCTFINDER model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function RedproductfinderBuildRoute($query) {
	$segments = array();
	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if(isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 */
function RedproductfinderParseRoute($segments) {
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);

	if ($count) {
		$count--;
		$segment = array_shift($segments);
		$vars[] = $segment;
	}
	return $vars;
}
?>
