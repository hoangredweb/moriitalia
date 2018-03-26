<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER
 */

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
jimport( 'joomla.application.module.helper' );
jimport( 'joomla.html.parameter' );
$db = JFactory::getDBO();
$module = JModuleHelper::getModule( 'redproductfinder' );
$modid = JRequest :: getVar('modid');
$component = JComponentHelper::getComponent( 'com_redproductfinder' );
$config = new JParameter( $component->params );
$form = JRequest::getInt('formname',0);



if($form==0)
$form=$config->get('form');
$query = "SELECT f.dependency,f.id FROM  #__redproductfinder_forms f
	         where id='".$form."'
	          ORDER BY id";
$db->setQuery($query);
$frmdependancy = $db->loadObject();
$check_dependency =$frmdependancy ->dependency;

if($module!='')
{
$params = new JParameter($module->params);

//$form=$params->get('form');
$component = JComponentHelper::getComponent( 'com_redproductfinder' );
$config = new JParameter( $component->params );
	if($modid!='')
	{
	$query = "SELECT f.dependency,f.id FROM  #__redproductfinder_forms as  f left outer join #__modules as m on f.id=m.showtitle
	         AND m.id='".$modid."'
	          ORDER BY f.id";

	$db->setQuery($query);
	$frmdependancy = $db->loadObject();

	$check_dependency =$frmdependancy ->dependency;
	$form=$frmdependancy->id;
	}

}

$selected_tags = JRequest :: getVar('selected_tag');
$checked_tags = JRequest :: getVar('checked_tag');
$ind = JRequest :: getVar('ind');
$mod = JRequest :: getVar('mod');


$chk_array_tags = array();
$array_tags = array();
$selectedtags = explode("`",$selected_tags);
for($i=0;$i<count($selectedtags);$i++)
{
	$tmp_tags = explode("::",$selectedtags[$i]);
	$array_tags[$i]->id = str_replace("finder_sel_","",$tmp_tags[0]);
	$array_tags[$i]->value = explode(",",$tmp_tags[1]);
}

	include_once(JPATH_COMPONENT_SITE.DS."helpers".DS."fields.php");
	include_once(JPATH_COMPONENT_SITE.DS."models".DS."redproductfinder.php");
	$dep_i=0;$dependent_typeid=0;
	$dependent_id=array();

	if($mod==1)
		echo '<div class="inputfieldwrapper">';
	foreach ($this->types as $key => $type)
	{


			$type_name_css = RedproductfinderModelRedproductfinder::replace_accents($type->type_name);

			echo '<div class="inputfieldwrapper">';
			echo '<div class="typename '.$type_name_css.'">';
			if($type->form_id==$form){
			echo $type->type_name;
			}
			echo '</div>';

			echo '<div class="typevalue '.$type_name_css.'">';
			if ($type->type_select == "generic")
			{

				$extras = "";

				if($check_dependency!='0')
				{

					if($type->form_id==$form){
					$rs = RedproductfinderModelRedproductfinder::dependent_typeTags($type->id,$dependent_id,$dependent_typeid);
					}

						if($mod==1)
						{
							$extras = "onChange='javascript:mod_getDependent(".$dep_i.",".$modid.");' id='finder_sel_".$type->id."'";
						}elseif($form!=''){
							$extras = "onChange='javascript:plg_getDependent(".$dep_i.",".$form.");' id='finder_sel_".$type->id."'";
						}else{
							$extras = "onChange='javascript:getDependent(".$dep_i.");' id='finder_sel_".$type->id."'";
						}

						if($dep_i>0)
						{
							if($dep_i > ($ind+1) || empty($rs) || $array_tags[$dep_i-1]->value[0]==""){
								$extras .= " disabled='disabled'";
							}
						}

				}
				else{


					$rs = RedproductfinderModelRedproductfinder::typeTags($type->id);

				}

				if($type->form_id==$form){
				echo redPRODUCTFINDERHelperFields::generateSelectBox_dependent($rs, "tag_name", "type".$type->id, "type".$type->id,  $array_tags[$dep_i]->value[0], true,$extras);
				}
			}
			else if ($type->type_select == "checkbox")
			{
				if($check_dependency)
					$tags_object = RedproductfinderModelRedproductfinder::dependent_typeTags($type->id,$dependent_id,$dependent_typeid);
				else
					$tags_object = RedproductfinderModelRedproductfinder::typeTags($type->id);

				foreach($tags_object as $i => $row)
				{
					if($mod==1){
						$extras = "onChange='javascript:mod_getDependent(".$dep_i.",".$modid.");'";
					}elseif($form!=''){
						$extras = "onChange='javascript:plg_getDependent(".$dep_i.",".$form.");'";
					}else{
						$extras = "onChange='javascript:getDependent(".$dep_i.");'";
					}
					if($dep_i>0)
					{
						if($dep_i > ($ind+1) || $array_tags[$dep_i-1]->value[0]=="" || $array_tags[$dep_i-1]->value[0]=="0")
							$extras .= " disabled='disabled'";
					}
					if($type->form_id==$form)
					{
						if(is_array($array_tags[$dep_i]->value)){
							$extras .= (in_array($tags_object[$i]->id,$array_tags[$dep_i]->value)) ? " 'checked' " : "";
						}


						echo $tags_object[$i]->tag_name;
						echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "finder_sel_".$type->id, $tags_object[$i]->id,$extras)." ";
					}
				}
			}
			elseif($type->type_select == "Productfinder datepicker")
			{
				if($type->picker==0)
				{
					$sdate=JRequest::getVar('from_startdate');
					$edate=JRequest::getVar('to_enddate');
					$ajax=JRequest::getVar('ajax');

					$from_startdate = $ajax ? 'from_startdate_ajax' : 'from_startdate';
					$to_enddate = $ajax ? 'to_enddate_ajax' : 'to_enddate';

					echo "From : ".JHTML::_('calendar',$sdate , $from_startdate, $from_startdate,$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
					echo " To : ".JHTML::_('calendar',$edate , $to_enddate, $to_enddate ,$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
				}else{
					$m = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
					$currMonth = date("M");
					$currYear = date("Y");
					$fullyear = date("Y");

					$getmonth=JRequest::getVar('month');
					if($getmonth!='')
					{
						$finalmnth=explode(",",$getmonth);
					}

					echo "<select multiple name='month[]' size='5'>\n";
					for ($i=0; $i <= 1; $i++)
					{
						foreach ($m as $value)
						{
							$mval=$value."-".$fullyear;
							if(in_array(trim($mval),$finalmnth))
							{
								$sel="selected='selected'";
								echo "dsdsds";
							}else{
								$sel="";
							}

							if(($value == $currMonth)&&($currYear == $fullyear))
							{
								echo "<option ".$sel." value='".$mval."' >$value $fullyear</option>";
							}else{
								echo "<option ".$sel." value='".$mval."' >$value $fullyear</option>";
							}
						}
					$fullyear++;
					};
					echo "</select>\n";
				}

			}
			if($type->form_id==$form  )
			{
			echo (strlen($type->tooltip) > 0) ? ' '.JHTML::tooltip($type->tooltip, $type->type_name, 'tooltip.png', '', '', false) : "";
			}
			echo '</div>';
			echo '</div>';
			echo '<div class="hrdivider '.$type_name_css.'"></div>';
			if($type->form_id==$form && $type->type_select != "Productfinder datepicker")
			{
				$dependent_id = $array_tags[$dep_i]->value;
				$dependent_typeid = $array_tags[$dep_i]->id;
				$dep_i++;
			}
	}
	if($mod==1)
		echo '</div>';
	exit;
	?>
