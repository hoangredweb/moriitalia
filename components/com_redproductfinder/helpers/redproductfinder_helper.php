<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER Helper
 */

defined('_JEXEC') or die('Restricted access');

include_once(JPATH_SITE.DS.'components'.DS.'com_redproductfinder'.DS."models".DS."redproductfinder.php");

class redproductfinder_helper
{
	function replaceProductfinder_tag($template='',$hdnFields = array(),$hide_filter=false)
	{
		global $mainframe,$context;

		$db = JFactory :: getDBO();

		$query = "SELECT * FROM #__redproductfinder_filters";
		$db->setQuery($query);
		$rs = $db->loadObjectlist();

		$template_start = "";
		$template_end = "";
		if(strstr($template,"{redproductfinderfilter_formstart}") && strstr($template,"{redproductfinderfilter_formend}"))
		{
			$tmp_template = explode( '{redproductfinderfilter_formstart}', $template );

			$template_start = $tmp_template[0];

			$tmp_template = explode( '{redproductfinderfilter_formend}', $tmp_template[1] );

			$template = $tmp_template[0];
			$template_end = $tmp_template[1];

		}

		$filter_desc = $template;

		for($i=0;$i<count($rs);$i++)
		{
			if(strstr($filter_desc,"{redproductfinderfilter:".$rs[$i]->filter_name."}"))
			{
				$replace = "";
				if($rs[$i]->published==1 && (!$hide_filter))
				{
					$tag = $mainframe->getUserStateFromRequest( $context. 'tag'.$rs[$i]->id, 'tag'.$rs[$i]->id,'' );
					if ($rs[$i]->type_select == "generic")
					{
						$generic = array();
						$generic[0]->tag_id = '0';
						$generic[0]->tag_name = trim($rs[$i]->select_name!="") ? $rs[$i]->select_name : JText::_('SELECT');
						$opt = array_merge($generic,RedproductfinderModelRedproductfinder::getTagname($rs[$i]->tag_id));

						$replace = JHTML::_('select.genericlist',$opt, "tag".$rs[$i]->id,'onChange="javascript:document.frmfinderfilter.submit();"','tag_id','tag_name',$tag);
					}
					else if ($rs[$i]->type_select == "checkbox")
					{
						$chkbox = "";
						$tags_object = RedproductfinderModelRedproductfinder::getTagname($rs[$i]->tag_id);

						foreach($tags_object as $j => $row)
						{
							$checked = "";
							if(is_array($tag))
								$checked = in_array($tags_object[$j]->tag_id,$tag) ? "checked" : "";
							$chkbox .= $tags_object[$j]->tag_name;
							$chkbox .= "<input type='checkbox' name='tag".$rs[$i]->id."[]' id='tag".$tags_object[$j]->tag_id."' value='".$tags_object[$j]->tag_id."' ".$checked." onChange='javascript:finder_checkbox(document.frmfinderfilter);document.frmfinderfilter.submit();' />";
						}
						$replace = $chkbox;
					}
					else if ($rs[$i]->type_select == "radiobutton")
					{
						$rdobox = "";
						$tags_object = RedproductfinderModelRedproductfinder::getTagname($rs[$i]->tag_id);

						foreach($tags_object as $j => $row)
						{
							$checked = in_array($tags_object[$j]->tag_id,$tag) ? "checked" : "";
							$rdobox .= $tags_object[$j]->tag_name;
							$rdobox .= "<input type='radio' name='tag".$rs[$i]->id."[]' id='tag".$tags_object[$j]->tag_id."' value='".$tags_object[$j]->tag_id."' ".$checked."  onChange='javascript:document.frmfinderfilter.submit();' />";
						}
						$replace = $rdobox;
					}
				}
				$filter_desc = str_replace("{redproductfinderfilter:".$rs[$i]->filter_name."}",$replace,$filter_desc);
			}
		}
		$hdn = "";
		foreach($hdnFields AS $name => $value)
		{
			$hdn .= "<input type='hidden' name='".$name."' value='".$value."' >";
		}
		if(!$hide_filter)
		$filter_desc = "<form name='frmfinderfilter' action='' method='post' >".$filter_desc.$hdn."</form>";
		$template = $template_start.$filter_desc.$template_end;

		return $template;
	}
}
?>