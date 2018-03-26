<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_products
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$uri = JURI::getInstance();
$url = $uri->root();

$Itemid = JRequest::getInt('Itemid');
$user   = JFactory::getUser();
$option = 'com_redshop';

$document = JFactory::getDocument();
$mod_class = explode(" ", $params->get('moduleclass_sfx'));
$headslide = in_array('headslide',$mod_class);
$sale_slider = in_array('sale-product',$mod_class);

JLoader::load('RedshopHelperAdminImages');

// Lightbox Javascript
JHtml::script('com_redshop/attribute.js', false, true);
JHtml::script('com_redshop/common.js', false, true);
JHtml::script('com_redshop/redbox.js', false, true);
JHtml::script(Juri::base() . 'templates/genshop/js/swiper.min.js', false, true);
JHtml::script(Juri::base() . 'templates/genshop/js/functionswiper.js', false, true);

$producthelper   = new producthelper;
$redhelper       = new redhelper;
$redTemplate     = Redtemplate::getInstance();
$extraField      = new extraField;
$stockroomhelper = rsstockroomhelper::getInstance();


$cate_id = $params->get('category')[0];
$ItemData = $producthelper->getMenuInformation(0, 0, '', 'category&layout=detail&cid=' . $cate_id);
if (count($ItemData) > 0)
{
	$cateItemid = $ItemData->id;
}
else
{
	$cateItemid = $redhelper->getItemid($row->product_id, $categoryId);
}

$cate_link = JRoute::_('index.php?option=com_redshop&view=category&layout=detail&cid='.$cate_id.'&manufacturer_id=0&Itemid='.$cateItemid);

$moduleId = "mod_" . $module->id;

echo "<div class=\"mod_redshop_products_wrapper ".$moduleId."\" >";

?>

	<?php if ($headslide): ?>
		<div class="mod_redshop_header">
			<div class="mod_redshop_header_wrapper">
				<?php if ($sale_slider): ?>
					<label><?php echo JText::_('COM_MOD_PRODUCTLIST_SALE_LABEL')?></label>
				<?php else: ?>
					<label><?php echo JText::_('COM_MOD_PRODUCTLIST_NEW_ARRIVAL_LABEL')?></label>
				<?php endif; ?>
				<div class="desc">
					<?php if ($sale_slider): ?>
						<h3><?php echo JText::_('COM_MOD_PRODUCTLIST_SALE_TITLE');?></h3>
						<p><?php echo JText::_('COM_MOD_PRODUCTLIST_SALE');?></p>
					<?php else: ?>
						<h3><?php echo JText::_('COM_MOD_PRODUCTLIST_NEW_ARRIVAL_TITLE');?></h3>
						<p><?php echo JText::_('COM_MOD_PRODUCTLIST_NEW_ARRIVAL');?></p>
					<?php endif; ?>
				</div>
				<a href="index.php?option=com_redshop&view=search&layout=newproduct&template_id=8&categorytemplate=7&productlimit=6&newproduct=365" class="btn btn-default btn-sm see-all"><?php echo JText::_('COM_REDSHOP_CATE_SHOP_ALL');?></a>
			</div>
		</div>
	<?php endif ?>

<?php

echo "<div class=\"slide_wrapper\" id=\"mod_products_".$module->id."\">";

for ($i = 0; $i < count($rows); $i++)
{
	$row = $rows[$i];

	if ($showStockroomStatus == 1)
	{
		$isStockExists = $stockroomhelper->isStockExists($row->product_id);

		if (!$isStockExists)
		{
			$isPreorderStockExists = $stockroomhelper->isPreorderStockExists($row->product_id);
		}

		if (!$isStockExists)
		{
			$productPreorder = $row->preorder;

			if (($productPreorder == "global" && Redshop::getConfig()->get('ALLOW_PRE_ORDER')) || ($productPreorder == "yes") || ($productPreorder == "" && Redshop::getConfig()->get('ALLOW_PRE_ORDER')))
			{
				if (!$isPreorderStockExists)
				{
					$stockStatus = "<div class=\"modProductStockStatus mod_product_outstock\"><span></span>" . JText::_('COM_REDSHOP_OUT_OF_STOCK') . "</div>";
				}
				else
				{
					$stockStatus = "<div class=\"modProductStockStatus mod_product_preorder\"><span></span>" . JText::_('COM_REDSHOP_PRE_ORDER') . "</div>";
				}
			}
			else
			{
				$stockStatus = "<div class=\"modProductStockStatus mod_product_outstock\"><span></span>" . JText::_('COM_REDSHOP_OUT_OF_STOCK') . "</div>";
			}
		}
		else
		{
			$stockStatus = "<div class=\"modProductStockStatus mod_product_instock\"><span></span>" . JText::_('COM_REDSHOP_AVAILABLE_STOCK') . "</div>";
		}
	}

	$categoryId = $producthelper->getCategoryProduct($row->product_id);

	$ItemData = $producthelper->getMenuInformation(0, 0, '', 'product&pid=' . $row->product_id);

	if (count($ItemData) > 0)
	{
		$Itemid = $ItemData->id;
	}
	else
	{
		$Itemid = $redhelper->getItemid($row->product_id, $categoryId);
	}

	$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $row->product_id . '&cid=' . $categoryId . '&Itemid=' . $Itemid);

	echo "<div class=\"mod_redshop_products\">";

	echo "<div class=\"product-wrapper\">";

	$productInfo = $producthelper->getProductById($row->product_id);

	if ($image)
	{
		$thumb = $productInfo->product_full_image;

		if (Redshop::getConfig()->get('WATERMARK_PRODUCT_IMAGE'))
		{
			$thumImage = $redhelper->watermark('product', $thumb, $thumbWidth, $thumbHeight, Redshop::getConfig()->get('WATERMARK_PRODUCT_THUMB_IMAGE'), '0');
			echo "<div class=\"mod_redshop_products_image\"><img src=\"" . $thumImage . "\"></div>";
		}
		else
		{
			$thumImage = RedShopHelperImages::getImagePath(
							$thumb,
							'',
							'thumb',
							'product',
							$thumbWidth,
							$thumbHeight,
							Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
						);
			echo "<div class=\"mod_redshop_products_image\"><a href=\"" . $link . "\" title=\"$row->product_name\"><img src=\"" . $thumImage . "\"></a></div>";
		}
	}

	if (!empty($stockStatus))
	{
		echo $stockStatus;
	}

	echo "<div class=\"mod_redshop_products_title\"><a href=\"" . $link . "\" title=\"\">" . $row->product_name . "</a></div>";

	if ($showShortDescription)
	{
		echo "<div class=\"mod_redshop_products_desc\">" . $row->product_s_desc . "</div>";
	}

	if (!$row->not_for_sale && $showPrice)
	{
		$productArr = $producthelper->getProductNetPrice($row->product_id);

		if ($showVat != '0')
		{
			$productPrice           = $productArr['product_main_price'];
			$productPriceDiscount   = $productArr['productPrice'] + $productArr['productVat'];
			$productOldPrice 		= $productArr['product_old_price'];
		}
		else
		{
			$productPrice          = $productArr['product_price_novat'];
			$productPriceDiscount = $productArr['productPrice'];
			$productOldPrice 		= $productArr['product_old_price_excl_vat'];
		}

		if (Redshop::getConfig()->get('SHOW_PRICE') && (!Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE') || (Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE') && Redshop::getConfig()->get('SHOW_QUOTATION_PRICE'))))
		{
			if (!$productPrice)
			{
				$productDiscountPrice = $producthelper->getPriceReplacement($productPrice);
			}
			else
			{
				$productDiscountPrice = $producthelper->getProductFormattedPrice($productPrice);
			}


			$displyText = "<div class=\"mod_product_price_wrapper\"><div class=\"mod_redshop_products_price\">" . $productDiscountPrice . "</div></div>";


			if ($row->product_on_sale && $productPriceDiscount > 0)
			{
				echo "<div class=\"mod_product_price_wrapper\">";
				if ($productOldPrice > $productPriceDiscount)
				{
					$displyText = "";
					$savingPrice     = $productOldPrice - $productPriceDiscount;

					if ($showDiscountPriceLayout)
					{
						echo "<div id=\"mod_redoldprice\" class=\"mod_redoldprice\">" . $producthelper->getProductFormattedPrice($productOldPrice) . "</div>";
						$productPrice = $productPriceDiscount;
						echo "<div class=\"mod_redshop_products_price\">" . $producthelper->getProductFormattedPrice($productPriceDiscount) . "</div>";
					}
					else
					{
						$productPrice = $productPriceDiscount;
						echo "<div class=\"mod_redshop_products_price\">" . $producthelper->getProductFormattedPrice($productPrice) . "</div>";
					}
				}
				echo "</div>";
			}

			echo $displyText;
		}
	}

	$extrafield = $row->extraFields;
	foreach ($extrafield as $list) {
		if (isset($list->field_title)) {
			if ($list->field_title == 'product_lbl') {
				echo '<div class=\'product_lbl\'>'.$list->data_txt.'</div>';
			}
		}
	}

	if ($showReadmore)
	{
		echo "<div class=\"mod_redshop_products_readmore\"><a href=\"" . $link . "\">" . JText::_('COM_REDSHOP_TXT_READ_MORE') . "</a>&nbsp;</div>";
	}

	if (isset($showAddToCart) && $showAddToCart)
	{
		// Product attribute  Start
		$attributesSet = array();

		if ($row->attribute_set_id > 0)
		{
			$attributesSet = $producthelper->getProductAttribute(0, $row->attribute_set_id, 0, 1);
		}

		$attributes = $producthelper->getProductAttribute($row->product_id);
		$attributes = array_merge($attributes, $attributesSet);
		$totalatt   = count($attributes);

		// Product attribute  End


		// Product accessory Start
		$accessory      = $producthelper->getProductAccessory(0, $row->product_id);
		$totalAccessory = count($accessory);

		// Product accessory End


		/*
		 * collecting extra fields
		 */
		$countNoUserField = 0;
		$hiddenUserField = '';
		$userfieldArr = array();

		if (Redshop::getConfig()->get('AJAX_CART_BOX'))
		{
			$ajaxDetailTemplateDesc = "";
			$ajaxDetailTemplate      = $producthelper->getAjaxDetailboxTemplate($row);

			if (count($ajaxDetailTemplate) > 0)
			{
				$ajaxDetailTemplateDesc = $ajaxDetailTemplate->template_desc;
			}

			$returnArr          = $producthelper->getProductUserfieldFromTemplate($ajaxDetailTemplateDesc);
			$templateUserfield = $returnArr[0];
			$userfieldArr       = $returnArr[1];

			if ($templateUserfield != "")
			{
				$ufield = "";

				for ($ui = 0; $ui < count($userfieldArr); $ui++)
				{
					$productUserfileds = $extraField->list_all_user_fields($userfieldArr[$ui], 12, '', '', 0, $row->product_id);
					$ufield .= $productUserfileds[1];

					if ($productUserfileds[1] != "")
					{
						$countNoUserField++;
					}

					$templateUserfield = str_replace('{' . $userfieldArr[$ui] . '_lbl}', $productUserfileds[0], $templateUserfield);
					$templateUserfield = str_replace('{' . $userfieldArr[$ui] . '}', $productUserfileds[1], $templateUserfield);
				}

				if ($ufield != "")
				{
					$hiddenUserField = "<div class=\"hiddenFields\"><form method=\"post\" action=\"\" id=\"user_fields_form_" . $row->product_id . "\" name=\"user_fields_form_" . $row->product_id . "\">" . $templateUserfield . "</form></div>";
				}
			}
		}

		// End

		$addtocart = $producthelper->replaceCartTemplate($row->product_id, $categoryId, 0, 0, "", false, $userfieldArr, $totalatt, $totalAccessory, $countNoUserField, $moduleId);
		echo "<div class=\"mod_redshop_products_addtocart\">" . $addtocart . $hiddenUserField . "</div>";
		
	}

	echo "</div>";

	echo "</div>";

	//modal here
	echo "<div id=\"quick-view-".$row->product_id."\" class=\"modal fade\" role=\"dialog\">";
          echo "<div class=\"modal-dialog\">";
            echo "<div class=\"modal-content\">";
              echo "<div class=\"modal-header\">";
                echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>";
              echo"</div>";
              echo "<div class=\"modal-body\">";
                echo "{tab ". JText::_('REDSHOP_PRODUCT_MODAL_INFO_TEXT')."}";
                    echo "<div class=\"product row\">";
                      echo "<div class=\"col-sm-4 col-xs-12 product-left\">";
                        echo "<div class=\"redSHOP_product_box_left\">";
                          echo "<div class=\"product_image\"><a href=\"".$link."\"><img src=\"". $thumImage ."\"/></a></div>";
                        echo "</div>";
                    	echo "<div class=\"div_compare_modal\">";
                          echo $producthelper->replaceCompareProductsButton($row->product_id, $row->category_id, '{compare_product_div}');
                        echo "</div>";
                      echo "</div>";
                      echo "<div class=\"col-sm-4 col-md-offset-0 col-xs-12 product-center\">";
                        echo "<div class=\"redSHOP_product_box clearfix\">";
                          echo "<div class=\"redSHOP_product_box_right\">";
                            echo "<div class=\"redSHOP_product_detail_box\">";
                              echo "<div class=\"brand\">";
                                echo "Nhà sản xuất: <a href=\"index.php?option=com_redshop&view=manufacturers&layout=product&Itemid=" . $Itemid . "\">" . $row->manufacturer_name . "</a>";
                              echo "</div>";
                              echo "<div class=\"product_title clearfix\">";
                                echo "<h3>" . $row->product_name . "</h3>";
                              echo "</div>";
                              echo "<div id=\"product_price\">";
                                echo "<span class=\"product_price_val\">" . $producthelper->getProductFormattedPrice($productOldPrice) . "</span>";
                                echo "<span class=\"product_price_discount\">" . $productDiscountPrice . "</span>";
                              echo "</div>";

                              echo "<div class=\"in-stock\"><i class=\"icon-time\"></i>" . $producthelper->replaceProductStockdata($row->product_id, "", "", "{stock_status}", "") . "</div>";

                              echo "<div class=\"cardiv1\">";
								
                              echo "</div>";
                       

                            echo "</div>";	
                          echo "</div>";
                        echo "</div>";
                      echo "</div>";
                      echo "<div class=\"col-sm-4 col-md-offset-0 col-xs-12 product-right\">";
                        echo "<div class=\"product_addtocart ".$outofstock."\">";
                          echo "<div id=\"add_to_cart_all\">". $addtocart . $hiddenUserField . "</div>";
                        echo "</div>";
                        echo "<div class=\"wishlist\">";
                          echo "<span>" . $producthelper->replaceWishlistButton($row->product_id, '{wishlist_link}') ."</span>";
                          echo "<span class=\"right\"><i class=\"icon-plus\"></i></span>";
                        echo "</div>";
                       echo "<div class=\"wishlist\">";
                          echo "<span>" . $producthelper->replaceCompareProductsButton($row->product_id, $row->category_id, '{compare_product_div}') . "</span>";
                          echo "<span class=\"right\"><i class=\"icon-plus\"></i></span>";
                        echo "</div>";
                        echo "<div class=\"delivery\">";
                          echo "<h5>".JText::_("REDSHOP_PRODUCT_MODAL_TITLE_SHIPPING")."</h5>";
                          echo "<p>".JText::_("REDSHOP_PRODUCT_MODAL_INFO_SHIPPING")."</p>";
                        echo "</div>";
                      echo "</div>";
                    echo "</div>";
               echo "{tab ". JText::_('REDSHOP_PRODUCT_MODAL_DES_TEXT')."}";
                    	echo "<div class=\"product_desc\">";
							echo "<div class=\"product_desc_full\">" . $row->product_desc . "</div>";
                    	echo "</div>";
                echo "{/tabs}";
              echo "</div>";

            echo "</div>";
          echo "</div>";
        echo "</div>";
	//end modal

	
}
echo "</div>";
echo "</div>";
?>
<?php if ($headslide): ?>
	<script type="text/javascript">
		jQuery('<?php echo "#mod_products_" . $module->id;?>').prepend('<div class=\'chevron-box\'><div class=\'prevbtn\'><b class=\'icon icon-angle-left\'></b></div><div class=\'nextbtn\'><b class=\'icon icon-angle-right\'></b></div></div>');
		makeswiper('<?php echo "#mod_products_" . $module->id;?>','.mod_redshop_products');
		reponSwiper('<?php echo "#mod_products_" . $module->id;?>', '<?php echo "#mod_products_" . $module->id;?> .nextbtn', '<?php echo "#mod_products_" . $module->id;?> .prevbtn');
	</script>
<?php else: ?>
	<script type="text/javascript">
		jQuery('<?php echo "#mod_products_" . $module->id;?>').prepend('<div class=\'chevron-box\'><div class=\'prevbtn\'><b class=\'icon icon-angle-left\'></b></div><div class=\'nextbtn\'><b class=\'icon icon-angle-right\'></b></div></div>');
		makeswiper('<?php echo "#mod_products_" . $module->id;?>','.mod_redshop_products');
		reponSwiper_mostproduct('<?php echo "#mod_products_" . $module->id;?>', '<?php echo "#mod_products_" . $module->id;?> .nextbtn', '<?php echo "#mod_products_" . $module->id;?> .prevbtn');
	</script>
<?php endif ?>