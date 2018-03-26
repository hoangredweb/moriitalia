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
JHtml::_('rjquery.select2', 'select');

$uri = JURI::getInstance();
$url = $uri->root();

$Itemid = JRequest::getInt('Itemid');
$user   = JFactory::getUser();
$option = 'com_redshop';

$document    = JFactory::getDocument();
$mod_class   = explode(" ", $params->get('moduleclass_sfx'));
$headslide   = in_array('headslide', $mod_class);
$sale_slider = in_array('sale-product', $mod_class);

// Render modules inside this article override
$renderer = $document->loadRenderer("modules");
$raw      = array("style" => "xhtml");

JLoader::load('RedshopHelperAdminImages');

// Lightbox Javascript
JHtml::script('com_redshop/attribute.js', false, true);
JHtml::script('com_redshop/common.js', false, true);
JHtml::script('com_redshop/redbox.js', false, true);
JHtml::script(Juri::base() . 'templates/genshop/js/swiper.min.js', false, true);
JHtml::script(Juri::base() . 'templates/genshop/js/functionswiper.js', false, true);

// $producthelper   = new producthelper;
// $redhelper       = new redhelper;
// $redTemplate     = Redtemplate::getInstance();
// $extraField      = new extraField;
//$stockroomhelper = rsstockroomhelper::getInstance();
$producthelper   = producthelper::getInstance();
$redhelper       = redhelper::getInstance();
$redTemplate     = Redtemplate::getInstance();
$extraField      = extraField::getInstance();
$stockroomhelper = rsstockroomhelper::getInstance();
$texts           = new text_library;

$cateItemid = "";
$categoryId = "";


$moduleId = "mod_" . $module->id;

echo "<div class=\"mod_redshop_products_wrapper " . $moduleId . "\" >";


echo "<div class=\"slide_wrapper\" id=\"mod_products_" . $module->id . "\">";

for ($i = 0; $i < count($rows); $i++)
{
	$row = $rows[$i];

	if ($showStockroomStatus == 1)
	{
		$isStockExists = $stockroomhelper->isStockExists($row->product_id);
		$outofstock    = '';

		if (!$isStockExists)
		{
			$isPreorderStockExists = $stockroomhelper->isPreorderStockExists($row->product_id);
		}

		if (!$isStockExists)
		{
			$productPreorder = $row->preorder;

			if (($productPreorder == "global" && ALLOW_PRE_ORDER) || ($productPreorder == "yes") || ($productPreorder == "" && ALLOW_PRE_ORDER))
			{
				if (!$isPreorderStockExists)
				{
					$outofstock  = 'outofstock';
					$stockStatus = "<div class=\"modProductStockStatus mod_product_outstock\"><span></span>" . JText::_('COM_REDSHOP_OUT_OF_STOCK') . "</div>";
				}
				else
				{
					$stockStatus = "<div class=\"modProductStockStatus mod_product_preorder\"><span></span>" . JText::_('COM_REDSHOP_PRE_ORDER') . "</div>";
				}
			}
			else
			{
				$outofstock  = 'outofstock';
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

	$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $row->product_id . '&cid=' . $row->category_id . '&Itemid=' . $Itemid);

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
			if (!empty($thumb))
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
			}
			else
			{
				$thumImage = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . Redshop::getConfig()->get('PRODUCT_DEFAULT_IMAGE');
			}

			echo "<div class=\"mod_redshop_products_image\"><a href=\"" . $link . "\" title=\"$row->product_name\"><img src=\"" . $thumImage . "\"></a><button data-target=\"#quick-view-" . $row->product_id . "\" data-toggle=\"modal\" class=\"btn btn-info btn-lg quick-view\" type=\"button\" style=\"margin-left: 1px;\">" . JText::_('COM_REDSHOP_TEXT_QUICK_VIEW') . "</button>" . "</div>";

		}
	}

	if (!empty($stockStatus))
	{
		echo $stockStatus;
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
		$hiddenUserField  = '';
		$userfieldArr     = array();

		if (AJAX_CART_BOX)
		{
			$ajaxDetailTemplateDesc = "";
			$ajaxDetailTemplate     = $producthelper->getAjaxDetailboxTemplate($row);

			if (count($ajaxDetailTemplate) > 0)
			{
				$ajaxDetailTemplateDesc = $ajaxDetailTemplate->template_desc;
			}

			$returnArr         = $producthelper->getProductUserfieldFromTemplate($ajaxDetailTemplateDesc);
			$templateUserfield = $returnArr[0];
			$userfieldArr      = $returnArr[1];

			if ($templateUserfield != "")
			{
				$ufield = "";

				for ($ui = 0; $ui < count($userfieldArr); $ui++)
				{
					$productUserfileds = $extraField->list_all_user_fields($userfieldArr[$ui], 12, '', '', 0, $row->product_id);
					$ufield            .= $productUserfileds[1];

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

		$addtocart = $producthelper->replaceCartTemplate($row->product_id, $categoryId, 0, 0, "{form_addtocart:gen_add_to_cart1}", false, $userfieldArr, $totalatt, $totalAccessory, $countNoUserField, $moduleId);
		//echo "<div class='mod-btn-group row'>";
		// echo "<div class='col-xs-6'>";
		// echo "<a class=\"link-detail\" href=\"" . $link . "\" title=\"\">". JText::_('COM_REDSHOP_TXT_DETAIL')."</a>";
		// echo "</div>";
		//echo "<div class='col-xs-6'>";
		//echo "<div class=\"mod_redshop_products_addtocart " . $outofstock . "\">". $addtocart . $hiddenUserField . "</div>";
		//echo "</div>";
		//echo "</div>";

	}

	if ($showShortDescription)
	{
		echo "<div class=\"mod_redshop_products_desc\">" . $row->product_s_desc . "</div>";
	}

	if (!$row->not_for_sale && $showPrice)
	{
		$productArr = $producthelper->getProductNetPrice($row->product_id);

		if ($showVat != '0')
		{
			$productPrice         = $productArr['product_main_price'];
			$productPriceDiscount = $productArr['productPrice'] + $productArr['productVat'];
			$productOldPrice      = $productArr['product_old_price'];
		}
		else
		{
			$productPrice         = $productArr['product_price_novat'];
			$productPriceDiscount = $productArr['productPrice'];
			$productOldPrice      = $productArr['product_old_price_excl_vat'];
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

			if (!isset($addtocart))
			{
				$addtocart = null;
			}

			if (!isset($hiddenUserField))
			{
				$hiddenUserField = null;
			}

			$displyText = "<div class=\"mod_product_price_wrapper\"><div class=\"inner row\"><div class=\"mod_redshop_products_title\"><a href=\"" . $link . "\" title=\"\">" . JHTML::_('string.truncate', $row->product_name, 37) . "</a></div><div class=\"mod_redshop_products_price col-xs-12 align-left\">" . $productDiscountPrice . "</div></div><div class=\"wishlist\">" . $producthelper->replaceWishlistButton($row->product_id, '{wishlist_link}') . "</div><div class=\"mod_redshop_products_addtocart " . $outofstock . "\">" . $addtocart . $hiddenUserField . "</div></div>";


			if ($row->product_on_sale && $productPriceDiscount > 0)
			{
				echo "<div class=\"mod_product_price_wrapper on-sale\">";

				echo "<div class=\"inner row\">";


				if ($productOldPrice > $productPriceDiscount)
				{
					$displyText   = "";
					$savingPrice  = $productOldPrice - $productPriceDiscount;
					$percentPrice = round(($savingPrice / $productOldPrice) * 100);
					if ($showDiscountPriceLayout)
					{

						echo "<div class=\"on_sale_wrap\">";
						// echo "<div class=\"on-sale\">";
						//     echo "<p>".JText::_('REDSHOP_PRODUCT_LABEL_DISCOUNT')."<p>";
						// echo "</p></div>";
						echo "<div class=\"sale-savings\">";
						echo "<p><span id=\"display_product_saving_price_percentage\">" . $percentPrice . "</span><span class=\"percent\">%</span></p>";
						echo "</div>";
						echo "</div>";
						echo "<div class=\"mod_redshop_products_title\"><a href=\"" . $link . "\" title=\"\">" . JHTML::_('string.truncate', $row->product_name, 37) . "</a></div>";
						echo "<div id=\"mod_redoldprice\" class=\"mod_redoldprice col-sm-6 col-xs-6\"><span>" . $producthelper->getProductFormattedPrice($productOldPrice) . "</span></div>";
						$productPrice = $productPriceDiscount;
						echo "<div class=\"mod_redshop_products_price col-sm-6 col-xs-6\">" . $producthelper->getProductFormattedPrice($productPriceDiscount) . "</div>";
					}
					else
					{
						$productPrice = $productPriceDiscount;
						echo "<div class=\"mod_redshop_products_price col-sm-6\">" . $producthelper->getProductFormattedPrice($productPrice) . "</div>";
					}
				}
				echo "</div>";

				echo "<div class=\"wishlist\">" . $producthelper->replaceWishlistButton($row->product_id, '{wishlist_link}') . "</div>";
				echo "<div class=\"mod_redshop_products_addtocart " . $outofstock . "\">" . $addtocart . $hiddenUserField . "</div>";

				echo "</div>";
			}

			echo $displyText;
		}
	}

	//echo "<div class=\"wishlist\">" . $producthelper->replaceWishlistButton($row->product_id, '{wishlist_link}') ."</div>";

	//here was title

	$extrafield = "";

	if (!empty($row->extraFields))
	{
		$extrafield = $row->extraFields;
	}

	if (!empty($extrafield))
	{
		foreach ($extrafield as $list)
		{
			if (isset($list->field_title))
			{
				if ($list->field_title == 'product_lbl')
				{
					echo '<div class=\'product_lbl\'>' . $list->data_txt . '</div>';
				}
			}
		}
	}

	if ($showReadmore)
	{
		echo "<div class=\"mod_redshop_products_readmore\"><a href=\"" . $link . "\">" . JText::_('COM_REDSHOP_TXT_READ_MORE') . "</a>&nbsp;</div>";
	}

// Checking for child products
	$childproduct = $producthelper->getChildProduct($row->product_id);

	if (count($childproduct) > 0)
	{
		if (Redshop::getConfig()->get('PURCHASE_PARENT_WITH_CHILD') == 1)
		{
			$attributes_set = array();

			if ($row->attribute_set_id > 0)
			{
				$attributes_set = $producthelper->getProductAttribute(0, $row->attribute_set_id, 0, 1);
			}

			$attributes = $producthelper->getProductAttribute($row->product_id);
			$attributes = array_merge($attributes, $attributes_set);
		}
		else
		{
			$attributes = array();
		}
	}
	else
	{
		$attributes_set = array();

		if ($row->attribute_set_id > 0)
		{
			$attributes_set = $producthelper->getProductAttribute(0, $row->attribute_set_id, 0, 1);
		}

		$attributes = $producthelper->getProductAttribute($row->product_id);
		$attributes = array_merge($attributes, $attributes_set);
	}

	$attribute_template = $producthelper->getAttributeTemplate('{attribute_template:gen_attributes}');

	$pimg_tag = '{more_images}';
	$ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
	$pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');

	$media_image = $producthelper->getAdditionMediaImage($row->product_id, "product");

	$more_images = '';

	for ($m = 0, $mn = count($media_image); $m < $mn; $m++)
	{
		$filename1 = REDSHOP_FRONT_IMAGES_RELPATH . "product/" . $media_image[$m]->media_name;

		if ($media_image[$m]->media_name != $media_image[$m]->product_full_image && file_exists($filename1))
		{
			$alttext = $producthelper->getAltText('product', $media_image[$m]->section_id, '', $media_image[$m]->media_id);

			if (!$alttext)
			{
				$alttext = $media_image [$m]->media_name;
			}

			if ($media_image [$m]->media_name)
			{
				$thumb = $media_image [$m]->media_name;

				if (Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'))
				{
					$pimg      = $redhelper->watermark('product', $thumb, $pw_thumb, $ph_thumb, Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), "1");
					$linkimage = $this->redhelper->watermark('product', $thumb, '', '', Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), "0");

					$hoverimg_path = $redhelper->watermark(
						'product',
						$thumb,
						Redshop::getConfig()->get('ADDITIONAL_HOVER_IMAGE_WIDTH'),
						Redshop::getConfig()->get('ADDITIONAL_HOVER_IMAGE_HEIGHT'),
						Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'),
						'2'
					);
				}
				else
				{
					$pimg      = RedShopHelperImages::getImagePath(
						$thumb,
						'',
						'thumb',
						'product',
						$pw_thumb,
						$ph_thumb,
						Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
					);
					$linkimage = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $thumb;

					$hoverimg_path = RedShopHelperImages::getImagePath(
						$thumb,
						'',
						'thumb',
						'product',
						Redshop::getConfig()->get('ADDITIONAL_HOVER_IMAGE_WIDTH'),
						Redshop::getConfig()->get('ADDITIONAL_HOVER_IMAGE_HEIGHT'),
						Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
					);
				}

				if (Redshop::getConfig()->get('PRODUCT_ADDIMG_IS_LIGHTBOX'))
				{
					$more_images_div_start = "<div class='additional_image'><a href='" . $linkimage . "' title='" . $alttext . "' rel=\"myallimg\">";
					$more_images_div_end   = "</a></div>";
					$more_images           .= $more_images_div_start;
					$more_images           .= "<img src='" . $pimg . "' alt='" . $alttext . "' title='" . $alttext . "'>";
					$more_images_hrefend   = "";
				}
				else
				{
					if (Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'))
					{
						$img_path = $redhelper->watermark('product', $thumb, $pw_thumb, $ph_thumb, Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), '0');
					}
					else
					{
						$img_path = RedShopHelperImages::getImagePath(
							$thumb,
							'',
							'thumb',
							'product',
							$pw_thumb,
							$ph_thumb,
							Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
						);
					}

					$hovermore_images = $redhelper->watermark('product', $thumb, '', '', Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), '0');

					$filename_org = REDSHOP_FRONT_IMAGES_RELPATH . "product/" . $media_image[$m]->product_full_image;

					if (file_exists($filename_org))
					{
						$thumb_original = $media_image[$m]->product_full_image;
					}
					else
					{
						$thumb_original = Redshop::getConfig()->get('PRODUCT_DEFAULT_IMAGE');
					}

					if (Redshop::getConfig()->get('WATERMARK_PRODUCT_THUMB_IMAGE'))
					{
						$img_path_org = $redhelper->watermark('product', $thumb_original, $pw_thumb, $ph_thumb, Redshop::getConfig()->get('WATERMARK_PRODUCT_THUMB_IMAGE'), '0');
					}
					else
					{
						$img_path_org = RedShopHelperImages::getImagePath(
							$thumb_original,
							'',
							'thumb',
							'product',
							$pw_thumb,
							$ph_thumb,
							Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
						);
					}

					$hovermore_org = RedShopHelperImages::getImagePath(
						$thumb_original,
						'',
						'thumb',
						'product',
						$pw_thumb,
						$ph_thumb,
						Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
					);
					$oimg_path     = RedShopHelperImages::getImagePath(
						$thumb,
						'',
						'thumb',
						'product',
						$pw_thumb,
						$ph_thumb,
						Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
					);

					$more_images_div_start = "<div class='additional_image'
				 								onmouseover='display_image(\"" . $img_path . "\"," . $row->product_id . ",\"" . $hovermore_images . "\");'
				 								onmouseout='display_image_out(\"" . $img_path_org . "\"," . $row->product_id . ",\"" . $img_path_org . "\");'>";
					$more_images_div_end   = "</div>";
					$more_images           .= $more_images_div_start;
					$more_images           .= '<a href="javascript:void(0)" >' . "<img src='" . $pimg . "' title='" . $alttext . "' style='cursor: auto;'>";
					$more_images_hrefend   = "</a>";
				}

				if (Redshop::getConfig()->get('ADDITIONAL_HOVER_IMAGE_ENABLE'))
				{
					$more_images .= "<img src='" . $hoverimg_path . "' alt='" . $alttext . "' title='" . $alttext . "' class='redImagepreview'>";
				}

				$more_images .= $more_images_hrefend;
				$more_images .= $more_images_div_end;
			}
		}
	}

	$insertStr = "<div class='redhoverImagebox' id='additional_images" . $row->product_id . "'>" . $more_images . "</div><div class=\"clr\"></div>";

// More images end

// More videos
	$media_videos = $producthelper->getAdditionMediaImage($row->product_id, "product", "youtube");
	$videoStr     = '<div class="redVideobox">';

	for ($m = 0, $mn = count($media_videos); $m < $mn; $m++)
	{
		$more_vid = '<iframe width="854" height="480" src="http://www.youtube.com/embed/' . $media_videos[$m]->media_name . '" frameborder="0" allowfullscreen>';
		$more_vid .= '</iframe>';

		$videoStr .= "<div id='additional_vids_" . $media_videos[$m]->media_id . "'><a class='additional_video' href='#video-" . $media_videos[$m]->media_id . "'><img src='https://img.youtube.com/vi/" . $media_videos[$m]->media_name . "/default.jpg' height='80px' width='80px'/></a></div>";
		$videoStr .= "<div class='hide'><div class='content' id='video-" . $media_videos[$m]->media_id . "'>" . $more_vid . "</div></div>";
	}

	$videoStr .= "</div>";


	echo "</div>";

	echo "</div>";

	//modal here
	echo "<div id=\"quick-view-" . $row->product_id . "\" class=\"modal fade\" role=\"dialog\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>";
	echo "</div>";
	echo "<div class=\"modal-body\">";
	//echo "{tab ". JText::_('REDSHOP_PRODUCT_MODAL_INFO_TEXT')."}";
	echo "<div class=\"product row\">";
	echo "<div class=\"col-sm-6 col-xs-12 product-left\">";
	echo "<div class=\"redSHOP_product_box_left\">";
	echo "<div class=\"product_image\"><a id=\"a_main_image" . $row->product_id . "\" href=\"" . $link . "\"><img id=\"main_image" . $row->product_id . "\" src=\"" . $thumImage . "\"/></a></div>";
	echo "<div class=\"product_more_images\">" . $insertStr . $videoStr . "</div>";
	echo "</div>";
	echo "<div class=\"div_compare div_compare_modal hidden\">";
	echo $producthelper->replaceCompareProductsButton($row->product_id, $row->category_id, '{compare_product_div}');
	echo "</div>";
	echo "</div>";
	// echo "<div class=\"col-sm-4 col-md-offset-0 col-xs-12 product-center\">";

	// echo "</div>";
	echo "<div class=\"col-sm-6 col-xs-12 product-right\">";

	echo "<div class=\"product_title clearfix\">";
	echo "<div class=\"brand\">";
	echo "<div class=\"redSHOP_product_detail_box\">";
	echo "<div class=\"title\">";
	echo "<a class=\"brand-logo\" href=\"index.php?option=com_redshop&view=manufacturers&layout=product&mid=" . $row->manufacturer_id . "&Itemid=" . $Itemid . "\"><img src=\"" . REDSHOP_FRONT_IMAGES_ABSPATH . 'manufacturer/' . $row->manufacturer_image . "\"/>" . "</a>";
	echo "<h3>" . $row->product_name . "</h3>";
	echo "</div>";
	// echo "<div class=\"product_title clearfix\">";

	// echo "</div>";
	echo "<div class=\"product_details\">";
	echo "<div id=\"product_price\">";
	if ($productOldPrice > $productPriceDiscount):
		echo "<span class=\"product_price_val\">" . $producthelper->getProductFormattedPrice($productOldPrice) . "</span>";
		echo "<span class=\"product_price_discount bold\">" . $productDiscountPrice . "</span>";
	else:
		echo "<span class=\"product_price_discount\">" . $producthelper->getProductFormattedPrice($productOldPrice) . "</span>";
	endif;
	echo "</div>";

	echo "<div class=\"view-full\">";
	echo "<a href=\"" . $link . "\">" . JText::_('VIEW_FULL_PRODUCT') . ">></a>";
	echo $producthelper->replaceWishlistButton($row->product_id, '{wishlist_link}');
	echo "</div>";

	echo "<div class=\"product_desc\">";
	echo "<div class=\"product_desc_full\">" . $row->product_s_desc . "</div>";
	echo "</div>";

	echo "</div>";

	echo "<div class=\"in-stock hidden\"><i class=\"icon-time\"></i>" . $producthelper->replaceProductStockdata($row->product_id, "", "", "{stock_status}", "") . "</div>";

	echo "<div class=\"cardiv1\">";
	echo $producthelper->replaceAttributeData($row->product_id, 0, 0, $attributes, '{attribute_template:gen_attributes}', $attribute_template);
	echo "</div>";

	echo "<div class=\"buttons\">";
	echo "<div class=\"wishlist\">";
	echo "<span>" . $producthelper->replaceCompareProductsButton($row->product_id, $row->category_id, '{compare_products_button}') . "</span>";
	echo "<span class=\"right\"><i class=\"icon-plus\"></i></span>";
	echo "</div>";
	echo "<div class=\"product_addtocart " . $outofstock . "\">";
	echo "<div id=\"add_to_cart_all\">" . $addtocart . $hiddenUserField . "</div>";
	echo "</div>";
	echo "</div>";

	echo "</div>";
	echo "</div>";
	echo "</div>";


	//echo $texts->replace_texts('{delivery}');
	echo "</div>";
	echo "</div>";
	//echo "{tab ". JText::_('REDSHOP_PRODUCT_MODAL_DES_TEXT')."}";

	//echo "{/tabs}";
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
        makeswiper('<?php echo "#mod_products_" . $module->id;?>', '.mod_redshop_products');
        reponSwiper('<?php echo "#mod_products_" . $module->id;?>', '<?php echo "#mod_products_" . $module->id;?> .nextbtn', '<?php echo "#mod_products_" . $module->id;?> .prevbtn');
    </script>
<?php else: ?>
    <script type="text/javascript">
        jQuery('<?php echo "#mod_products_" . $module->id;?>').prepend('<div class=\'chevron-box\'><div class=\'prevbtn\'><b class=\'icon icon-angle-left\'></b></div><div class=\'nextbtn\'><b class=\'icon icon-angle-right\'></b></div></div>');
        makeswiper('<?php echo "#mod_products_" . $module->id;?>', '.mod_redshop_products');
        reponSwiper_mostproduct('<?php echo "#mod_products_" . $module->id;?>', '<?php echo "#mod_products_" . $module->id;?> .nextbtn', '<?php echo "#mod_products_" . $module->id;?> .prevbtn');
    </script>
<?php endif ?>
