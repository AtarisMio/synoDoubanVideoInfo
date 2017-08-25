#!/usr/bin/php
<?php

require_once(dirname(__FILE__) . '/../search.inc.php');

function Process($input, $lang, $type, $limit, $search_properties, $allowguess, $id)
{
	$RET = array();
	if( 'chs' == $lang ){
		require_once(dirname(__FILE__) . '/doubanSearch.php');
		$RET = ProcessDouban($input, $lang, $type, $limit, $search_properties, $allowguess, $id);
	}else{
		require_once(dirname(__FILE__) . '/originSearch.php');
		$RET = ProcessOrigin($input, $lang, $type, $limit, $search_properties, $allowguess, $id);
	}
	return $RET;
}

PluginRun('Process');
?>
