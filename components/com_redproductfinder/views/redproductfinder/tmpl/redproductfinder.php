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
$component = JComponentHelper::getComponent( 'com_redproductfinder' );
$config = new JRegistry( $component->params );
$db = JFactory::getDBO();
$form=$config->get('form');
$query = "SELECT f.dependency,f.id FROM  #__redproductfinder_forms f
         where id='".$form."'
          ORDER BY id";
$db->setQuery($query);
$frmdependancy = $db->loadObject();
$check_dependency =$frmdependancy ->dependency;
$Itemid = JRequest::getVar('Itemid');
$formname = JRequest::getVar('formname');

?>
<form method="get" name="adminForm">
	<div id="pfheader">
		<?php
		echo JText::_('FIND_THE_RIGHT_PRODUCT');
		?>
	</div>
	<div id="pfsearchheader">
		<?php
		echo JText::_('SEARCH_CRITERIA');
		?>
	</div>
	<div class="hrdivider"></div>
	<div>
		<input type="text" name="searchkey" value="<?php echo JRequest::getVar('searchkey'); ?>" />
	</div>
	<div id='rep_search'>
	<?php
	include_once(JPATH_COMPONENT_SITE.DS."helpers".DS."fields.php");
	include_once(JPATH_COMPONENT_SITE.DS."models".DS."redproductfinder.php");
	$j=0;
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

	foreach ($this->types as $key => $type)
	{
	//echo "<pre>";print_r($type);
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
				if($check_dependency!=0)
				{
					$extras = "onChange='javascript:getDependent(".$j.");' id='finder_sel_".$type->id."'";
					$getmyid=JRequest::getVar('myid');

					if($j!=0 && $getmyid==''){
						$extras .= " disabled='disabled'";
					}else{
						$extras .= " ";
					}

				}
					if($type->form_id==$form)
					{
						$rs = RedproductfinderModelRedproductfinder::typeTags($type->id);

						if(JRequest::getVar('hide_dropdown')!='')
						{

						echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name","type".$type->id, "type".$type->id, $maindropval, true,$extras);
						}else{

						echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type".$type->id, "type".$key, 0, true,$extras);
						}
					}

			}
			else if ($type->type_select == "checkbox")
			{
				$tags_object = RedproductfinderModelRedproductfinder::typeTags($type->id);

				foreach($tags_object as $i => $row)
				{
					if($type->form_id==$form)
					{
					echo $tags_object[$i]->tag_name;
					}
					if($check_dependency!=0)
					{
						$extras = "onChange='javascript:getDependent(".$j.");'";
						$getmyid=JRequest::getVar('myid');
						if($j!=0 && $getmyid==''){
							$extras .= " disabled='disabled'";
						}else{
							$extras .= " ";
						}
						if($type->form_id==$form)
						{
							if(JRequest::getVar('hide_dropdown')!='')
							{
							echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "finder_sel_".$type->id, $tags_object[$i]->id,$extras,$maindropval)." ";
							//echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "type", $tags_object[$i]->id,"",$maindropval)." ";
							}else{
							echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "finder_sel_".$type->id, $tags_object[$i]->id,$extras)." ";
							}
						}


					}
					else{
						if($type->form_id==$form)
						{
							if(JRequest::getVar('hide_dropdown')!='')
							{
							echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "type", $tags_object[$i]->id,"",$maindropval)." ";
							}else{
							echo redPRODUCTFINDERHelperFields::generateCheckbox("type".$type->id."[]", "type", $tags_object[$i]->id)." ";
							}
						}
					}
				}
			}elseif($type->type_select == "Productfinder datepicker")
			{
				if($type->picker==0)
				{
					$sdate=JRequest::getVar('from_startdate');
					$edate=JRequest::getVar('to_enddate');

					echo "From : ".JHTML::_('calendar',$sdate , 'from_startdate', 'from_startdate',$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
					echo " To : ".JHTML::_('calendar',$edate , 'to_enddate', 'to_enddate',$format = '%d-%m-%Y',array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19'));
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
			if($type->form_id==$form)
			{
				echo (strlen($type->tooltip) > 0) ? ' '.JHTML::tooltip($type->tooltip, $type->type_name, 'tooltip.png', '', '', false) : "";
			}
			echo '</div>';
			echo '</div>';
			echo '<div class="hrdivider '.$type_name_css.'"></div>';
			if($type->form_id==$form && $type->type_select != "Productfinder datepicker")
			{
			$j++;
			}
	}
	?>
	</div>
	<div class="button">
	    <input type="submit" value="<?php echo JText::_('SEARCH'); ?>" />
	</div>

	<input type="hidden" name="formname" value="<?php echo $formname;?>" />
	<input type="hidden" name="option" value="com_redproductfinder" />
	<input type="hidden" name="task" value="findproducts" />
	<input type="hidden" name="controller" value="redproductfinder" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
</form>

