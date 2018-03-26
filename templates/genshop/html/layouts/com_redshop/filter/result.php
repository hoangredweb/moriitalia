<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JLoader::import('redshop.library');

$products    = $displayData['products'];
$template_id = $displayData['templateId'];

$pk          = $displayData['post'];
$keyword     = isset($displayData['keyword']) ? $displayData['keyword'] : '';
$cid         = $pk['cid'] ? $pk['cid'] : 0;
$model       = $displayData['model'];
$app         = JFactory::getApplication();
$input       = $app->input;
$db          = JFactory::getDbo();

$productHelper    = productHelper::getInstance();
$objHelper        = redhelper::getInstance();
$redConfiguration = Redconfiguration::getInstance();
$redConfiguration->defineDynamicVars();
$extraField       = extraField::getInstance();
$stockroomHelper  = rsstockroomhelper::getInstance();
$redTemplate      = Redtemplate::getInstance();
$texts            = new text_library;

$orderData = "";
$list = array(
			JHtml::_('select.option', '', JText::_('COM_REDSHOP_SELECT')),
			JHtml::_('select.option', 'p.product_price', JText::_('COM_REDSHOP_PRODUCT_PRICE_ASC')),
			JHtml::_('select.option', 'p.product_price desc', JText::_('COM_REDSHOP_PRODUCT_PRICE_DESC')),
			JHtml::_('select.option', 'p.product_id', JText::_('COM_REDSHOP_NEWEST'))
		);
$orderData = $list;
$getOrderBy = JRequest::getString('order_by', DEFAULT_PRODUCT_ORDERING_METHOD);
$lists['order_select'] = JHTML::_('select.genericlist', $orderData, 'orderBy', 'class="inputbox" size="1" onchange="order(this);" ', 'value', 'text', $getOrderBy);

$count_no_user_field = 0;
$productData = '';
$extraFieldName = $extraField->getSectionFieldNameArray(1, 1, 1);

JPluginHelper::importPlugin('redshop_product');
$dispatcher = JDispatcher::getInstance();
$params = $app->getParams('com_redshop');

// Check Itemid on pagination
$Itemid = $input->get('Itemid', 0, "int");

$start = $input->get('limitstart', 0, '', 'int');

$fieldArray = $extraField->getSectionFieldList(17, 0, 0);

$templateArray = RedshopHelperTemplate::getTemplate("category", $template_id);
$templateDesc = $templateArray[0]->template_desc;
$attributeTemplate = $productHelper->getAttributeTemplate($templateDesc);

// Begin replace template
$templateDesc = str_replace("{total_product_lbl}", JText::_('COM_REDSHOP_TOTAL_PRODUCT'), $templateDesc);
$templateDesc = str_replace("{total_product}", JText::sprintf('COM_REDSHOP_TOTAL_PRODUCT_COUNT', $displayData['total'] ), $templateDesc);

if (strpos($templateDesc, "{product_loop_start}") !== false && strpos($templateDesc, "{product_loop_end}") !== false)
{
	// Get only Product template
	$templateD1 = explode("{product_loop_start}", $templateDesc);
	$templateD2 = explode("{product_loop_end}", $templateD1[1]);
	$templateProduct = $templateD2[0];

	$attributeTemplate = $productHelper->getAttributeTemplate($templateProduct);

	// Loop product lists
	foreach ($products as $k => $pid)
	{
		$product = $productHelper->getProductById($pid);
		$catid   = $product->category_id;

		// Count accessory
		$accessorylist = $productHelper->getProductAccessory(0, $product->product_id);
		$totacc        = count($accessorylist);
		$netPrice      = $productHelper->getProductNetPrice($pid);
		$productPrice  = $netPrice['productPrice'] + $netPrice['productVat'];

		$dataAdd = $templateProduct;

		// ProductFinderDatepicker Extra Field Start
		$dataAdd = $productHelper->getProductFinderDatepickerValue($templateProduct, $product->product_id, $fieldArray);

		$ItemData = $productHelper->getMenuInformation(0, 0, '', 'product&pid=' . $product->product_id);

		$catidmain = JRequest::getVar("cid");

		if (count($ItemData) > 0)
		{
			$pItemid = $ItemData->id;
		}
		else
		{
			$pItemid = $objHelper->getItemid($product->product_id, $catidmain);
		}

		$dataAdd = str_replace("{product_price}", $productHelper->getProductFormattedPrice($productPrice), $dataAdd);
		$dataAdd = str_replace("{product_id_lbl}", JText::_('COM_REDSHOP_PRODUCT_ID_LBL'), $dataAdd);
		$dataAdd = str_replace("{product_id}", $product->product_id, $dataAdd);
		$dataAdd = str_replace("{product_number_lbl}", JText::_('COM_REDSHOP_PRODUCT_NUMBER_LBL'), $dataAdd);
		$product_number_output = '<span id="product_number_variable' . $product->product_id . '">' . $product->product_number . '</span>';
		$dataAdd = str_replace("{product_number}", $product_number_output, $dataAdd);

		// Replace VAT information
		$dataAdd = $productHelper->replaceVatinfo($dataAdd);
		$language = JFactory::getLanguage();
		$lang = $language->getTag();

		$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $product->product_id . '&cid=' . $catid . '&Itemid=' . $pItemid .'&lang=' . $lang);

		$pname = $redConfiguration->maxchar($product->product_name, Redshop::getConfig()->get('CATEGORY_PRODUCT_TITLE_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_TITLE_END_SUFFIX'));

		if (!empty($keyword))
		{
			$pname = str_ireplace($keyword, "<b class='search_hightlight'>" . $keyword . "</b>", $pname);
		}

		$product_nm = $pname;

		if (strstr($dataAdd, '{product_name_nolink}'))
		{
			$dataAdd = str_replace("{product_name_nolink}", $product_nm, $dataAdd);
		}

		if (strstr($dataAdd, '{product_id}'))
		{
			$dataAdd = str_replace("{product_id}", $product->product_id, $dataAdd);
		}

		if (strstr($dataAdd, '{product_name}'))
		{
			$pname = "<a href='" . $link . "' title='" . $product->product_name . "'>" . $pname . "</a>";
			$dataAdd = str_replace("{product_name}", $pname, $dataAdd);
		}

		if (strstr($dataAdd, '{read_more}'))
		{
			$rmore = "<a href='" . $link . "' title='" . $product->product_name . "'>" . JText::_('COM_REDSHOP_READ_MORE') . "</a>";
			$dataAdd = str_replace("{read_more}", $rmore, $dataAdd);
		}

		if (strstr($dataAdd, '{category_product_link}'))
		{
			$dataAdd = str_replace("{category_product_link}", $link, $dataAdd);
		}

		if (strstr($dataAdd, '{read_more_link}'))
		{
			$dataAdd = str_replace("{read_more_link}", $link, $dataAdd);
		}

		if (strstr($dataAdd, '{product_s_desc}'))
		{
			$p_s_desc = $redConfiguration->maxchar($product->product_s_desc, Redshop::getConfig()->get('CATEGORY_PRODUCT_SHORT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_SHORT_DESC_END_SUFFIX'));

			if (!empty($keyword))
			{
				$p_s_desc = str_ireplace($keyword, "<b class='search_hightlight'>" . $keyword . "</b>", $p_s_desc);
			}

			$dataAdd = str_replace("{product_s_desc}", $p_s_desc, $dataAdd);
		}

		if (strstr($dataAdd, '{product_desc}'))
		{
			$p_desc = $redConfiguration->maxchar($product->product_desc, Redshop::getConfig()->get('CATEGORY_PRODUCT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_DESC_END_SUFFIX'));

			if (!empty($keyword))
			{
				$p_desc = str_ireplace($keyword, "<b class='search_hightlight'>" . $keyword . "</b>", $p_desc);
			}

			$dataAdd = str_replace("{product_desc}", $p_desc, $dataAdd);
		}

		if (strstr($dataAdd, '{product_rating_summary}'))
		{
			// Product Review/Rating Fetching reviews
			$final_avgreview_data = $productHelper->getProductRating($product->product_id);
			$dataAdd = str_replace("{product_rating_summary}", $final_avgreview_data, $dataAdd);
		}

		if (strstr($dataAdd, '{manufacturer_link}'))
		{
			$manufacturer_link_href = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=detail&mid=' . $product->manufacturer_id . '&Itemid=' . $Itemid);

			$query = $db->getQuery(true)
				->select($db->qn('name'))
				->from($db->qn('#__redshop_manufacturer'))
				->where($db->qn('id') . ' = ' . $db->q((int) $product->manufacturer_id));
			$manufacturerName = $db->setQuery($query)->loadResult();

			if (empty($manufacturerName))
			{
				$manufacturer_link = '';
			}
			else
			{
				if (isset($product->manufacturer_name))
				{
					$manufacturer_link = '<a href="' . $manufacturer_link_href . '" title="' . $product->manufacturer_name . '">' . $manufacturerName . '</a>';
				}
				else
				{
					$manufacturer_link = '<a href="' . $manufacturer_link_href . '" title="' . $manufacturerName . '">' . $manufacturerName . '</a>';	
				}
				
			}

			$dataAdd = str_replace("{manufacturer_link}", $manufacturer_link, $dataAdd);

			if (strstr($dataAdd, "{manufacturer_link}"))
			{
				$dataAdd = str_replace("{manufacturer_name}", "", $dataAdd);
			}
		}

		if (strstr($dataAdd, '{manufacturer_product_link}'))
		{
			$manufacturerPLink = "<a href='" . JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $product->manufacturer_id . '&Itemid=' . $Itemid) . "'>" . JText::_("COM_REDSHOP_VIEW_ALL_MANUFACTURER_PRODUCTS") . " " . $product->manufacturer_name . "</a>";
			$dataAdd = str_replace("{manufacturer_product_link}", $manufacturerPLink, $dataAdd);
		}

		if (strstr($dataAdd, '{manufacturer_name}'))
		{
			if (isset($product->manufacturer_name) && $product->manufacturer_name != "")
			{
				$dataAdd = str_replace("{manufacturer_name}", $product->manufacturer_name, $dataAdd);
			}
			else
			{
				$dataAdd = str_replace("{manufacturer_name}", "", $dataAdd);
			}
		}

		if (strstr($dataAdd, '{manufacturer_image}'))
		{
			$query = $db->getQuery(true)
				->select($db->qn('md.media_name'))
				->from($db->qn('#__redshop_manufacturer', 'm'))
				->leftjoin($db->qn('#__redshop_media', 'md') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('md.section_id'))
				->where($db->qn('m.id') . ' = ' . $db->q((int) $product->manufacturer_id));

			$manufacturerImage = $db->setQuery($query)->loadResult();

			if ($manufacturerImage != "")
			{
				$dataAdd = str_replace("{manufacturer_image}", '<img src="' . REDSHOP_FRONT_IMAGES_ABSPATH . 'manufacturer/' . $manufacturerImage . '" />', $dataAdd);
			}
			else
			{
				$dataAdd = str_replace("{manufacturer_image}", "", $dataAdd);
			}
		}

		$extraFieldsForCurrentTemplate = $productHelper->getExtraFieldsForCurrentTemplate($extraFieldName, $templateProduct, 1);

		/*
		 * product loop template extra field
		 * lat arg set to "1" for indetify parsing data for product tag loop in category
		 * last arg will parse {producttag:NAMEOFPRODUCTTAG} nameing tags.
		 * "1" is for section as product
		 */
		if ($extraFieldsForCurrentTemplate)
		{
			$dataAdd = $extraField->extra_field_display(1, $product->product_id, $extraFieldsForCurrentTemplate, $dataAdd, 1);
		}

		if (strstr($dataAdd, "{product_thumb_image_3}"))
		{
			$pimg_tag = '{product_thumb_image_3}';
			$ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT_3');
			$pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH_3');
		}
		elseif (strstr($dataAdd, "{product_thumb_image_2}"))
		{
			$pimg_tag = '{product_thumb_image_2}';
			$ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT_2');
			$pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH_2');
		}
		elseif (strstr($dataAdd, "{product_thumb_image_1}"))
		{
			$pimg_tag = '{product_thumb_image_1}';
			$ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
			$pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');
		}
		else
		{
			$pimg_tag = '{product_thumb_image}';
			$ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
			$pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');
		}

		$hidden_thumb_image = "<input type='hidden' name='prd_main_imgwidth' id='prd_main_imgwidth' value='" . $pw_thumb . "'><input type='hidden' name='prd_main_imgheight' id='prd_main_imgheight' value='" . $ph_thumb . "'>";

		$thum_image = $productHelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, 2, 1);
		$dataAdd = str_replace($pimg_tag, $thum_image . $hidden_thumb_image, $dataAdd);
		$thum_image4 = $productHelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, 2, 1, 'modal');
		$dataAdd = str_replace('{product_thumb_image_4}', $thum_image4 . $hidden_thumb_image, $dataAdd);

		/* front-back image tag */
		if (strstr($dataAdd, "{front_img_link}") || strstr($dataAdd, "{back_img_link}"))
		{
			if ($this->_data->product_thumb_image)
			{
				$mainsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_thumb_image;
			}
			else
			{
				$mainsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_full_image . "&newxsize=" . $pw_thumb . "&newysize=" . $ph_thumb . "&swap=" . Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING');
			}

			if ($this->_data->product_back_thumb_image)
			{
				$backsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_thumb_image;
			}
			else
			{
				$backsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_back_full_image . "&newxsize=" . $pw_thumb . "&newysize=" . $ph_thumb . "&swap=" . Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING');
			}

			$ahrefpath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_full_image;
			$ahrefbackpath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_full_image;

			$product_front_image_link = "<a href='#' onClick='javascript:changeproductImage(" . $product->product_id . ",\"" . $mainsrcPath . "\",\"" . $ahrefpath . "\");'>" . JText::_('COM_REDSHOP_FRONT_IMAGE') . "</a>";
			$product_back_image_link = "<a href='#' onClick='javascript:changeproductImage(" . $product->product_id . ",\"" . $backsrcPath . "\",\"" . $ahrefbackpath . "\");'>" . JText::_('COM_REDSHOP_BACK_IMAGE') . "</a>";

			$dataAdd = str_replace("{front_img_link}", $product_front_image_link, $dataAdd);
			$dataAdd = str_replace("{back_img_link}", $product_back_image_link, $dataAdd);
		}
		else
		{
			$dataAdd = str_replace("{front_img_link}", "", $dataAdd);
			$dataAdd = str_replace("{back_img_link}", "", $dataAdd);
		}
		/* front-back image tag end */


		/* product preview image. */
		if (strstr($dataAdd, '{product_preview_img}'))
		{
			if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . 'product/' . $product->product_preview_image))
			{
				$previewsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_preview_image . "&newxsize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH') . "&newysize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT') . "&swap=" . Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING');
				$previewImg = "<img src='" . $previewsrcPath . "' class='rs_previewImg' />";
				$dataAdd = str_replace("{product_preview_img}", $previewImg, $dataAdd);
			}
			else
			{
				$dataAdd = str_replace("{product_preview_img}", "", $dataAdd);
			}
		}

		// 	product preview image end.

		/* front-back preview image tag... */
		if (strstr($dataAdd, "{front_preview_img_link}") || strstr($dataAdd, "{back_preview_img_link}"))
		{
			if ($product->product_preview_image)
			{
				$mainpreviewsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_preview_image . "&newxsize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH') . "&newysize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT') . "&swap=" . Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING');
			}

			if ($product->product_preview_back_image)
			{
				$backpreviewsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_preview_back_image . "&newxsize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH') . "&newysize=" . Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT') . "&swap=" . Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING');
			}

			$product_front_image_link = "<a href='#' onClick='javascript:changeproductPreviewImage(" . $product->product_id . ",\"" . $mainpreviewsrcPath . "\");'>" . JText::_('COM_REDSHOP_FRONT_IMAGE') . "</a>";
			$product_back_image_link = "<a href='#' onClick='javascript:changeproductPreviewImage(" . $product->product_id . ",\"" . $backpreviewsrcPath . "\");'>" . JText::_('COM_REDSHOP_BACK_IMAGE') . "</a>";

			$dataAdd = str_replace("{front_preview_img_link}", $product_front_image_link, $dataAdd);
			$dataAdd = str_replace("{back_preview_img_link}", $product_back_image_link, $dataAdd);
		}
		else
		{
			$dataAdd = str_replace("{front_preview_img_link}", "", $dataAdd);
			$dataAdd = str_replace("{back_preview_img_link}", "", $dataAdd);
		}
		/* front-back preview image tag end */

		$dataAdd = $productHelper->getJcommentEditor($product, $dataAdd);

		/************************************
		*  Conditional tag
		*  if product on discount : Yes
		*  {if product_on_sale} This product is on sale {product_on_sale end if} // OUTPUT : This product is on sale
		*  NO : // OUTPUT : Display blank
		************************************/
		$dataAdd = $productHelper->getProductOnSaleComment($product, $dataAdd);

		/* replace wishlistbutton */
		$dataAdd = $productHelper->replaceWishlistButton($product->product_id, $dataAdd);

		/* replace compare product button */
		$dataAdd = $productHelper->replaceCompareProductsButton($product->product_id, $catid, $dataAdd);

		if (strstr($dataAdd, "{stockroom_detail}"))
		{
			$dataAdd = $stockroomHelper->replaceStockroomAmountDetail($dataAdd, $product->product_id);
		}

		/* checking for child products */
		$childproduct = $productHelper->getChildProduct($product->product_id);

		if (count($childproduct) > 0)
		{
			if (Redshop::getConfig()->get('PURCHASE_PARENT_WITH_CHILD') == 1)
			{
				$isChilds = false;
				/* get attributes */
				$attributes_set = array();

				if ($product->attribute_set_id > 0)
				{
					$attributes_set = $productHelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
				}

				$attributes = $productHelper->getProductAttribute($product->product_id);
				$attributes = array_merge($attributes, $attributes_set);
			}
			else
			{
				$isChilds = true;
				$attributes = array();
			}
		}
		else
		{
			$isChilds = false;

			/*  get attributes */
			$attributes_set = array();

			if ($product->attribute_set_id > 0)
			{
				$attributes_set = $productHelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
			}

			$attributes = $productHelper->getProductAttribute($product->product_id);
			$attributes = array_merge($attributes, $attributes_set);
		}

		$returnArr = $productHelper->getProductUserfieldFromTemplate($dataAdd);
		$userfieldArr = $returnArr[1];

		/* Product attribute  Start */
		$totalatt = count($attributes);
		/* check product for not for sale */

		$dataAdd = $productHelper->getProductNotForSaleComment($product, $dataAdd, $attributes);
		/* echo $dataAdd;die(); */
		$dataAdd = $productHelper->replaceProductInStock($product->product_id, $dataAdd, $attributes, $attributeTemplate);

		$dataAdd = $productHelper->replaceAttributeData($product->product_id, 0, 0, $attributes, $dataAdd, $attributeTemplate, $isChilds);

		// More images
		if (strstr($dataAdd, "{more_images_3}"))
		{
			$mpimg_tag = '{more_images_3}';
			$mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT_3');
			$mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_3');
		}
		elseif (strstr($dataAdd, "{more_images_2}"))
		{
			$mpimg_tag = '{more_images_2}';
			$mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT_2');
			$mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_2');
		}
		elseif (strstr($dataAdd, "{more_images_1}"))
		{
			$mpimg_tag = '{more_images_1}';
			$mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT');
			$mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE');
		}
		else
		{
			$mpimg_tag = '{more_images}';
			$mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT');
			$mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE');
		}

		if (strstr($dataAdd, $mpimg_tag))
		{
			if (isset($moreimage_response) && $moreimage_response != "")
			{
				$more_images = $moreimage_response;
			}
			else
			{
				$media_image = $productHelper->getAdditionMediaImage($product->product_id, "product");

				$more_images = '';

				for ($m = 0, $mn = count($media_image); $m < $mn; $m++)
				{
					$filename1 = REDSHOP_FRONT_IMAGES_RELPATH . "product/" . $media_image[$m]->media_name;

					if ($media_image[$m]->media_name != $media_image[$m]->product_full_image && file_exists($filename1))
					{
						$alttext = $productHelper->getAltText('product', $media_image[$m]->section_id, '', $media_image[$m]->media_id);

						if (!$alttext)
						{
							$alttext = $media_image [$m]->media_name;
						}

						if ($media_image [$m]->media_name)
						{
							$thumb = $media_image [$m]->media_name;

							if (Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'))
							{
								$pimg          = $objHelper->watermark('product', $thumb, $mpw_thumb, $mph_thumb, Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), "1");
								$linkimage     = $objHelper->watermark('product', $thumb, '', '', Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), "0");

								$hoverimg_path = $objHelper->watermark(
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
								$pimg = RedShopHelperImages::getImagePath(
												$thumb,
												'',
												'thumb',
												'product',
												$mpw_thumb,
												$mph_thumb,
												Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
											);
								$linkimage     = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $thumb;

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
								$more_images .= $more_images_div_start;
								$more_images .= "<img src='" . $pimg . "' alt='" . $alttext . "' title='" . $alttext . "'>";
								$more_images_hrefend = "";
							}
							else
							{
								if (Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'))
								{
									$img_path = $objHelper->watermark('product', $thumb, $pw_thumb, $ph_thumb, Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), '0');
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

								$hovermore_images = $objHelper->watermark('product', $thumb, '', '', Redshop::getConfig()->get('WATERMARK_PRODUCT_ADDITIONAL_IMAGE'), '0');

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
								$oimg_path = RedShopHelperImages::getImagePath(
													$thumb,
													'',
													'thumb',
													'product',
													$mpw_thumb,
													$mph_thumb,
													Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
												);

								$more_images_div_start = "<div class='additional_image'>";
								$more_images_div_end   = "</div>";
								$more_images .= $more_images_div_start;
								$more_images .= '<a href="javascript:void(0)" onmouseover=\'display_image("'.$img_path.'", "'.$product->product_id.'" ,"'.$hovermore_images.'");\'
						 								onmouseout=\'display_image_out("'.$img_path_org.'","'. $product->product_id .'","'.$img_path_org.'");\' data-image="'.$hovermore_images.'" data-zoom-image="'.$hovermore_images.'" >' . "<img src='" . $pimg . "' title='" . $alttext . "' style='cursor: auto;'>";
								$more_images_hrefend = "</a>";
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
			}
		}

		$insertStr     = "<div class='redhoverImagebox' id='additional_images" . $product->product_id . "'>" . $more_images . "</div><div class=\"clr\"></div>";
		$dataAdd = str_replace($mpimg_tag, $insertStr, $dataAdd);

		if (strstr($dataAdd, "{more_videos}"))
		{
			$media_videos = $productHelper->getAdditionMediaImage($product->product_id, "product", "youtube");
			$insertStr = '<div class="redVideobox">';

			for ($m = 0, $mn = count($media_videos); $m < $mn; $m++)
			{
				$more_vid = '<iframe width="854" height="480" src="http://www.youtube.com/embed/' . $media_videos[$m]->media_name . '" frameborder="0" allowfullscreen>';
				$more_vid .= '</iframe>';

				$insertStr .= "<div id='additional_vids_" . $media_videos[$m]->media_id . "'><a class='additional_video' href='#video-" . $media_videos[$m]->media_id . "'><img src='https://img.youtube.com/vi/" . $media_videos[$m]->media_name . "/default.jpg' height='80px' width='80px'/></a></div>";
				$insertStr .= "<div class='hide'><div class='content' id='video-" . $media_videos[$m]->media_id . "'>" . $more_vid . "</div></div>";
			}
			$insertStr .= "</div>";

			$dataAdd = str_replace("{more_videos}", $insertStr, $dataAdd);
		}

		// Replace attribute with null value if it exist
		if (isset($attributeTemplate))
		{
			$templateAttribute = "{attributeTemplate:" . isset($attributeTemplate->template_name) ?:'' . "}";

			if (strstr($dataAdd, $templateAttribute))
			{
				$dataAdd = str_replace($templateAttribute, "", $dataAdd);
			}
		}

		/* get cart tempalte */
		$dataAdd = $productHelper->replaceCartTemplate($product->product_id, $catid, 0, 0, $dataAdd, $isChilds, $userfieldArr, $totalatt, $totacc, $count_no_user_field, "");

		$results = $dispatcher->trigger('onPrepareProduct', array(& $dataAdd, & $params, $product));

		$productData .= $dataAdd;
	}

	$productTmpl = $productData;
	$catName = "";

	if (!empty($cid))
	{
		$query = $db->getQuery(true)
			->select($db->qn('name'))
			->from($db->qn('#__redshop_category'))
			->where($db->qn('id') . ' = ' . $db->q((int) $cid));

		$catName = $db->setQuery($query)->loadResult();
	}

	if (strstr($templateDesc, "{pagination}"))
	{
		$pagination = $displayData["pagination"];
		$templateDesc = str_replace("{pagination}", $pagination->getPaginationLinks('pagination.customize'), $templateDesc);
	}

	$usePerPageLimit = false;

	if (strstr($templateDesc, "perpagelimit:"))
	{
		$usePerPageLimit = true;
		$perpage       = explode('{perpagelimit:', $templateDesc);
		$perpage       = explode('}', $perpage[1]);
		$templateDesc = str_replace("{perpagelimit:" . intval($perpage[0]) . "}", "", $templateDesc);
	}

	if (strstr($templateDesc, "{product_display_limit}"))
	{
		if ($usePerPageLimit == false)
		{
			$limitBox = '';
		}
		else
		{
			$limitBox = $pagination->getLimitBox();
		}

		$templateDesc = str_replace("{product_display_limit}", $limitBox, $templateDesc);
	}

	$templateDesc = str_replace("{order_by_lbl}", JText::_('COM_REDSHOP_SELECT_ORDER_BY'), $templateDesc);
	$templateDesc = str_replace("{order_by}", $lists['order_select'], $templateDesc);
	$templateDesc = str_replace("{product_loop_start}", "", $templateDesc);
	$templateDesc = str_replace("{product_loop_end}", "", $templateDesc);
	$templateDesc = str_replace("{category_main_name}", $catName, $templateDesc);
	$templateDesc = str_replace("{category_main_description}", '', $templateDesc);
	$templateDesc = str_replace($templateProduct, $productTmpl, $templateDesc);
	$templateDesc = str_replace("{with_vat}", "", $templateDesc);
	$templateDesc = str_replace("{without_vat}", "", $templateDesc);
	$templateDesc = str_replace("{attribute_price_with_vat}", "", $templateDesc);
	$templateDesc = str_replace("{attribute_price_without_vat}", "", $templateDesc);
	$templateDesc = str_replace("{redproductfinderfilter_formstart}", "", $templateDesc);
	$templateDesc = str_replace("{product_price_slider1}", "", $templateDesc);
	$templateDesc = str_replace("{redproductfinderfilter_formend}", "", $templateDesc);
	$templateDesc = str_replace("{redproductfinderfilter:rp_myfilter}", "", $templateDesc);

	/** todo: trigger plugin for content redshop**/
	$templateDesc = $redTemplate->parseredSHOPplugin($templateDesc);

	$templateDesc = $texts->replace_texts($templateDesc);
}

echo $templateDesc;
