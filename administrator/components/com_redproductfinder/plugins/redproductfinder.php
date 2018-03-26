<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.plugin.plugin' );

JHTML::_('behavior.tooltip');
class plgContentRedproductfinder extends JPlugin
{
	public function onContentPrepare($context, &$row, &$params, $page=0 )
	{

	/* Check if there are forms to be started or stopped */
	// CheckForms();

	/* Regex to find categorypage references */
	$regex = "#{redproductfinder}(.*?){/redproductfinder}#s";

	/* Execute the code */
	return $row->text = preg_replace_callback( $regex, 'redProductfinderPage', $row->text );
	}
}

/**
 * Create the forms
 */
function redProductfinderPage ($matches) {
	/* Load the language file as Joomla doesn't do it */
	$component = JComponentHelper::getComponent( 'com_redproductfinder' );
	$config = $component->params;
	$check_dependency = $config->get('check_dependency');

	$language = JFactory::getLanguage();
	$document = JFactory::getDocument();
	$redfinder_js = JURI::root().'components/com_redproductfinder/helpers/redproductfinder.js';
	$document->addScript($redfinder_js);
	$language->load('plg_content_redproductfinder');
	$form = getProductfinderForm($matches[1]);

	$html = '<div id="redproductfinder_'.$form->classname.'">';
		$formid= $matches[1];



	$getmyid=JRequest::getVar('myid');

	$takedropval= JRequest::getVar('hide_dropdownvalue');
	$takedropname= JRequest::getVar('hide_dropdown');

	$chktakedropval= JRequest::getVar('hide_dropdownvalue1');
	$chktakedropname= JRequest::getVar('hide_dropdown1');

	if($chktakedropval!='')
	{
		$chkmaindropval=explode(',',$chktakedropval);
		$chkmaindropname=explode(',',$chktakedropname);

	}
	if($takedropval!='')
	{
		$maindropval=explode(',',$takedropval);
		$maindropname=explode(',',$takedropname);

	}

		if ($form->showname) {
			$html .= '<div id="redproductfinder_formname">'.$form->formname.'</div>';
		}
		$html .= '<form method="post" name="adminForm" action="'.JRoute::_ ( 'index.php?option=com_redproductfinder&task=findproducts' ).'">';
		$html .= '<div id="pfheader">'.JText::_('FIND_THE_RIGHT_PRODUCT').'</div>';
		$html .= '<div id="pfsearchheader">'.JText::_('SEARCH_CRITERIA').'</div>';
		$html .= '<div class="hrdivider"></div>';

		/* Load the types */
		$types = redgetTypes($matches[1]);
		$post = JRequest::get('post');

		include_once("components".DS."com_redproductfinder".DS."helpers".DS."fields.php");
		include_once("components".DS."com_redproductfinder".DS."models".DS."redproductfinder.php");
		$j=0;
		$html .= '<div id="rep_search">';
		foreach ($types as $key => $type) {
			$type_name_css = RedproductfinderModelRedproductfinder::replace_accents($type->type_name);

			$html .= '<div class="inputfieldwrapper">';
			if($type->form_id==$formid){
			$html .= '<div class="typename '.$type_name_css.'">'.$type->type_name;
			}
			if (strlen($type->tooltip) > 0) {
				$html .= ' '.JHTML::tooltip($type->tooltip, $type->type_name, 'tooltip.png', '', '', false);
			}
			$html .= '</div>';
			$tags = redgetTypeTags($type->id);
			// Only show if the type has tags
			/*
			 if (count($tags) > 0) {
				// Create the selection boxes
				switch ($type->type_select) {
					case 'checkbox':
						foreach ($tags as $tagkey => $tag) {
							$html .= '<input type="checkbox" name="type'.$key.'[]" value="'.$tag->tag_id.'"';
							if (isset($post['type'.$key]) && in_array($tag->tag_id, $post['type'.$key])) $html .= 'checked="checked"';
							$html .= '>'.$tag->tag_name.'</input>';
						}
						break;
					case 'generic':
					default:
						array_unshift($tags, array('tag_id' => 0, 'tag_name' => JText::_('MAKE_CHOICE')));
						if (isset($post['type'.$key])) $selected = $post['type'.$key];
						else $selected = '';
						$html .= JHTML::_('select.genericlist', $tags, 'type', '', 'tag_id', 'tag_name', $selected);
						break;
				}
			}
			else unset($types[$key]);
			*/
			$html .= '<div class="typevalue '.$type_name_css.'">';

			if ($type->type_select == "generic")
			{
				$extras = "";
				$selected = JRequest::getInt('type'.$key);
				if($check_dependency!=0)
				{
					$extras = "onChange='javascript:plg_getDependent(".$j.",".$formid.");' id='finder_sel_".$type->id."'";

					if($j!=0 && $getmyid==''){
						$extras .= " disabled='disabled'";
					}else{
						$extras .= " ";
					}

				}
				if(count($types)==1 && !$check_dependency)
				{
					$extras = "onChange='javascript:document.finderForm.submit();'";
					$enable_submit = false;
				}
				if($type->form_id==$formid)
					{
						$rs = RedproductfinderModelRedproductfinder::typeTags($type->id);

						if(JRequest::getVar('hide_dropdown')!='')
						{
						$html .= redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type".$type->id, "type".$type->id, $maindropval, true,$extras);
					//	echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name","type".$type->id, "type".$type->id, $maindropval, true,$extras);
						}else{
						$html .= redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type".$type->id, "type".$type->id, $selected, true,$extras);
						//echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type".$type->id, "type".$key, 0, true,$extras);
						}
					}
				//$rs = RedproductfinderModelRedproductfinder::typeTags($type->id);
				//$html .= redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type".$key, "type".$key, $selected, true,$extras);
			}
			else if ($type->type_select == "checkbox")
			{
				$tags_object = RedproductfinderModelRedproductfinder::typeTags($type->id);
				foreach($tags_object as $i => $row)
				{
					$html .= $tags_object[$i]->tag_name;
					if($check_dependency)
					{
						$extras = "onChange='javascript:plg_getDependent(".$j.",".$formid.");'";
						if($j !=0)
							$extras .= " disabled='disabled'";
						$html .= redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "finder_sel_".$type->id, $tags_object[$i]->id,$extras)." ";
					}
					else
					$html .= redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "type", $tags_object[$i]->id)." ";
				}
			}
			elseif($type->type_select == "Productfinder datepicker")
			{

				if($type->picker==0)
				{
					$sdate=JRequest::getVar('from_startdate');
					$edate=JRequest::getVar('to_enddate');

					$html .= "From : ".JHTML::_('calendar',$sdate , 'from_startdate', 'from_startdate',$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
					$html .= " To : ".JHTML::_('calendar',$edate , 'to_enddate', 'to_enddate',$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
				}else{
					$m = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
					$currMonth = date("M");
					$currYear = date("Y");
					$fullyear = date("Y");
					$finalmnth = array();

					$getmonth=JRequest::getVar('month');

					if($getmonth!='')
					{
						$finalmnth=explode(",",$getmonth);
					}
					$html .=  "<select multiple name='month[]' size='5'>\n";
					for ($i=0; $i <= 1; $i++)
					{
						foreach ($m as $value)
						{
							$mval=$value."-".$fullyear;

							if(in_array(trim($mval),$finalmnth))
							{
								$sel="selected='selected'";

							}else{
								$sel="";
							}

							if(($value == $currMonth)&&($currYear == $fullyear))
							{
								$html .= "<option ".$sel." value='".$mval."' >$value $fullyear</option>";
							}else{
								$html .= "<option ".$sel." value='".$mval."' >$value $fullyear</option>";
							}
						}
					$fullyear++;
					};
					$html .="</select>\n";
				}

			}

			$j++;

			//$j++;
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="hrdivider '.$type_name_css.'"></div>';
		}
		$html .= '</div>';
		//if($enable_submit)
		//{
			$html .= '<div class="button">';
				$html .= '<input type="submit" value="'.JText::_('SUBMIT').'" />';
			$html .= '</div>';
		//}
		$html .= '<input type="hidden" name="formname" value="'. $formname.'" />';
		$html .= '<input type="hidden" name="Itemid" value="'.JRequest::getInt('Itemid').'" />';
		$html .= '<input type="hidden" name="option" value="com_redproductfinder" />';
		$html .= '<input type="hidden" name="task" value="findproducts" />';
		$html .= '<input type="hidden" name="returnurl" value="'.urlencode($_SERVER["REQUEST_URI"]).'" />';
		$html .= '<input type="hidden" name="controller" value="redproductfinder" />';
		$html .= '</form>';
	$html .= '</div>';
	return $html;
}

/**
 * Show all types that have been created
 */
function redgetTypes($form_id) {
	$db = JFactory::getDBO();

	/* Get all the fields based on the limits */
	$query = "SELECT t.*, f.formname AS form_name
			FROM #__redproductfinder_types t
			LEFT JOIN #__redproductfinder_forms f
			ON t.form_id = f.id
			WHERE f.id = ".$form_id."
			AND t.published = 1
			ORDER BY ordering";
	$db->setQuery($query);
	return $db->loadObjectList();
}

/**
 * Show all types that have been created
 */
function getProductfinderForm($form_id) {
	$db = JFactory::getDBO();

	/* Get all the fields based on the limits */
	$query = "SELECT *
			FROM #__redproductfinder_forms
			WHERE id = ".$form_id;
	$db->setQuery($query);
	return $db->loadObject();
}


/**
 * Get the tag names
 */
function redgetTypeTags($type_id) {
	$db = JFactory::getDBO();
	$q = "SELECT CONCAT(tag_id,'.',j.type_id) AS tag_id, tag_name
		FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
		WHERE j.tag_id = t.id
		AND j.type_id = ".$type_id."
		ORDER BY t.ordering";
	$db->setQuery($q);
	return $db->loadObjectList();
}

/**
 * Get the current page URL
 */
function redcurPageURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function redreplace_productfinderaccents($str) {
  $str = htmlentities($str, ENT_COMPAT, "UTF-8");
  $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|elig|slash|ring);/','$1',$str);
  return html_entity_decode($str);
}
?>
