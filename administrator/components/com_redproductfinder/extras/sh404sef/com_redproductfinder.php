<?php
/**
 * sh404SEF support for com_search component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_redproductfinder.php 30 2009-05-08 10:22:21Z roland $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig, $shGETVars;
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
$shLangIso = shLoadPluginLanguage( 'com_redproductfinder', $shLangIso, '_COM_SEF_REDPRODUCTFINDER');
// ------------------  load language file - adjust as needed ----------------------------------------

/* Remove some default values */
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('task');
shRemoveFromGETVarsList('limit');
shRemoveFromGETVarsList('layout');
shRemoveFromGETVarsList('view');
$db=& JFactory::getDBO ();
$mainfrmname=JRequest::getVar('formname');

/*if($mainfrmname!=''){
$query = "SELECT formname FROM #__redproductfinder_forms where id='".$mainfrmname."'";
}else{
$query = "SELECT formname FROM #__redproductfinder_forms order by id desc";
}
$db->setQuery($query);
$types = $db->loadObject();
*/
/* Set the main title of the component */
//if($types->formname!=''){
//$title[] = "Form/".$types->formname;
//}else{
	//$title[] = "form";
//$title[] = $sh_LANG[$shLangIso]['_COM_SEF_TITLE_REDPRODUCTFINDER'];
//}

if (JRequest::getVar('task') == 'findproducts') {
	$db = JFactory::getDBO();
	/* Get all the types */
	$query = "SELECT id, type_name FROM #__redproductfinder_types";
	$db->setQuery($query);
	$types = $db->loadAssocList('id');

	/* Get all the tags */
	$query = "SELECT id, tag_name FROM #__redproductfinder_tags";
	$db->setQuery($query);
	$tags = $db->loadAssocList('id');

	$monthdis=explode("-",$shGETVars['month']);
//echo "<pre>";print_r(split("-",$shGETVars['month']));
for($y=0;$y<count($monthdis);$y++)
{
	if($monthdis[$y]!='')
	{
		$mnth= strtolower(substr($monthdis[1],0,3));
		$year=strtolower(substr($monthdis[2],2,3));
		$myfinalval="/".$mnth.$year;
	}else{
		$myfinalval='';
	}
}


	$title[] = "form".$myfinalval;
	/* Add all choices to the URL */
	foreach ($shGETVars as $key => $value) {
		
		if($shGETVars->month!='')
		{
			$title[] = JFilterOutput::stringURLSafe($value);
		}
		if($shGETVars->from_startdate!='' && $shGETVars->to_enddate!='')
		{
			$title[] = JFilterOutput::stringURLSafe($value);
		}

		//if (substr($key, 0, 4) == 'type' && $value > 0) {
		if (substr($key, 0, 4) == 'type') {
			if(is_array($value))
			{
				foreach($value as $v)
				{
				//	$title[] = $types[$type]['type_name'];
					$title[] = JFilterOutput::stringURLSafe($tags[$v]['tag_name']);
				}
			}
			else
			{
				list($tag, $type) = explode('.', $value);

				/* In case the option is an array */
				if (strstr($key, '[')) {
					if (!in_array($types[$type]['type_name'], $title))
						 $title[] = $types[$type]['type_name'];
				}
				else {
					$title[] = JFilterOutput::stringURLSafe($types[$type]['type_name']);
				}
				$title[] = JFilterOutput::stringURLSafe($tags[$tag]['tag_name']);
			}
		}else if (substr($key, 0) == 'searchkey' && $value != ""){
				$title[] = JFilterOutput::stringURLSafe($value);
		}
		shRemoveFromGETVarsList($key);
	}

}
// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------
?>
