<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');

$isredshop = JComponentHelper::isEnabled('com_redshop');
$Itemid = JRequest::getVar('Itemid');
$formname = JRequest::getVar('formname');
$db = JFactory::getDBO();
if ($isredshop == 1)
{
	// get modal behavior
	JHTML::_('behavior.modal');

	// set path to redshop
	$redshop_site_path = JPATH_SITE . DS . 'components' . DS . 'com_redshop';
	$redshop_admin_path = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_redshop';

	// set redshop site helper
	require_once( $redshop_site_path . DS . 'helpers' . DS . 'product.php' );
	$redshophelper = new producthelper();

	// Getting the redshop configuration
	if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php')){
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php');
	}

    require_once($redshop_admin_path . DS . 'helpers' . DS . 'template.php');
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'configuration.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'stockroom.php' );
}
$Redconfiguration = new Redconfiguration();
$Redconfiguration->defineDynamicVars();
$redstockroom = new rsstockroomhelper();
$uri =& JURI::getInstance();
$url= $uri->root();
$app = JFactory::getApplication($url);
$config = $app->getParams('com_redproductfinder');

$show_price = $config->get('show_price');
$display_tags=$config->get('search_tag_display');
$show_addtocart = $config->get('show_add_to_cart');
$redproductfinder_template= $config->get('prod_template', 260);
$searchkey = JRequest::getVar('searchkey','');

$post = JRequest::get('request');

if($post['month']!='')
{

$month='&month='.implode(",",$post['month']).'';
}elseif($post['from_startdate']!='' && $post['to_enddate']){
$from_startdate='&from_startdate='.$post['from_startdate'].'';
$to_enddate='&to_enddate='.$post['to_enddate'].'';
}elseif($post['from_startdate_ajax']!='' && $post['to_enddate_ajax']){
$from_startdate='&from_startdate='.$post['from_startdate_ajax'].'';
$to_enddate='&to_enddate='.$post['to_enddate_ajax'].'';	
}

$sel_getitemid="select * from #__modules where id=".$_POST['mod_id_main'];
$db->setQuery($sel_getitemid);
$getitemid = $db->loadObjectlist();
$productfinder_param_main = new JParameter( $getitemid[0]->params );
$main_itemid = $productfinder_param_main->get('itemid','');
if($main_itemid!="")
{
	$Itemid=$main_itemid;
}

/* Create a link to get back to this result */
$geturl = "index.php?option=com_redproductfinder&myid=1&task=findproducts".$month."".$from_startdate."".$to_enddate."&view=redproductfinder&layout=redproductfinder&formname=".$formname."&Itemid=".$Itemid;
$appendFlag = false;
foreach ($post as $key => $value) {

	if (stripos($key, 'type') !== false)
	{
		$appendFlag = true;
		if (is_array($value)) {
			foreach ($value as $vkey => $vvalue)
			{
				$geturl .= "&".$key."[]=".$vvalue;
				//$geturl .= "&type".$vkey."=".$vvalue;
				?>
				<!-- <input type="hidden" name="<?php echo $vkey; ?>" value="<?php echo $vvalue; ?>" />-->
				 <input type="hidden" name="<?php echo $key; ?>[]" value="<?php echo $vvalue; ?>" />
				<?php
			}
		}
		else
		{
			$geturl .= "&".$key."=".$value;
			?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
			<?php
		}
	}
	if (stripos($key, 'searchkey') !== false)
	{
		$appendFlag = true;
		if (is_array($value)) {
			foreach ($value as $vkey => $vvalue)
			{
				$geturl .= "&".$key."[]=".$vvalue;
				?>
				<input type="hidden" name="<?php echo $key; ?>[]" value="<?php echo $vvalue; ?>" />
				<?php
			}
		}
		else
		{
			$geturl .= "&".$key."=".$value;
			?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
			<?php
		}
	}
}

if(JRequest::getVar('redirectFlag',0)!=1 && $appendFlag){
	global $mainframe;
	$mainframe->redirect($geturl.'&redirectFlag=1');
}
echo "<div class=\"redproductfinder_result_header\">".JText::_('SEARCH_RESULTS')."</div>";

/*if (array_key_exists('products', $this->searchresult))
{
	foreach ($this->searchresult['scores'] as $assoc_id => $score)
	{
		if (array_key_exists($assoc_id, $this->searchresult['products']))
		{
			$redshop_product_id[] = $this->searchresult['products'][$assoc_id]->product_id;
			$product_name[] = $this->searchresult['products'][$assoc_id]->product_name;
			$product_s_desc[] = $this->searchresult['products'][$assoc_id]->product_s_desc;
			$product_desc[] = $this->searchresult['products'][$assoc_id]->product_desc;
			$expired[] = $this->searchresult['products'][$assoc_id]->expired;
		}
	}
}
else */
if (count($this->searchresult)>0 && !(array_key_exists('products', $this->searchresult)))
{
	 for ($i=0;$i<count($this->searchresult);$i++)
	 {

		$redshop_product_id[] = $this->searchresult[$i]->product_id;
		$product_name[] = $this->searchresult[$i]->product_name;
		$product_s_desc[] = $this->searchresult[$i]->product_s_desc;
		$product_desc[] = $this->searchresult[$i]->product_desc;
		$expired[] = $this->searchresult[$i]->expired;
	 }
}
else
{
	?>
	<div class="productfinder_product">
		<?php echo JText::_('NO_PRODUCTS_FOUND'); ?>
		<div style="clear:left;" ><hr /></div>
	</div>
	<?php
}

if (isset($redshop_product_id))
{
	if($display_tags==1)
	{
		/* search tag display code start */
		$query = "SELECT id, type_name FROM #__redproductfinder_types";
		$db->setQuery($query);
		$types = $db->loadAssocList('id');

		$query = "SELECT id, tag_name FROM #__redproductfinder_tags";
		$db->setQuery($query);
		$tags = $db->loadAssocList('id');

		/* Get all the tags */
		$query = "SELECT type_id FROM #__redproductfinder_tag_type";
		$db->setQuery($query);
		$type_id = $db->loadAssocList('type_id');
		$y=0;
		$serarch_disp = "";
		foreach ($post as $key => $value)
		{
			if (substr($key, 0, 4) == 'type')
			{
				if(is_array($value))
				{
					$maintag='';
					foreach($value as $v)
					{
						$query = "SELECT t.*,type.*,ts.* FROM #__redproductfinder_tags as t left outer join #__redproductfinder_tag_type as type on t.id=type.tag_id left outer join #__redproductfinder_types as ts on type.type_id=ts.id where t.tag_name='".$tags[$v]['tag_name']."' group by type.type_id ";
						$db->setQuery($query);
						$type_id = $db->loadObject();

							if($y!=$type_id->id)
							{
								$serarch_disp .=$type_id->type_name.": ";
								$y++;
							}
							$maintag .=$tags[$v]['tag_name'].",";
					}
					$serarch_disp .= substr_replace($maintag," ",-1);
					$serarch_disp .= "<br/>";
				}
				else
				{
					list($tag) = explode('.', $value);
						$query = "SELECT t.*,type.*,ts.* FROM #__redproductfinder_tags as t left outer join #__redproductfinder_tag_type as type on t.id=type.tag_id left outer join #__redproductfinder_types as ts on type.type_id=ts.id where t.id='".$tag."' group by type.type_id ";
						$db->setQuery($query);
						$type_id = $db->loadObject();
						if($y<$tag)
						{
							$serarch_disp .= $type_id->type_name.": ".$type_id->tag_name."<br/>";
							$y++;
						}
				}
			}else if (substr($key, 0) == 'searchkey' && $value!= ""){
					$serarch_disp .= "Searchkey: ".$value."<br/>";
			}
		}
	/* search tag display code end */
	}
	if($redproductfinder_template)
	{

		$redTemplate = new Redtemplate();
		$redstockroom = new rsstockroomhelper();
		$template_array = $redTemplate->getTemplate("redproductfinder",$redproductfinder_template);
		$template_desc = $template_array[0]->template_desc;
		include_once JPATH_COMPONENT_SITE.DS."views".DS."redproductfinder".DS."tmpl".DS."searchresult_template.php";

	}
	else
	{


		//display search result tag start

			echo $serarch_disp;
		//display search result tag end
		for ($j=0; $j<count($redshop_product_id); $j++)
		{
			?>
			<div class="productfinder_product">
				<?php $link = JRoute::_( 'index.php?option=com_redshop&view=product&pid='. $redshop_product_id[$j]); ?>
				<div class="productfinder_productimage">
					<?php
					//Image
					echo $redshophelper->getProductImage($redshop_product_id[$j],$link,CATEGORY_PRODUCT_THUMB_WIDTH,CATEGORY_PRODUCT_THUMB_HEIGHT,2,1);
					?>
				</div>
				<div class="productfinder_titledescription">
					<a href="<?php echo $link;?>" alt="<?php echo $product_name[$j]; ?>" title="<?php echo $product_name[$j]; ?>"><h2 class="productfinder_title"><?php echo $product_name[$j]; ?></h2></a>
					<br />
					<?php
					echo ($config->get('product_description', 'short') == 'short') ? $product_s_desc[$j] : $product_desc[$j];
					?>
				</div>
				<?php
				if ($show_price == 1)
				{
					$product_price = $redshophelper->getProductNetPrice($redshop_product_id[$j]);

					echo "<div>".REDCURRENCY_SYMBOL."&nbsp;".$product_price['product_price']."</div>";
				}
				//echo $this->searchresult['products'][$assoc_id]->product_currency.' '.number_format($this->searchresult['products'][$assoc_id]->product_price, 2);
				?>
				<div>
				<?php
				if($show_addtocart == 1 && $show_price == 1)
				{
					//$product_stock = $redshophelper->getProductStock ($redshop_product_id[$j]);
					$product_stock = $redstockroom->isStockExists ( $redshop_product_id[$j] );

					$product = $redshophelper->getProductById($redshop_product_id[$j]);
					$add_cart_flag = false;

					if($expired == 1)
					{
							echo PRODUCT_EXPIRE_TEXT;
					}
					else if($product_stock && USE_STOCKROOM)
					{
						// check if preorder is set to yes than add pre order button
						if(ALLOW_PRE_ORDER)
						{
							$add_cart_flag = true;
							$p_availability_date = "";
							if($product->product_availability_date!="")
								$p_availability_date = $redconfig->convertDateFormat($product->product_availability_date);

							$ADD_OR_PRE_LBL = JText::_ ( 'PRE_ORDER' );
							$ADD_OR_PRE_TOOLTIP =str_replace ( "{availability_date}",$p_availability_date, ALLOW_PRE_ORDER_MESSAGE);
							$ADD_OR_PRE_BTN =  PRE_ORDER_IMAGE;
						}
						else
						{
							echo JText::_('PRODUCT_OUTOFSTOCK_MESSAGE');
						}
					}
					else
					{
						$add_cart_flag = true;
						$ADD_OR_PRE_LBL = JText::_('ADD_TO_CART');
						$ADD_OR_PRE_BTN =  ADDTOCART_IMAGE;
						$ADD_OR_PRE_TOOLTIP = "";
					}
					if($add_cart_flag)
					{
						echo "<div><form name='addtocartredproductfinder".$redshop_product_id[$j]."' id='addtocartredproductfinder".$redshop_product_id[$j]."' action='index.php' method='post'>
							<input type='hidden' value='".$redshop_product_id[$j]."' name='product_id'>
							<input type='hidden' value='cart' name='view'>
							<input type='hidden' value='com_redshop' name='option'>
							<input type='hidden' value='add' name='task'>
							<input type='hidden' name='product_price' value='".$product_price."'>
							<input type='hidden' name='quantity' id='quantity".$redshop_product_id[$j]."' value='1'>
							<div onclick='document.addtocartredproductfinder".$redshop_product_id[$j].".submit();' style='cursor:pointer;' title='".$ADD_OR_PRE_TOOLTIP."' >".$ADD_OR_PRE_LBL."</div></form></div>";
					}
				}
				?>
				</div>
				<div style="clear:left;">
					<hr />
				</div>
			</div>
			<?php
		}
	}
}

$returnurl = JRequest::getVar('returnurl', false);
if ($returnurl){
 $submiturl = JRoute::_(urldecode($returnurl));
}else{
 $submiturl = JRoute::_('index.php');
}
?>

<form name="adminForm" method="post" action="<?php echo $submiturl; ?>">
<?php
$hide_dropdown='';
if (!$returnurl)
{
	$query = "SELECT id, tag_name FROM #__redproductfinder_tags";
	$db->setQuery($query);
	$tags = $db->loadAssocList('id');
	$hidepost=JRequest::get('request');
	$myid=1;
	//echo "<pre>";print_r($hidepost);
	$hide_dropdownvalue='';
	foreach ($hidepost as $key => $value)
	{
		if (substr($key, 0, 4) == 'type')
		{

					if(is_array($value))
					{

						$maintag='';
						foreach($value as $v)
						{

							$query = "SELECT t.*,type.*,ts.* FROM #__redproductfinder_tags as t left outer join #__redproductfinder_tag_type as type on t.id=type.tag_id left outer join #__redproductfinder_types as ts on type.type_id=ts.id where t.tag_name='".$tags[$v]['tag_name']."' group by type.type_id ";
							$db->setQuery($query);
							$type_id = $db->loadObject();

							$hide_dropdownvalue .=$tags[$v]['id'].",";

						}

					}else{
							$hide_dropdownvalue.=$value.",";
					}

						$hide_dropdown.=$key.",";
		}
	}
	if($hide_dropdown=='')
	{
		$hide_dropdown="";
	}
	$maintask=JRequest::getVar('task');
?>
	<input type="hidden" name="formname" value="<?php echo $formname;?>" />
	<?PHP if($maintask!='findproducts'){?>
		<input type="hidden" name="option" value="com_redproductfinder" />


	<?PHP }?>
	<input type="hidden" name="task" value="redproductfinder" />
	<input type="hidden" name="controller" value="redproductfinder" />
	<input type="hidden" name="searchkey" value="<?php echo $searchkey;?>" />
	<input type="hidden" name="hide_dropdown" value="<?php echo $hide_dropdown; ?>" />
	<input type="hidden" name="hide_dropdownvalue" value="<?php echo $hide_dropdownvalue; ?>" />
	<input type="hidden" name="myid" value="<?php echo $myid;?>" />
	<input type="hidden" name="from_startdate" value="<?php echo JRequest::getVar('from_startdate');?>" />
	<input type="hidden" name="to_enddate" value="<?php echo JRequest::getVar('to_enddate');?>" />
	<input type="hidden" name="month" value="<?php echo JRequest::getVar('month');?>" />

<?php
}
?>
<div id="link_search">
	<input type="submit" name="submit" value="<?php echo JText::_('SEARCH_AGAIN'); ?>" />
	
</div>
<div id="link_result">
	<a href='<?php echo "index.php?".$_SERVER['QUERY_STRING'];?>'><?php echo JText::_('LINK_THIS_RESULT'); ?></a>
</div>
<?php
if ($returnurl)
{
	?>
	<div id="link_returnurl">
		<?php echo JHTML::_('link', urldecode($returnurl), JText::_('LINK_RETURN')); ?>
	</div>
	<?php
}
?>
</form>
