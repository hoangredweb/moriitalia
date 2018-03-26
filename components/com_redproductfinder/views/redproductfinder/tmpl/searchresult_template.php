<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');
require_once( JPATH_SITE.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'extra_field.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'product.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'configuration.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'stockroom.php' );
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php');
require_once( JPATH_SITE.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'pagination.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'helper.php' );


$lang =& JFactory::getLanguage();
$extension = 'com_redshop';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$lang->load($extension, $base_dir, $language_tag, true);

$Redconfiguration = new Redconfiguration();
$Redconfiguration->defineDynamicVars();

$extra_field = new extraField();
$db = JFactory :: getDBO();
$doc =& JFactory::getDocument();
$session = JFactory::getSession('product_currency');
$redconfig = new Redconfiguration();
$redstockroom = new rsstockroomhelper();
$producthelper = new producthelper();
$objhelper = new redhelper();

	$post = JRequest::get('POST');

	//display search result tag start
	$serach_tag=$serarch_disp;
	$template_desc = str_replace ("{search_tag_display}",$serach_tag,$template_desc);
	//display search result tag end

	if(isset($post['product_currency']))
		$session->set('product_currency',$post['product_currency']);

	$currency_symbol = 	REDCURRENCY_SYMBOL;
	$currency_convert = 1;
	if($session->get('product_currency')){

		$currency_symbol = $session->get('product_currency');
		$convertPrice = new convertPrice();
		$currency_convert = $convertPrice->convert(1);
	}

	$doc->addScriptDeclaration("
                var site_url = '".JURI::root()."';
                var AJAX_CART_BOX = ".AJAX_CART_BOX.";
                var REDCURRENCY_SYMBOL = '".REDCURRENCY_SYMBOL."';
                var CURRENCY_SYMBOL_CONVERT = '".$currency_symbol."';
                var CURRENCY_CONVERT = '".$currency_convert."';
                var PRICE_SEPERATOR = '".PRICE_SEPERATOR."';
				var PRODUCT_OUTOFSTOCK_MESSAGE = '".JText::_('PRODUCT_OUTOFSTOCK_MESSAGE')."';
				var CURRENCY_SYMBOL_POSITION = '".CURRENCY_SYMBOL_POSITION."';
				var PRICE_DECIMAL = '".PRICE_DECIMAL."';

				var THOUSAND_SEPERATOR = '".THOUSAND_SEPERATOR."';
                var VIEW_CART = '".JText::_('VIEW_CART')."';
                var CONTINUE_SHOPPING = '".JText::_('CONTINUE_SHOPPING')."';
                var CART_SAVE = '".JText::_('CART_SAVE')."';
                var IS_REQUIRED = '".JText::_('IS_REQUIRED')."';
                var ENTER_NUMBER = '".JText::_('ENTER_NUMBER')."';
                var USE_STOCKROOM = '".USE_STOCKROOM."';
                var USE_AS_CATALOG = '".USE_AS_CATALOG."';
                var SHOW_PRICE = '".SHOW_PRICE."';
				var DEFAULT_QUOTATION_MODE = '".DEFAULT_QUOTATION_MODE."';
                var PRICE_REPLACE = '".PRICE_REPLACE."';
				var PRICE_REPLACE_URL = '".PRICE_REPLACE_URL."';
				var ZERO_PRICE_REPLACE = '".ZERO_PRICE_REPLACE."';
				var ZERO_PRICE_REPLACE_URL = '".ZERO_PRICE_REPLACE_URL."';
		");

$doc->addScript ('components/com_redshop/assets/js/attribute.js');
$doc->addScript ('components/com_redshop/assets/js/common.js');

if (AJAX_CART_BOX == 1){
			$doc->addScript('components/com_redshop/assets/js/fetchscript.js');
			$doc->addStyleSheet('components/com_redshop/assets/css/fetchscript.css');
		}
	$temp_detail = '';
	if(strstr($template_desc,"{redproductfinderfilter:"))
	{
		if(file_exists(JPATH_SITE.DS."components".DS."com_redproductfinder".DS."helpers".DS."redproductfinder_helper.php"))
		{
			include_once(JPATH_SITE.DS."components".DS."com_redproductfinder".DS."helpers".DS."redproductfinder_helper.php");
			$redproductfinder_helper = new redproductfinder_helper();
			$hdnFields = array('texpricemin'=>'0','texpricemax'=>'0');
			$hide_filter_flag = false;
			/*if($this->_id)
			{
				$prodctofcat = $producthelper->getProductCategory($this->_id);
				if(empty($prodctofcat))
					$hide_filter_flag = true;
			}*/
			$template_desc = $redproductfinder_helper->replaceProductfinder_tag($template_desc,$hdnFields,$hide_filter_flag);
		}
	}
	

$total_product = count($this->searchresult);
$endlimit = $total_product;
$start = JRequest::getInt( 'limitstart', 0, '', 'int' );

if (strstr($template_desc, "{pagination}" ))
{
	$endlimit = getProductPerPage ();
	if(strstr($template_desc,"{product_display_limit}"))
	{
			$endlimit = JRequest::getInt( 'limit', $endlimit, '', 'int' );
	}
}

if (strstr($template_desc, "{pagination}" ))
{		
		
		$pagination = new redPagination($total_product, $start, $endlimit);
		$slidertag = $pagination->getPagesLinks();
		
		if(strstr($template_desc,"{product_display_limit}"))
		{
			$slidertag = "<form action='' method='post'> ".$pagination->getListFooter()."</form>";
			$template_desc = str_replace("{product_display_limit}",$slidertag,$template_desc);
			$template_desc = str_replace("{pagination}",'',$template_desc);
		}
		$template_desc = str_replace("{pagination}",$slidertag,$template_desc);		
}
$template_desc = str_replace("{product_display_limit}","",$template_desc);

if(strstr($template_desc,"perpagelimit:"))
{
	$perpage = explode ( '{perpagelimit:', $template_desc );
	$perpage = explode ( '}', $perpage[1] );
	$template_desc = str_replace("{perpagelimit:".intval($perpage[0])."}","",$template_desc);
}
?>	

<script type="text/javascript">

function setSliderMinMax()
{ 
	if(document.getElementById('slider_texpricemin') && document.getElementById('texpricemin'))
	{
		document.getElementById('texpricemin').value = document.getElementById('slider_texpricemin').value;
	}
	if(document.getElementById('slider_texpricemax') && document.getElementById('texpricemax'))
	{
		document.getElementById('texpricemax').value = document.getElementById('slider_texpricemax').value;
	}
	document.frmfinderfilter.submit();
}
</script>

<?php
$uri =& JURI::getInstance();
$url= $uri->root();
$app = JFactory::getApplication($url);
$config = $app->getParams('com_redproductfinder');
$redproductfinder_template= $config->get('prod_template', 260);
$products=getProducts($redshop_product_id);

$ProductPriceArr = $producthelper->getProductNetPrice($products[0]->product_id);
$min = $ProductPriceArr['product_price'];
$ProductPriceArr = $producthelper->getProductNetPrice($products[count($products)-1]->product_id);
$max = $ProductPriceArr['product_price']  ;
if($min>=$max)
{
	$min = $products[0]->product_price;
	$max = $max+100;
}
$texpricemin = floor($min);
$texpricemax = ceil($max);
global $mainframe;
$menu	=& $mainframe->getMenu();
$item	=& $menu->getActive();

$option	= JRequest::getVar('option','com_redshop');
$params = &$mainframe->getParams($option);
$order_data = $objhelper->getOrderByList();
$order_by_select = JRequest::getVar ( 'order_by', '' );
$disabled="";
if ($order_by_select=='')
{
	$order_by_select = $params->get ( 'order_by', DEFAULT_PRODUCT_ORDERING_METHOD );
}
$order_by = JHTML::_ ( 'select.genericlist', $order_data, 'order_by', 'class="inputbox" size="1" onChange="javascript:setSliderMinMax();" '.$disabled.' ', 'value', 'text', $order_by_select );

if(strstr($template_desc,"{order_by}"))
{
	//$orderby_form = "<form name='order_by' action='' method='post'>";
	$orderby_form .= $order_by;
	$orderby_form .= "<input type='hidden' name='texpricemin' id='texpricemin' value='".$texpricemin."' />";
	$orderby_form .= "<input type='hidden' name='texpricemax' id='texpricemax' value='".$texpricemax."' />";
	$orderby_form .= "<input type='hidden' name='slider_texpricemin' id='slider_texpricemin' value='".$texpricemin."' />";
	$orderby_form .= "<input type='hidden' name='slider_texpricemax' id='slider_texpricemax' value='".$texpricemax."' />";
	$orderby_form .= "<input type='hidden' name='manufacturer_id' id='manufacturer_id' value='".$products[0]->manufacturer_id."' />";
	$orderby_form .= "<input type='hidden' name='category_template' id='category_template' value='".$redproductfinder_template."' />";
	//$orderby_form .= "</form>";
	
	$template_desc =  str_replace("{order_by_lbl}",JText::_('COM_REDSHOP_SELECT_ORDER_BY' ),$template_desc);
	$template_desc = str_replace("{order_by}",$orderby_form,$template_desc);
}

function getProductPerPage()
{
	global $mainframe;
	$menu	=& $mainframe->getMenu();
	$item	=& $menu->getActive();
	
	$uri =& JURI::getInstance();
	$url= $uri->root();
	$app = JFactory::getApplication($url);
	$config = $app->getParams('com_redproductfinder');
	$redproductfinder_template= $config->get('prod_template', 260);
	
	$redTemplate = new Redtemplate();		
	$template_array = $redTemplate->getTemplate("redproductfinder",$redproductfinder_template);
	$template_desc = $template_array[0]->template_desc;

	if(isset($template_desc) && strstr($template_desc,"{pagination}") && strstr($template_desc,"perpagelimit:"))
	{
		$perpage = explode ( '{perpagelimit:', $template_desc );
		$perpage = explode ( '}', $perpage[1] );
		$limit = intval($perpage[0]);
	}
	
	if(strstr($template_desc,"{product_display_limit}"))
	{
			$endlimit = JRequest::getInt( 'limit', 0, '', 'int' );
	}
	return $limit;
}

$template_d1 = explode ( "{product_loop_start}", $template_desc );
$template_d2 = explode ( "{product_loop_end}", $template_d1 [1] );
$template_desc = $template_d2 [0];
//$template_product = $template_desc;

global $count_no_user_field;

if($endlimit == 0)
 {  
 	$final_endlimit = $total_product;
 }else {
 	$final_endlimit = $endlimit;
 }

for($i = $start; $i < ($start+$final_endlimit); $i ++)
		{
			$data_add = '';
			$thum_image = "";
			$query = 'SELECT p.*,c.category_id FROM #__redshop_product AS p,#__redshop_product_category_xref AS c '
			.' WHERE p.product_id = c.product_id AND p.product_id = '. $redshop_product_id[$i];
	
			$db->setQuery($query);

			$product = $db->loadObject();

			if(!is_object($product)) {
				break;
			}


				$count_no_user_field = 0;

				// counting attribute
				$attributes = getCountAttributes ( $product->product_id );

				// counting accessory
				$accessory = getCountAccessory ( $product->product_id );
				$attributes += $accessory;

				$product_template_desc = $template_desc;//$product_template [0]->template_desc;

				$tmpuserfield=array();
				$userfield_template = "";
				$userfieldsFirst = "";
				$userfieldsLast = "";

				$tmpuserfieldstart = @ explode ( "{if product_userfield}", $product_template_desc );

				if(count($tmpuserfieldstart) > 1)
				{
					$tmpuserfieldend = @ explode ( "{product_userfield end if}", $tmpuserfieldstart [1] );
					if(count($tmpuserfieldend)>0)
					{
						$userfield_template = $tmpuserfieldend[0];
						$tmpuserfield = @ explode ( '}', $tmpuserfieldend [0] );
					}
					if(count($tmpuserfieldend)>1)
					{
						$userfieldsLast = $tmpuserfieldend [1];
					}
				}
				if(count($tmpuserfieldstart) > 0)
				{
					$userfieldsFirst = $tmpuserfieldstart[0];
				}
				$product_userfileds_name = array();
				if (count($tmpuserfield)>0) {

					$ufield = "";
					for($ui=0;$ui<count($tmpuserfield);$ui++)
					{
						$val = strpbrk ( $tmpuserfield[$ui], "{" );
						$value = str_replace ( "{", "", $val );
//						echo "<br/>".$product->product_id;
						$product_userfileds = $extra_field->list_all_user_fields ( $value, 12, "", "", "",$product->product_id );
						$product_userfileds_name [] = $value;
						$ufield .= $product_userfileds;
						if ($product_userfileds) {
							$count_no_user_field ++ ;
						}
						$userfield_template = str_replace ( '{' . $value . '}', $product_userfileds, $userfield_template );
					}
					$userfield_template = "{if product_userfield}".$userfield_template."{product_userfield end if}";
					// attribute ajax change
//					if (AJAX_CART_BOX==1 || AJAX_CART_BOX==0)
//					{
						$product_userfileds_form = "<form method='post' action='' id='user_fields_form_".$product->product_id."' name='user_fields_form_".$product->product_id."'>";
						if ($ufield != "")
						{
							$userfield_template = str_replace ( '{if product_userfield}', $product_userfileds_form, $userfield_template );
							$userfield_template = str_replace ( '{product_userfield end if}', "</form>", $userfield_template );
						} else {
							$userfield_template = str_replace ( '{if product_userfield}', "", $userfield_template );
							$userfield_template = str_replace ( '{product_userfield end if}', "", $userfield_template );
						}
//					} else {
//						$userfield_template = str_replace ( '{if product_userfield}', "", $userfield_template );
//						$userfield_template = str_replace ( '{product_userfield end if}', "", $userfield_template );
//					}
				}
				else
				{
					if(AJAX_CART_BOX)
					{

						$pq = "SELECT * FROM #__redshop_product AS p"
							. ", #__redshop_template AS t "
							. "WHERE t.template_id = p.product_template "
							. "AND t.template_section='product' "
							. "AND p.product_id=".$product->product_id
							. " AND t.published = 1 LIMIT 0,1 ";
						
							$db->setQuery($pq);

						$prodtemplate = $db->loadObjectlist();
						$prodtemplate[0]->template_desc	= $redTemplate->readtemplateFile("product",$prodtemplate[0]->template_name);

						$ajax_detail_templatename = "";
						$ajax_detail_template = $redTemplate->getTemplate ( "ajax_cart_detail_box" );

						for($w = 0; $w < count ( $ajax_detail_template ); $w ++)
						{
							if (strstr( $prodtemplate[0]->template_desc, "{ajaxdetail_template:" . $ajax_detail_template [$w]->template_name . "}" ))
							{
								$ajax_detail_templatename = $ajax_detail_template [$w]->template_name;
								$ajax_detail_template_desc = $ajax_detail_template [$w]->template_desc;
							}
						}
						if ($ajax_detail_templatename=="")
						{
							$ajax_detail_template = $redTemplate->getTemplate ( "ajax_cart_detail_box", DEFAULT_AJAX_DETAILBOX_TEMPLATE);
							if($ajax_detail_template){
								$ajax_detail_templatename = $ajax_detail_template[0]->template_name;
								$ajax_detail_template_desc = $ajax_detail_template[0]->template_desc;
							}
						}
						$ajax_detail_template_desc = $redTemplate->readtemplateFile("ajax_cart_detail_box",$ajax_detail_templatename);
	//					$userfieldsFirst = "";
	//					$userfieldsLast = "";
						$tmpuserfieldstart = @ explode ( "{if product_userfield}", $ajax_detail_template_desc );
						if(count($tmpuserfieldstart) > 1)
						{
							$tmpuserfieldend = @ explode ( "{product_userfield end if}", $tmpuserfieldstart [1] );
							if(count($tmpuserfieldend)>0)
							{
								$userfield_template = $tmpuserfieldend[0];
								$tmpuserfield = @ explode ( '}', $tmpuserfieldend [0] );
							}
						}
						$product_userfileds_name = array();
						if (count($tmpuserfield)>0) {

							$ufield = "";
							for($ui=0;$ui<count($tmpuserfield);$ui++)
							{
								$val = strpbrk ( $tmpuserfield[$ui], "{" );
								$value = str_replace ( "{", "", $val );
								$product_userfileds = $extra_field->list_all_user_fields ( $value, 12, "", "", "",$product->product_id );
								$product_userfileds_name [] = $value;
								$ufield .= $product_userfileds;
								if ($product_userfileds) {
									$count_no_user_field ++ ;
								}
								$userfield_template = str_replace ( '{' . $value . '}', $product_userfileds, $userfield_template );
							}
							$userfield_template = "{if product_userfield}".$userfield_template."{product_userfield end if}";
							// attribute ajax change
							$product_userfileds_form = "<form method='post' action='' id='user_fields_form_".$product->product_id."' name='user_fields_form_".$product->product_id."'>";
							if ($ufield != "")
							{
								$userfield_template = str_replace ( '{if product_userfield}', $product_userfileds_form, $userfield_template );
								$userfield_template = str_replace ( '{product_userfield end if}', "</form>", $userfield_template );
							} else {
								$userfield_template = str_replace ( '{if product_userfield}', "", $userfield_template );
								$userfield_template = str_replace ( '{product_userfield end if}', "", $userfield_template );
							}
							$userfield_template = "<div style='display:none;'>".$userfield_template."</div>";
						}
					} else {
						$count_no_user_field = 0;
					}
				}

				$data_add = $userfieldsFirst.$userfield_template.$userfieldsLast;

		// adding userfieds to attribute count
				$attributes += $count_no_user_field;
				/********************************************************* end user fields ********************************************************/

				$link = JRoute::_ ( 'index.php?option=com_redshop&view=product&pid=' . $product->product_id .'&Itemid=' . $Itemid );
				$pname = $redconfig->maxchar ( $product->product_name, CATEGORY_PRODUCT_TITLE_MAX_CHARS, CATEGORY_PRODUCT_TITLE_END_SUFFIX );
				$pname = "<a href='" . $link . "'>" . $pname . "</a>";
				$data_add = str_replace ( "{product_name}", $pname, $data_add );

				/*
				 * manufacturer data
				 */
				$manufacturer_id = $product->manufacturer_id;
				if($manufacturer_id!=0)
				{
					$manufacturer_data = getManufacturer($manufacturer_id);
					$manufacturer_link_href = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=detail&mid='.$manufacturer_id.'&Itemid='.$Itemid);
					$manufacturer_name = "";
					if(count($manufacturer_data)>0)
					{
						$manufacturer_name = $manufacturer_data[0]->manufacturer_name;
					}
					$manufacturer_link = '<a href="'.$manufacturer_link_href.'" title="'.$manufacturer_name.'">'.$manufacturer_name.'</a>';
					$data_add = str_replace ( "{manufacturer_link}", $manufacturer_link , $data_add );
					if(strstr("{manufacturer_link}",$data_add))
					{
						$data_add = str_replace ( "{manufacturer_name}", $manufacturer_name , $data_add );
					} else {
						$data_add = str_replace ( "{manufacturer_name}", "" , $data_add );
					}
				} else {
					$data_add = str_replace ( "{manufacturer_link}", "" , $data_add );
					$data_add = str_replace ( "{manufacturer_name}", "" , $data_add );
				}
				//End

				$rmore = "<a href='" . $link . "'>" . JText::_ ( 'READ_MORE' ) . "</a>";
				$data_add = str_replace ( "{read_more}", $rmore, $data_add );
				$data_add = str_replace ( "{read_more_link}", $link, $data_add );
				/*
				 * product loop template extra field
				 * lat arg set to "1" for indetify parsing data for product tag loop in category
				 * last arg will parse {producttag:NAMEOFPRODUCTTAG} nameing tags.
				 *
				 * "1" is for section as product
				 */
				$data_add = $redshophelper->getExtraSectionTag ($redproductfinder_template, $product->product_id, "1", $data_add, 1 );

				$p_s_desc = $redconfig->maxchar ( $product->product_s_desc, CATEGORY_PRODUCT_DESC_MAX_CHARS, CATEGORY_PRODUCT_DESC_END_SUFFIX );
				$data_add = str_replace ( "{product_s_desc}", $p_s_desc, $data_add );

				$stockamountList = $redstockroom->getStockAmountImage($product->product_id);
				if(count($stockamountList)>0)
				{
					$stockamountImage = '<a class="imgtooltip"><span>';
					$stockamountImage .= '<div class="spnheader">'.JText::_('STOCK_AMOUNT').'</div>';
					$stockamountImage .= '<div class="spnalttext" id="stockImageTooltip'.$product->product_id.'">'.$stockamountList[0]->stock_amount_image_tooltip.'</div></span>';
					$stockamountImage .= '<img src="'.$url.'components/com_redshop/assets/images/stockroom'.DS.$stockamountList[0]->stock_amount_image.'" width="150px" height="90px" alt="'.$stockamountList[0]->stock_amount_image_tooltip.'" id="stockImage'.$product->product_id.'" /></a>';
				}
				$data_add = str_replace ( "{product_stock_amount_image}", $stockamountImage, $data_add );

				// check product for not for sale
				if(!$product->not_for_sale){
					$data_add = $redshophelper->GetProductShowPrice ( $product->product_id, $data_add );
				}else{

					$data_add = str_replace ( "{product_price_lbl}", "", $data_add );
					$data_add = str_replace ( "{product_price}", "", $data_add );
					$data_add = str_replace ( "{price_excluding_vat}", "", $data_add );
					$data_add = str_replace ( "{product_price_table}", "", $data_add );
					$data_add = str_replace ( "{product_old_price}", "", $data_add );
					$data_add = str_replace ( "{product_price_saving}", "", $data_add );
				}

				/************************************
	           	   *  Conditional tag
    	           *  if product on discount : Yes
        	       *  {if product_on_sale} This product is on sale {product_on_sale end if} // OUTPUT : This product is on sale
            	   *  NO : // OUTPUT : Display blank
           		************************************/
				if( $product->product_on_sale == 0)
				{
					if(strstr($data_add,"{if product_on_sale}"))
	                {
	                	$template_pd_sdata = explode('{if product_on_sale}',$data_add);
                        $template_pd_start =$template_pd_sdata[0];
                        $template_pd_edata = explode('{product_on_sale end if}',$template_pd_sdata[1]);
                        $template_pd_end =$template_pd_edata[1];
//                        $template_pd_middle =$template_pd_edata[0];
                        $data_add=$template_pd_start.$template_pd_end;
					}
					$data_add = str_replace ( "{discount_start_date}",'',$data_add);
	                $data_add = str_replace ( "{discount_end_date}", '' , $data_add );
				} else {
	                $data_add = str_replace ( "{discount_start_date}", $redconfig->convertDateFormat($product->discount_stratdate) , $data_add );
					$data_add = str_replace ( "{discount_end_date}", $redconfig->convertDateFormat($product->discount_enddate) , $data_add );
	                $data_add = str_replace ( "{if product_on_sale}", '', $data_add );
	            	$data_add = str_replace ( "{product_on_sale end if}", '', $data_add );
				}

				$data_add = str_replace ( "{product_desc}", $product->product_desc, $data_add );

				if (strstr ( $data_add, "{product_thumb_image_3}" )) {
					$pimg_tag = '{product_thumb_image_3}';
					$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT_3;
					$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH_3;
				} elseif (strstr ( $data_add, "{product_thumb_image_2}" )) {
					$pimg_tag = '{product_thumb_image_2}';
					$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT_2;
					$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH_2;
				} elseif (strstr ( $data_add, "{product_thumb_image_1}" )) {
					$pimg_tag = '{product_thumb_image_1}';
					$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT;
					$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH;
				} else {
					$pimg_tag = '{product_thumb_image}';
					$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT;
					$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH;
				}
				$thum_image = $redshophelper->getProductImage ( $product->product_id, $link, $pw_thumb, $ph_thumb,2,1 );
				$data_add = str_replace ( $pimg_tag, $thum_image, $data_add );

				$data_add = str_replace ( "{product_id_lbl}", JText::_ ( 'PRODUCT_ID_LBL' ), $data_add );
				$data_add = str_replace ( "{product_id}", $product->product_id, $data_add );
				$data_add = str_replace ( "{product_number_lbl}", JText::_ ( 'PRODUCT_NUMBER_LBL' ), $data_add );
				$data_add = str_replace ( "{product_number}", $product->product_number, $data_add );
 				$data_add = $redshophelper->replaceVatinfo($data_add);
 				
 				// Start Product attribute template
				$attribute_template = $producthelper->getAttributeTemplate($data_add);				
				if(strstr($data_add,"{attribute_template:$attribute_template->template_name}"))
				{					
					$isChilds = false;
					$attributes_set = array();
	
					if($product->attribute_set_id > 0){
						$attributes_set = $producthelper->getProductAttribute($product->product_id,$product->attribute_set_id,0,1);
					}
					$attributes = $producthelper->getProductAttribute($product->product_id);
					$attributes = array_merge($attributes,$attributes_set);

					//$attribute_template = $producthelper->getAttributeTemplate($data_add);
					$totalatt = count($attributes);
	
					$data_add = $producthelper->replaceAttributeData($product->product_id,0,0,$attributes,$data_add,$attribute_template);
				}
				//End Product attribute template
 				
				// get cart tempalte
				//$data_add = replaceCartTemplate($product,$data_add,$attributes,"user_fields_form_".$product->product_id,$product_userfileds_name);
				//$data_add = replaceCartTemplate($product->product_id,0,0,0,$data_add);
				$data_add = $producthelper->replaceCartTemplate($product->product_id,0,0,0,$data_add, $isChilds, $userfieldArr=array(), $totalatt);
				$a_data_add .= $data_add;
	}
echo $template_d1 [0].$a_data_add.$template_d2 [1];
		// count products attributes
	function getCountAttributes($pid) {
		$db = &JFactory :: getDBO();
		$query = "SELECT count(a.attribute_id) " . "FROM #__redshop_product AS p" . ", #__redshop_product_attribute AS a " . "WHERE a.product_id = p.product_id " . "AND p.product_id=" . $pid . " AND p.published = 1 " . "ORDER BY a.ordering ASC ";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}

	function getCountAccessory($pid) {
		$db = &JFactory :: getDBO();
		$query = "SELECT count(a.accessory_id) " . "FROM #__redshop_product AS p" . ", #__redshop_product_accessory AS a " . "WHERE a.product_id = p.product_id " . "AND p.product_id=" . $pid . " AND p.published = 1 ";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}


	// get Manufacturer
	function getManufacturer($mid=0) {
		$db = &JFactory :: getDBO();
		$manufacurer_id = "";
		if($mid){
			$manufacurer_id = " AND manufacturer_id = ".$mid;
		}

		$query = "SELECT * FROM `#__redshop_manufacturer` WHERE published = 1 ".$manufacurer_id." ORDER BY ordering ASC";
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
	
	// Get products
	function getProducts($pid) {
		$db = &JFactory :: getDBO();
		$pid=implode(',',$pid);
		$query = 'SELECT p.*,c.category_id FROM #__redshop_product AS p,#__redshop_product_category_xref AS c '
			.' WHERE p.product_id = c.product_id AND p.product_id IN('. $pid.')';	
		$db->setQuery($query);
		return $db->loadObjectList();
	}
?>