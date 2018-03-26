<?php

class rsCarthelper extends rsCarthelperDefault{
	public function Getstock_extrafield($id = 'NULL', $section_id = 1)
	{
		$extraField = new extraField;
		$cart       = $this->_session->get('cart');
		$product_id = $cart[$id]['product_id'];
		$row_data   = $extraField->getSectionFieldList($section_id, 1, 0);

		$resultArr = array();

		for ($j = 0; $j < count($row_data); $j++)
		{
			$main_result = $extraField->getSectionFieldDataList($row_data[$j]->field_id, $section_id, $product_id);

			if (isset($main_result->data_txt) && isset($row_data[$j]->display_in_checkout))
			{
				if ($main_result->data_txt != "" && 1 == $row_data[$j]->display_in_checkout)
				{
					$title = str_replace(' ', '', strtolower($main_result->field_title));
					if ($title == 'lowinstock') {
						$resultArr[] = $main_result->data_txt;
						break;
					}
				}
			}
		}

		$resultstr = "";

		if (count($resultArr) > 0)
		{
			$resultstr = implode("<br/>", $resultArr);
		}

		return $resultstr;
	}
	public function GetProdcutfield_order_instock($orderitemid = 'NULL', $section_id = 1)
	{
		$extraField      = new extraField;
		$order_functions = new order_functions;
		$orderItem       = $order_functions->getOrderItemDetail(0, 0, $orderitemid);

		$product_id = $orderItem[0]->product_id;

		$row_data = $extraField->getSectionFieldList($section_id, 1, 0);

		$resultArr = array();

		for ($j = 0; $j < count($row_data); $j++)
		{
			$main_result = $extraField->getSectionFieldDataList($row_data[$j]->field_id, $section_id, $product_id);

			if (isset($main_result->data_txt) && isset($row_data[$j]->display_in_checkout))
			{
				if ($main_result->data_txt != "" && 1 == $row_data[$j]->display_in_checkout)
				{
					$title = str_replace(' ', '', strtolower($main_result->field_title));

					if ($title == 'lowinstock') {
						$resultArr[] = $main_result->data_txt;
						break;
					}
				}
			}
		}

		$resultstr = "";

		if (count($resultArr) > 0)
		{
			$resultstr = implode("<br/>", $resultArr);
		}

		return $resultstr;
	}
	public function replaceCartItem($data, $cart = array(), $replace_button, $quotation_mode = 0)
	{
		JPluginHelper::importPlugin('redshop_product');
		$dispatcher = JDispatcher::getInstance();
		$prdItemid  = JRequest::getInt('Itemid');
		$option     = JRequest::getVar('option', 'com_redshop');
		$Itemid     = $this->_redhelper->getCheckoutItemid();
		$url        = JURI::base(true);
		$mainview   = JRequest::getVar('view');
		$producthelper = new producthelper;
		$redhelper = new redhelper;

		if ($Itemid == 0)
		{
			$Itemid = JRequest::getInt('Itemid');
		}

		$cart_tr = '';

		$idx        = $cart['idx'];
		$fieldArray = $this->_extraFieldFront->getSectionFieldList(17, 0, 0);

		if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . ADDTOCART_DELETE))
		{
			$delete_img = ADDTOCART_DELETE;
		}
		else
		{
			$delete_img = "defaultcross.jpg";
		}

		for ($i = 0; $i < $idx; $i++)
		{
			$quantity = $cart[$i]['quantity'];

			if (isset($cart[$i]['giftcard_id']) && $cart[$i]['giftcard_id'])
			{
				$giftcard_id  = $cart[$i]['giftcard_id'];
				$giftcardData = $this->_producthelper->getGiftcardData($giftcard_id);
				$link         = JRoute::_('index.php?option=com_redshop&view=giftcard&gid=' . $giftcard_id . '&Itemid=' . $Itemid);
				$reciverInfo = '<div class="reciverInfo">' . JText::_('LIB_REDSHOP_GIFTCARD_RECIVER_NAME_LBL') . ': ' . $cart[$i]['reciver_name']
					. '<br />' . JText::_('LIB_REDSHOP_GIFTCARD_RECIVER_EMAIL_LBL') . ': ' . $cart[$i]['reciver_email'] . '</div>';

				$product_name = "<div  class='product_name'><a href='" . $link . "'>" . $giftcardData->giftcard_name . "</a></div>" . $reciverInfo;

				if (strstr($data, "{product_name_nolink}"))
				{
					$product_name_nolink = "<div  class=\"product_name\">" . $giftcardData->giftcard_name . "</div><" . $reciverInfo;
					$cart_mdata          = str_replace("{product_name_nolink}", $product_name_nolink, $data);

					if (strstr($data, "{product_name}"))
						$cart_mdata = str_replace("{product_name}", "", $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_name}", $product_name, $data);
				}

				$cart_mdata = str_replace("{product_attribute}", '', $cart_mdata);
				$cart_mdata = str_replace("{product_accessory}", '', $cart_mdata);
				$cart_mdata = str_replace("{product_wrapper}", '', $cart_mdata);
				$cart_mdata = str_replace("{product_old_price}", '', $cart_mdata);
				$cart_mdata = str_replace("{vat_info}", '', $cart_mdata);
				$cart_mdata = str_replace("{product_number_lbl}", '', $cart_mdata);
				$cart_mdata = str_replace("{product_number}", '', $cart_mdata);
				$cart_mdata = str_replace("{attribute_price_without_vat}", '', $cart_mdata);
				$cart_mdata = str_replace("{attribute_price_with_vat}", '', $cart_mdata);

				if ($quotation_mode && !SHOW_QUOTATION_PRICE)
				{
					$cart_mdata = str_replace("{product_total_price}", "", $cart_mdata);
					$cart_mdata = str_replace("{product_price}", "", $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_price}", $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price']), $cart_mdata);
					$cart_mdata = str_replace("{product_total_price}", $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price'] * $cart[$i]['quantity'], true), $cart_mdata);
				}

				$cart_mdata     = str_replace("{if product_on_sale}", '', $cart_mdata);
				$cart_mdata     = str_replace("{product_on_sale end if}", '', $cart_mdata);

				$thumbUrl = RedShopHelperImages::getImagePath(
					$giftcardData->giftcard_image,
					'',
					'thumb',
					'giftcard',
					CART_THUMB_WIDTH,
					CART_THUMB_HEIGHT,
					USE_IMAGE_SIZE_SWAPPING
				);

				$giftcard_image = "&nbsp;";

				if ($thumbUrl)
				{
					$giftcard_image = "<div  class='giftcard_image'><img src='" . $thumbUrl . "'></div>";
				}

				$cart_mdata     = str_replace("{product_thumb_image}", $giftcard_image, $cart_mdata);
				$user_fields    = $this->_producthelper->GetProdcutUserfield($i, 13);
				$cart_mdata     = str_replace("{product_userfields}", $user_fields, $cart_mdata);
				$cart_mdata     = str_replace("{product_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price']), $cart_mdata);
				$cart_mdata     = str_replace("{product_total_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price'] * $cart[$i]['quantity']), $cart_mdata);
				$cart_mdata     = str_replace("{attribute_change}", '', $cart_mdata);
				$cart_mdata     = str_replace("{product_attribute_price}", "", $cart_mdata);
				$cart_mdata     = str_replace("{product_attribute_number}", "", $cart_mdata);
				$cart_mdata     = str_replace("{product_tax}", "", $cart_mdata);

				// ProductFinderDatepicker Extra Field
				$cart_mdata = $this->_producthelper->getProductFinderDatepickerValue($cart_mdata, $giftcard_id, $fieldArray, $giftcard = 1);

				$remove_product = '<form style="" class="rs_hiddenupdatecart" name="delete_cart' . $i . '" method="POST" >
				<input type="hidden" name="giftcard_id" value="' . $cart[$i]['giftcard_id'] . '">
				<input type="hidden" name="cart_index" value="' . $i . '">
				<input type="hidden" name="task" value="">
				<input type="hidden" name="Itemid" value="' . $Itemid . '">
				<img class="delete_cart" src="' . REDSHOP_FRONT_IMAGES_ABSPATH . $delete_img
					. '" title="' . JText::_('COM_REDSHOP_DELETE_PRODUCT_FROM_CART_LBL')
					. '" alt="' . JText::_('COM_REDSHOP_DELETE_PRODUCT_FROM_CART_LBL')
					. '" onclick="document.delete_cart' . $i . '.task.value=\'delete\';document.delete_cart' . $i . '.submit();"></form>';

				if (QUANTITY_TEXT_DISPLAY)
				{
					$cart_mdata = str_replace("{remove_product}", $remove_product, $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{remove_product}", $remove_product, $cart_mdata);
				}

				// Replace attribute tags to empty on giftcard
				if (strstr($cart_mdata, "{product_attribute_loop_start}") && strstr($cart_mdata, "{product_attribute_loop_end}"))
				{
					$templateattibute_sdata  = explode('{product_attribute_loop_start}', $cart_mdata);
					$templateattibute_edata  = explode('{product_attribute_loop_end}', $templateattibute_sdata[1]);
					$templateattibute_middle = $templateattibute_edata[0];

					$cart_mdata = str_replace($templateattibute_middle, "", $cart_mdata);
				}

				$cartItem = 'giftcard_id';
			}
			else
			{
				$product_id     = $cart[$i]['product_id'];
				$product        = $this->_producthelper->getProductById($product_id);
				$retAttArr      = $this->_producthelper->makeAttributeCart($cart [$i] ['cart_attribute'], $product_id, 0, 0, $quantity, $data);
				$cart_attribute = $retAttArr[0];

				$retAccArr      = $this->_producthelper->makeAccessoryCart($cart [$i] ['cart_accessory'], $product_id, $data);
				$cart_accessory = $retAccArr[0];

				$ItemData = $this->_producthelper->getMenuInformation(0, 0, '', 'product&pid=' . $product_id);

				if (count($ItemData) > 0)
				{
					$Itemid = $ItemData->id;
				}
				else
				{
					$Itemid = $this->_redhelper->getItemid($product_id);
				}

				$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $product_id . '&Itemid=' . $Itemid);

				$pname         = $product->product_name;
				$product_name  = "<div  class='product_name'><a href='" . $link . "'>" . $pname . "</a></div>";
				$product_image = "";
				$prd_image     = '';
				$type          = 'product';

				if (WANT_TO_SHOW_ATTRIBUTE_IMAGE_INCART && isset($cart[$i]['hidden_attribute_cartimage']))
				{
					$image_path    = REDSHOP_FRONT_IMAGES_ABSPATH;
					$product_image = str_replace($image_path, '', $cart[$i]['hidden_attribute_cartimage']);
				}

				if ($product_image && is_file(REDSHOP_FRONT_IMAGES_RELPATH . $product_image))
				{
					$val        = explode("/", $product_image);
					$prd_image  = $val[1];
					$type       = $val[0];
				}
				elseif ($product->product_full_image && is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . $product->product_full_image))
				{
					$prd_image = $product->product_full_image;
					$type      = 'product';
				}
				elseif (is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . PRODUCT_DEFAULT_IMAGE))
				{
					$prd_image = PRODUCT_DEFAULT_IMAGE;
					$type      = 'product';
				}

				$isAttributeImage = false;

				if (isset($cart[$i]['attributeImage']))
				{
					$isAttributeImage = is_file(REDSHOP_FRONT_IMAGES_RELPATH . "mergeImages/" . $cart[$i]['attributeImage']);
				}

				if ($isAttributeImage)
				{
					$prd_image = $cart[$i]['attributeImage'];
					$type      = 'mergeImages';
				}

				if ($prd_image !== '')
				{
					$redhelper = new redhelper;

					if (WATERMARK_CART_THUMB_IMAGE && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . WATERMARK_IMAGE))
					{
						$product_cart_img = $redhelper->watermark($type, $prd_image, CART_THUMB_WIDTH, CART_THUMB_HEIGHT, WATERMARK_CART_THUMB_IMAGE, '0');

						$product_image = "<div  class='product_image'><img src='" . $product_cart_img . "'></div>";
					}
					else
					{
						$thumbUrl = RedShopHelperImages::getImagePath(
								$prd_image,
								'',
								'thumb',
								$type,
								CART_THUMB_WIDTH,
								CART_THUMB_HEIGHT,
								USE_IMAGE_SIZE_SWAPPING
							);

						$product_image = "<div  class='product_image'><img src='" . $thumbUrl . "'></div>";
					}
				}
				else
				{
					$product_image = "<div  class='product_image'></div>";
				}

				// Trigger to change product image.
				$dispatcher->trigger('OnSetCartOrderItemImage', array(&$cart, &$product_image, $product, $i));

				$chktag              = $this->_producthelper->getApplyVatOrNot($data);
				$product_total_price = "<div class='product_price'>";

				if (!$quotation_mode || ($quotation_mode && SHOW_QUOTATION_PRICE))
				{
					if (!$chktag)
					{
						$product_total_price .= $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price_excl_vat'] * $quantity);
					}
					else
					{
						$product_total_price .= $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price'] * $quantity);
					}
				}

				$product_total_price .= "</div>";

				$product_old_price = "";
				$product_price     = "<div class='product_price'>";

				if (!$quotation_mode || ($quotation_mode && SHOW_QUOTATION_PRICE))
				{
					if (!$chktag)
					{
						$product_price .= $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price_excl_vat'], true);
					}
					else
					{
						$product_price .= $this->_producthelper->getProductFormattedPrice($cart[$i]['product_price'], true);
					}

					if (isset($cart[$i]['product_old_price']))
					{
						$product_old_price = $cart[$i]['product_old_price'];

						if (!$chktag)
						{
							$product_old_price = $cart[$i]['product_old_price_excl_vat'];
						}

						// Set Product Old Price without format
						$productOldPriceNoFormat = $product_old_price;

						$product_old_price = $this->_producthelper->getProductFormattedPrice($product_old_price, true);
					}
				}

				$product_price .= "</div>";

				$wrapper_name = "";

				if ((array_key_exists('wrapper_id', $cart[$i])) && $cart[$i]['wrapper_id'])
				{
					$wrapper = $this->_producthelper->getWrapper($product_id, $cart[$i]['wrapper_id']);

					if (count($wrapper) > 0)
					{
						$wrapper_name = JText::_('COM_REDSHOP_WRAPPER') . ": " . $wrapper[0]->wrapper_name;

						if (!$quotation_mode || ($quotation_mode && SHOW_QUOTATION_PRICE))
						{
							$wrapper_name .= "(" . $this->_producthelper->getProductFormattedPrice($cart[$i]['wrapper_price'], true) . ")";
						}
					}
				}

				$cart_mdata = '';

				if (strstr($data, "{product_name_nolink}"))
				{
					$product_name_nolink = "";
					$product_name_nolink = "<div  class='product_name'>$product->product_name</a></div>";
					$cart_mdata          = str_replace("{product_name_nolink}", $product_name_nolink, $data);

					if (strstr($data, "{product_name}"))
						$cart_mdata = str_replace("{product_name}", "", $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_name}", $product_name, $data);
				}

				if (strstr($cart_mdata, "{manufacturer_image}"))
				{

					$manufacturer = $producthelper->getProductById($cart[$i]['product_id']);
					$manufacturerid = $manufacturer->manufacturer_id;

					$mh_thumb    = MANUFACTURER_THUMB_HEIGHT;
					$mw_thumb    = MANUFACTURER_THUMB_WIDTH;
					$thum_image  = "";
					$media_image = $producthelper->getAdditionMediaImage($manufacturerid, "manufacturer");
					$m           = 0;

					if ($media_image[$m]->media_name && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . "manufacturer/" . $media_image[$m]->media_name))
					{
						$wimg      = $redhelper->watermark('manufacturer', $media_image[$m]->media_name, $mw_thumb, $mh_thumb, WATERMARK_MANUFACTURER_THUMB_IMAGE, '0');
						$linkimage = $redhelper->watermark('manufacturer', $media_image[$m]->media_name, '', '', WATERMARK_MANUFACTURER_IMAGE, '0');

						$altText = $producthelper->getAltText('manufacturer', $manufacturerid);

						$thum_image = "<img alt='" . $altText . "' title='" . $altText . "' src='" . $wimg . "'>";
					}

					$cart_mdata = str_replace("{manufacturer_image}", $thum_image, $cart_mdata);
				}

				$cart_mdata = str_replace("{product_s_desc}", $product->product_s_desc, $cart_mdata);

				// Replace Attribute data
				if (strstr($cart_mdata, "{product_attribute_loop_start}") && strstr($cart_mdata, "{product_attribute_loop_end}"))
				{
					$templateattibute_sdata  = explode('{product_attribute_loop_start}', $cart_mdata);
					$templateattibute_start  = $templateattibute_sdata[0];
					$templateattibute_edata  = explode('{product_attribute_loop_end}', $templateattibute_sdata[1]);
					$templateattibute_end    = $templateattibute_edata[1];
					$templateattibute_middle = $templateattibute_edata[0];
					$pro_detail              = '';
					$sum_total               = count($cart[$i]['cart_attribute']);
					$temp_tpi                = $cart[$i]['cart_attribute'];

					if ($sum_total > 0)
					{
						$propertyCalculatedPriceSum = $productOldPriceNoFormat;

						for ($tpi = 0; $tpi < $sum_total; $tpi++)
						{
							$product_attribute_name        = "";
							$product_attribute_value       = "";
							$product_attribute_value_price = "";
							$product_attribute_name        = $temp_tpi[$tpi]['attribute_name'];

							if (count($temp_tpi[$tpi]['attribute_childs']) > 0)
							{
								$product_attribute_value = ": " . $temp_tpi[$tpi]['attribute_childs'][0]['property_name'];

								if (count($temp_tpi[$tpi]['attribute_childs'][0]['property_childs']) > 0)
								{
									$product_attribute_value .= ": " . $temp_tpi[$tpi]['attribute_childs'][0]['property_childs'][0]['subattribute_color_title'] . ": " . $temp_tpi[$tpi]['attribute_childs'][0]['property_childs'][0]['subproperty_name'];
								}

								$product_attribute_value_price = $temp_tpi[$tpi]['attribute_childs'][0]['property_price'];
								$propertyOperand               = $temp_tpi[$tpi]['attribute_childs'][0]['property_oprand'];

								if (count($temp_tpi[$tpi]['attribute_childs'][0]['property_childs']) > 0)
								{
									$product_attribute_value_price = $product_attribute_value_price + $temp_tpi[$tpi]['attribute_childs'][0]['property_childs'][0]['subproperty_price'];
									$propertyOperand               = $temp_tpi[$tpi]['attribute_childs'][0]['property_childs'][0]['subproperty_oprand'];
								}

								// Show actual productive price
								if ($product_attribute_value_price > 0)
								{
									$productAttributeCalculatedPriceBase = redhelper::setOperandForValues($propertyCalculatedPriceSum, $propertyOperand, $product_attribute_value_price);

									$productAttributeCalculatedPrice = $productAttributeCalculatedPriceBase - $propertyCalculatedPriceSum;
									$propertyCalculatedPriceSum      = $productAttributeCalculatedPriceBase;
								}

								$product_attribute_value_price = $this->_producthelper->getProductFormattedPrice($product_attribute_value_price);
							}

							$productAttributeCalculatedPrice = $this->_producthelper->getProductFormattedPrice(
								$productAttributeCalculatedPrice
							);
							$productAttributeCalculatedPrice = JText::sprintf('COM_REDSHOP_CART_PRODUCT_ATTRIBUTE_CALCULATED_PRICE', $productAttributeCalculatedPrice);

							$data_add_pro = $templateattibute_middle;
							$data_add_pro = str_replace("{product_attribute_name}", $product_attribute_name, $data_add_pro);
							$data_add_pro = str_replace("{product_attribute_value}", $product_attribute_value, $data_add_pro);
							$data_add_pro = str_replace("{product_attribute_value_price}", $product_attribute_value_price, $data_add_pro);
							$data_add_pro = str_replace(
								"{product_attribute_calculated_price}",
								$productAttributeCalculatedPrice,
								$data_add_pro
							);
							$pro_detail .= $data_add_pro;
						}
					}

					$cart_mdata = str_replace($templateattibute_middle, $pro_detail, $cart_mdata);
				}

				if (count($cart [$i] ['cart_attribute']) > 0)
				{
					$cart_mdata = str_replace("{attribute_label}", JText::_("COM_REDSHOP_ATTRIBUTE"), $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{attribute_label}", "", $cart_mdata);
				}

				$cart_mdata           = str_replace("{product_number}", $product->product_number, $cart_mdata);
				$cart_mdata           = str_replace("{product_vat}", $cart[$i]['product_vat'] * $cart[$i]['quantity'], $cart_mdata);
				$user_fields          = $this->_producthelper->GetProdcutUserfield($i);
				$cart_mdata           = str_replace("{product_userfields}", $user_fields, $cart_mdata);
				$user_custom_fields   = $this->_producthelper->GetProdcutfield($i);
				$cart_mdata           = str_replace("{product_customfields}", $user_custom_fields, $cart_mdata);
				$cart_mdata           = str_replace("{product_customfields_lbl}", JText::_("COM_REDSHOP_PRODUCT_CUSTOM_FIELD"), $cart_mdata);
				$user_custom_fields_instock   = $this->Getstock_extrafield($i);
				$cart_mdata           = str_replace("{product_customfields_instock}", $user_custom_fields_instock, $cart_mdata);
				$discount_calc_output = (isset($cart[$i]['discount_calc_output']) && $cart[$i]['discount_calc_output']) ? $cart[$i]['discount_calc_output'] . "<br />" : "";
				$cart_mdata           = str_replace("{product_attribute}", $discount_calc_output . $cart_attribute, $cart_mdata);
				$cart_mdata           = str_replace("{product_accessory}", $cart_accessory, $cart_mdata);
				$cart_mdata           = str_replace("{product_attribute_price}", "", $cart_mdata);
				$cart_mdata           = str_replace("{product_attribute_number}", "", $cart_mdata);
				$cart_mdata           = $this->_producthelper->getProductOnSaleComment($product, $cart_mdata, $product_old_price);
				$cart_mdata           = str_replace("{product_old_price}", $product_old_price, $cart_mdata);
				$cart_mdata           = str_replace("{product_wrapper}", $wrapper_name, $cart_mdata);
				$cart_mdata           = str_replace("{product_thumb_image}", $product_image, $cart_mdata);
				$cart_mdata           = str_replace("{attribute_price_without_vat}", '', $cart_mdata);
				$cart_mdata           = str_replace("{attribute_price_with_vat}", '', $cart_mdata);

				// ProductFinderDatepicker Extra Field Start
				$cart_mdata = $this->_producthelper->getProductFinderDatepickerValue($cart_mdata, $product_id, $fieldArray);

				$product_price_excl_vat = $cart[$i]['product_price_excl_vat'];

				if (!$quotation_mode || ($quotation_mode && SHOW_QUOTATION_PRICE))
				{
					$cart_mdata = str_replace("{product_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($product_price_excl_vat), $cart_mdata);
					$cart_mdata = str_replace("{product_total_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($product_price_excl_vat * $quantity), $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_price_excl_vat}", "", $cart_mdata);
					$cart_mdata = str_replace("{product_total_price_excl_vat}", "", $cart_mdata);
				}

				// $cart[$i]['product_price_excl_vat'] = $product_price_excl_vat;
				$this->_session->set('cart', $cart);

				if ($product->product_type == 'subscription')
				{
					$subscription_detail   = $this->_producthelper->getProductSubscriptionDetail($product->product_id, $cart[$i]['subscription_id']);
					$selected_subscription = $subscription_detail->subscription_period . " " . $subscription_detail->period_type;
					$cart_mdata            = str_replace("{product_subscription_lbl}", JText::_('COM_REDSHOP_SUBSCRIPTION'), $cart_mdata);
					$cart_mdata            = str_replace("{product_subscription}", $selected_subscription, $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_subscription_lbl}", "", $cart_mdata);
					$cart_mdata = str_replace("{product_subscription}", "", $cart_mdata);
				}

				if ($replace_button)
				{
					$update_attribute = '';

					if ($mainview == 'cart')
					{
						$attchange        = JURI::root() . 'index.php?option=com_redshop&view=cart&layout=change_attribute&tmpl=component&pid=' . $product_id . '&cart_index=' . $i;
						$update_attribute = '<a class="modal" rel="{handler: \'iframe\', size: {x: 550, y: 400}}" href="' . $attchange . '">' . JText::_('COM_REDSHOP_CHANGE_ATTRIBUTE') . '</a>';
					}

					if ($cart_attribute != "")
					{
						$cart_mdata = str_replace("{attribute_change}", $update_attribute, $cart_mdata);
					}
					else
					{
						$cart_mdata = str_replace("{attribute_change}", "", $cart_mdata);
					}
				}
				else
				{
					$cart_mdata = str_replace("{attribute_change}", '', $cart_mdata);
				}

				$cartItem = 'product_id';
				$cart_mdata = $this->_producthelper->replaceVatinfo($cart_mdata);
				$cart_mdata = str_replace("{product_price}", $product_price, $cart_mdata);
				$cart_mdata = str_replace("{product_total_price}", $product_total_price, $cart_mdata);
			}

			if ($replace_button)
			{
				$update_cart_none = '<label>' . $quantity . '</label>';

				$update_img = '';

				if ($mainview == 'checkout')
				{
					$update_cart = $quantity;
				}
				else
				{
					$update_cart = '<form name="update_cart' . $i . '" method="POST" >';
					$update_cart .= '<input class="inputbox input-mini" type="text" value="' . $quantity . '" name="quantity" id="quantitybox' . $i . '" size="' . DEFAULT_QUANTITY . '" maxlength="' . DEFAULT_QUANTITY . '" onchange="validateInputNumber(this.id);">';
					$update_cart .= '<ul id="quantitylist' . $i . '" datatoggle="quantitybox' . $i . '">';

					for ($k = 1; $k < 11; $k++)
					{
						$update_cart .= '<li><a href="javascript:void(0)" onclick="document.update_cart' . $i . '.quantitybox' . $i . '.value=' . $k . ';if (validateInputNumber(this.id)){document.update_cart' . $i . '.task.value=\'update\';document.update_cart' . $i . '.submit();}">' . $k . '</a></li>';
					}

					$update_cart .= '</ul>';
					$update_cart .= '<input type="hidden" name="' . $cartItem . '" value="' . ${$cartItem} . '">
								<input type="hidden" name="cart_index" value="' . $i . '">
								<input type="hidden" name="Itemid" value="' . $Itemid . '">
								<input type="hidden" name="task" value="">';

					if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . ADDTOCART_UPDATE))
					{
						$update_img = ADDTOCART_UPDATE;
					}
					else
					{
						$update_img = "defaultupdate.jpg";
					}

					$update_cart .= '<img class="update_cart" src="' . REDSHOP_FRONT_IMAGES_ABSPATH . $update_img . '" title="' . JText::_('COM_REDSHOP_UPDATE_PRODUCT_FROM_CART_LBL') . '" alt="' . JText::_('COM_REDSHOP_UPDATE_PRODUCT_FROM_CART_LBL') . '" onclick="document.update_cart' . $i . '.task.value=\'update\';document.update_cart' . $i . '.submit();">';

					$update_cart .= '</form>';
				}

				$update_cart_minus_plus = '<form name="update_cart' . $i . '" method="POST">';

				$update_cart_minus_plus .= '<input type="text" id="quantitybox' . $i . '" name="quantity"  size="1"  value="' . $quantity . '" /><input type="button" id="minus" value="-"
						onClick="quantity.value = (quantity.value) ; var qty1 = quantity.value; if( !isNaN( qty1 ) &amp;&amp; qty1 > 1 ) quantity.value--;return false;">';

				$update_cart_minus_plus .= '<input type="button" value="+"
						onClick="quantity.value = (+quantity.value+1)"><input type="hidden" name="' . $cartItem . '" value="' . ${$cartItem} . '">
						<input type="hidden" name="cart_index" value="' . $i . '">
						<input type="hidden" name="Itemid" value="' . $Itemid . '">
						<input type="hidden" name="task" value=""><img class="update_cart" src="' . REDSHOP_FRONT_IMAGES_ABSPATH . $update_img . '" title="' . JText::_('COM_REDSHOP_UPDATE_PRODUCT_FROM_CART_LBL') . '" alt="' . JText::_('COM_REDSHOP_UPDATE_PRODUCT_FROM_CART_LBL') . '" onclick="document.update_cart' . $i . '.task.value=\'update\';document.update_cart' . $i . '.submit();">
						</form>';

				if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . ADDTOCART_DELETE))
				{
					$delete_img = ADDTOCART_DELETE;
				}
				else
				{
					$delete_img = "defaultcross.jpg";
				}

				if ($mainview == 'checkout')
				{
					$remove_product = '';
				}
				else
				{
					$remove_product = '<form style="padding:0px;margin:0px;" name="delete_cart' . $i . '" method="POST" >
							<input type="hidden" name="' . $cartItem . '" value="' . ${$cartItem} . '">
							<input type="hidden" name="cart_index" value="' . $i . '">
							<input type="hidden" name="task" value="">
							<input type="hidden" name="Itemid" value="' . $Itemid . '">
							<img class="delete_cart" src="' . REDSHOP_FRONT_IMAGES_ABSPATH . $delete_img . '" title="' . JText::_('COM_REDSHOP_DELETE_PRODUCT_FROM_CART_LBL') . '" alt="' . JText::_('COM_REDSHOP_DELETE_PRODUCT_FROM_CART_LBL') . '" onclick="document.delete_cart' . $i . '.task.value=\'delete\';document.delete_cart' . $i . '.submit();"></form>';
				}

				if (QUANTITY_TEXT_DISPLAY)
				{
					if (strstr($cart_mdata, "{quantity_increase_decrease}") && $mainview == 'cart')
					{
						$cart_mdata = str_replace("{quantity_increase_decrease}", $update_cart_minus_plus, $cart_mdata);
						$cart_mdata = str_replace("{update_cart}", '', $cart_mdata);
					}
					else
					{
						$cart_mdata = str_replace("{quantity_increase_decrease}", $update_cart, $cart_mdata);
						$cart_mdata = str_replace("{update_cart}", $update_cart, $cart_mdata);
					}

					$cart_mdata = str_replace("{remove_product}", $remove_product, $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{quantity_increase_decrease}", $update_cart_minus_plus, $cart_mdata);
					$cart_mdata = str_replace("{update_cart}", $update_cart_none, $cart_mdata);
					$cart_mdata = str_replace("{remove_product}", $remove_product, $cart_mdata);
				}
			}
			else
			{
				$cart_mdata = str_replace("{update_cart}", $quantity, $cart_mdata);
				$cart_mdata = str_replace("{remove_product}", '', $cart_mdata);
			}

			// Plugin support:  Process the product plugin for cart item
			$dispatcher->trigger('onCartItemDisplay', array(& $cart_mdata, $cart, $i));

			$cart_tr .= $cart_mdata;
		}

		return $cart_tr;
	}

	public function repalceOrderItems($data, $rowitem = array())
	{
		JPluginHelper::importPlugin('redshop_product');
		$dispatcher = JDispatcher::getInstance();
		$mainview   = JRequest::getVar('view');
		$fieldArray = $this->_extraFieldFront->getSectionFieldList(17, 0, 0);
		$producthelper = new producthelper;
		$redhelper = new redhelper;

		$subtotal_excl_vat = 0;
		$cart              = '';
		$url               = JURI::root();
		$returnArr         = array();

		$wrapper_name = "";

		$OrdersDetail = $this->_order_functions->getOrderDetails($rowitem [0]->order_id);

		for ($i = 0; $i < count($rowitem); $i++)
		{
			$product_id = $rowitem [$i]->product_id;
			$quantity   = $rowitem [$i]->product_quantity;

			if ($rowitem [$i]->is_giftcard)
			{
				$giftcardData      = $this->_producthelper->getGiftcardData($product_id);
				$product_name      = $giftcardData->giftcard_name;
				$userfield_section = 13;
				$product = new stdClass;
			}
			else
			{
				$product           = $this->_producthelper->getProductById($product_id);
				$product_name      = $product->product_name;
				$userfield_section = 12;
				$giftcardData = new stdClass;
			}

			$dirname = JPATH_COMPONENT_SITE . "/assets/images/orderMergeImages/" . $rowitem [$i]->attribute_image;

			if (is_file($dirname))
			{
				$attribute_image_path = RedShopHelperImages::getImagePath(
											$rowitem[$i]->attribute_image,
											'',
											'thumb',
											'orderMergeImages',
											CART_THUMB_WIDTH,
											CART_THUMB_HEIGHT,
											USE_IMAGE_SIZE_SWAPPING
										);
				$attrib_img = '<img src="' . $attribute_image_path . '">';
			}
			else
			{
				if (is_file(JPATH_COMPONENT_SITE . "/assets/images/product_attributes/" . $rowitem [$i]->attribute_image))
				{
					$attribute_image_path = RedShopHelperImages::getImagePath(
												$rowitem[$i]->attribute_image,
												'',
												'thumb',
												'product_attributes',
												CART_THUMB_WIDTH,
												CART_THUMB_HEIGHT,
												USE_IMAGE_SIZE_SWAPPING
											);
					$attrib_img = '<img src="' . $attribute_image_path . '">';
				}
				else
				{
					if ($rowitem [$i]->is_giftcard)
					{
						$product_full_image = $giftcardData->giftcard_image;
						$product_type = 'giftcard';
					}
					else
					{
						$product_full_image = $product->product_full_image;
						$product_type = 'product';
					}

					if ($product_full_image)
					{
						if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . $product_type . "/" . $product_full_image))
						{
							$attribute_image_path = RedShopHelperImages::getImagePath(
														$product_full_image,
														'',
														'thumb',
														$product_type,
														CART_THUMB_WIDTH,
														CART_THUMB_HEIGHT,
														USE_IMAGE_SIZE_SWAPPING
													);
							$attrib_img = '<img src="' . $attribute_image_path . '">';
						}
						else
						{
							if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . PRODUCT_DEFAULT_IMAGE))
							{
								$attribute_image_path = RedShopHelperImages::getImagePath(
															PRODUCT_DEFAULT_IMAGE,
															'',
															'thumb',
															'product',
															CART_THUMB_WIDTH,
															CART_THUMB_HEIGHT,
															USE_IMAGE_SIZE_SWAPPING
														);
								$attrib_img = '<img src="' . $attribute_image_path . '">';
							}
						}
					}
					else
					{
						if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . PRODUCT_DEFAULT_IMAGE))
						{
							$attribute_image_path = RedShopHelperImages::getImagePath(
														PRODUCT_DEFAULT_IMAGE,
														'',
														'thumb',
														'product',
														CART_THUMB_WIDTH,
														CART_THUMB_HEIGHT,
														USE_IMAGE_SIZE_SWAPPING
													);
							$attrib_img = '<img src="' . $attribute_image_path . '">';
						}
					}
				}
			}

			$product_name        = "<div class='product_name'>" . $product_name . "</div>";
			$product_total_price = "<div class='product_price'>";

			if (!$this->_producthelper->getApplyVatOrNot($data))
			{
				$product_total_price .= $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price_excl_vat * $quantity);
			}
			else
			{
				$product_total_price .= $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price * $quantity);
			}

			$product_total_price .= "</div>";

			$product_price = "<div class='product_price'>";

			if (!$this->_producthelper->getApplyVatOrNot($data))
			{
				$product_price .= $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price_excl_vat);
			}
			else
			{
				$product_price .= $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price);
			}

			$product_price .= "</div>";

			$product_old_price = $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_old_price);

			$product_quantity = '<div class="update_cart">' . $quantity . '</div>';

			if ($rowitem [$i]->wrapper_id)
			{
				$wrapper = $this->_producthelper->getWrapper($product_id, $rowitem [$i]->wrapper_id);

				if (count($wrapper) > 0)
				{
					$wrapper_name = $wrapper [0]->wrapper_name;
				}

				$wrapper_price = $this->_producthelper->getProductFormattedPrice($rowitem [$i]->wrapper_price);
				$wrapper_name  = JText::_('COM_REDSHOP_WRAPPER') . ": " . $wrapper_name . "(" . $wrapper_price . ")";
			}

			$cart_mdata = str_replace("{product_name}", $product_name, $data);

			if (strstr($cart_mdata, "{manufacturer_image}"))
			{

				$manufacturer = $this->_producthelper->getProductById($product_id);
				$manufacturerid = $product->manufacturer_id;

				$mh_thumb    = MANUFACTURER_THUMB_HEIGHT;
				$mw_thumb    = MANUFACTURER_THUMB_WIDTH;
				$thum_image  = "";
				$media_image = $producthelper->getAdditionMediaImage($manufacturerid, "manufacturer");
				$m           = 0;

				if ($media_image[$m]->media_name && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . "manufacturer/" . $media_image[$m]->media_name))
				{
					$wimg      = $redhelper->watermark('manufacturer', $media_image[$m]->media_name, $mw_thumb, $mh_thumb, WATERMARK_MANUFACTURER_THUMB_IMAGE, '0');
					$linkimage = $redhelper->watermark('manufacturer', $media_image[$m]->media_name, '', '', WATERMARK_MANUFACTURER_IMAGE, '0');

					$altText = $producthelper->getAltText('manufacturer', $manufacturerid);

					$thum_image = "<img alt='" . $altText . "' title='" . $altText . "' src='" . $wimg . "'>";
				}

				$cart_mdata = str_replace("{manufacturer_image}", $thum_image, $cart_mdata);
			}

			$catId = $this->_producthelper->getCategoryProduct($product_id);
			$res   = $this->_producthelper->getSection("category", $catId);

			if (count($res) > 0)
			{
				$cname = $res->category_name;
				$clink = JRoute::_($url . 'index.php?option=com_redshop&view=category&layout=detail&cid=' . $catId);
				$category_path = "<a href='" . $clink . "'>" . $cname . "</a>";
			}
			else
			{
				$category_path = '';
			}

			$cart_mdata    = str_replace("{category_name}", $category_path, $cart_mdata);

			$cart_mdata = $this->_producthelper->replaceVatinfo($cart_mdata);

			$product_note = "<div class='product_note'>" . $wrapper_name . "</div>";

			$cart_mdata = str_replace("{product_wrapper}", $product_note, $cart_mdata);

			// Make attribute order template output
			$attribute_data = $this->_producthelper->makeAttributeOrder($rowitem[$i]->order_item_id, 0, $product_id, 0, 0, $data);

			// Assign template output into {product_attribute} tag
			$cart_mdata = str_replace("{product_attribute}", $attribute_data->product_attribute, $cart_mdata);

			// Assign template output into {attribute_middle_template} tag
			$cart_mdata = str_replace($attribute_data->attribute_middle_template_core, $attribute_data->attribute_middle_template, $cart_mdata);

			if (strstr($cart_mdata, '{remove_product_attribute_title}'))
			{
				$cart_mdata = str_replace("{remove_product_attribute_title}", "", $cart_mdata);
			}

			if (strstr($cart_mdata, '{remove_product_subattribute_title}'))
			{
				$cart_mdata = str_replace("{remove_product_subattribute_title}", "", $cart_mdata);
			}

			if (strstr($cart_mdata, '{product_attribute_number}'))
			{
				$cart_mdata = str_replace("{product_attribute_number}", "", $cart_mdata);
			}

			$cart_mdata = str_replace("{product_accessory}", $this->_producthelper->makeAccessoryOrder($rowitem [$i]->order_item_id), $cart_mdata);

			$product_userfields = $this->_producthelper->getuserfield($rowitem [$i]->order_item_id, $userfield_section);

			$cart_mdata = str_replace("{product_userfields}", $product_userfields, $cart_mdata);

			$user_custom_fields = $this->_producthelper->GetProdcutfield_order($rowitem [$i]->order_item_id);
			$cart_mdata         = str_replace("{product_customfields}", $user_custom_fields, $cart_mdata);
			$cart_mdata         = str_replace("{product_customfields_lbl}", JText::_("COM_REDSHOP_PRODUCT_CUSTOM_FIELD"), $cart_mdata);
			$user_custom_fields_instock = $this->GetProdcutfield_order_instock($rowitem [$i]->order_item_id);
			$cart_mdata         = str_replace("{product_customfields_instock}", $user_custom_fields_instock, $cart_mdata);

			if ($rowitem [$i]->is_giftcard)
			{
				$cart_mdata = str_replace(
					array('{product_sku}', '{product_number}', '{product_s_desc}', '{product_subscription}', '{product_subscription_lbl}'),
					'', $cart_mdata);
			}
			else
			{
				$cart_mdata = str_replace("{product_sku}", $product->product_number, $cart_mdata);
				$cart_mdata = str_replace("{product_number}", $product->product_number, $cart_mdata);
				$cart_mdata = str_replace("{product_s_desc}", $product->product_s_desc, $cart_mdata);

				if ($product->product_type == 'subscription')
				{
					$user_subscribe_detail = $this->_producthelper->getUserProductSubscriptionDetail($rowitem[$i]->order_item_id);
					$subscription_detail   = $this->_producthelper->getProductSubscriptionDetail($product->product_id, $user_subscribe_detail->subscription_id);
					$selected_subscription = $subscription_detail->subscription_period . " " . $subscription_detail->period_type;

					$cart_mdata = str_replace("{product_subscription_lbl}", JText::_('COM_REDSHOP_SUBSCRIPTION'), $cart_mdata);
					$cart_mdata = str_replace("{product_subscription}", $selected_subscription, $cart_mdata);
				}
				else
				{
					$cart_mdata = str_replace("{product_subscription_lbl}", "", $cart_mdata);
					$cart_mdata = str_replace("{product_subscription}", "", $cart_mdata);
				}
			}

			$cart_mdata = str_replace("{product_number_lbl}", JText::_('COM_REDSHOP_PRODUCT_NUMBER'), $cart_mdata);

			$product_vat = ($rowitem [$i]->product_item_price - $rowitem [$i]->product_item_price_excl_vat) * $rowitem [$i]->product_quantity;

			$cart_mdata = str_replace("{product_vat}", $product_vat, $cart_mdata);

			$cart_mdata = $this->_producthelper->getProductOnSaleComment($product, $cart_mdata);

			$cart_mdata = str_replace("{attribute_price_without_vat}", '', $cart_mdata);

			$cart_mdata = str_replace("{attribute_price_with_vat}", '', $cart_mdata);

			// ProductFinderDatepicker Extra Field Start
			$cart_mdata = $this->_producthelper->getProductFinderDatepickerValue($cart_mdata, $product_id, $fieldArray);

			// Change order item image based on plugin
			$prepareCartAttributes[$i]               = get_object_vars($attribute_data);
			$prepareCartAttributes[$i]['product_id'] = $rowitem[$i]->product_id;

			$dispatcher->trigger(
				'OnSetCartOrderItemImage',
				array(
					&$prepareCartAttributes,
					&$attrib_img,
					$rowitem[$i],
					$i
				)
			);

			$cart_mdata = str_replace(
				"{product_thumb_image}",
				"<div  class='product_image'>" . $attrib_img . "</div>",
				$cart_mdata
			);

			$cart_mdata = str_replace("{product_price}", $product_price, $cart_mdata);

			$cart_mdata = str_replace("{product_old_price}", $product_old_price, $cart_mdata);

			$cart_mdata = str_replace("{product_quantity}", $quantity, $cart_mdata);

			$cart_mdata = str_replace("{product_total_price}", $product_total_price, $cart_mdata);

			$cart_mdata = str_replace("{product_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price_excl_vat), $cart_mdata);

			$cart_mdata = str_replace("{product_total_price_excl_vat}", $this->_producthelper->getProductFormattedPrice($rowitem [$i]->product_item_price_excl_vat * $quantity), $cart_mdata);

			$subtotal_excl_vat += $rowitem [$i]->product_item_price_excl_vat * $quantity;

			if ($mainview == "order_detail")
			{
				$Itemid     = JRequest::getVar('Itemid');
				$Itemid     = $this->_redhelper->getCartItemid();
				$copytocart = "<a href='" . JRoute::_('index.php?option=com_redshop&view=order_detail&task=copyorderitemtocart&order_item_id=' . $rowitem[$i]->order_item_id . '&Itemid=' . $Itemid, false) . "'>";
				$copytocart .= "<img src='" . REDSHOP_ADMIN_IMAGES_ABSPATH . "add.jpg' title='" . JText::_("COM_REDSHOP_COPY_TO_CART") . "' alt='" . JText::_("COM_REDSHOP_COPY_TO_CART") . "' /></a>";
				$cart_mdata = str_replace("{copy_orderitem}", $copytocart, $cart_mdata);
			}
			else
			{
				$cart_mdata = str_replace("{copy_orderitem}", "", $cart_mdata);
			}

			// Get Downloadable Products
			$downloadProducts     = $this->_order_functions->getDownloadProduct($rowitem[$i]->order_id);
			$totalDownloadProduct = count($downloadProducts);

			$dproducts = array();

			for ($t = 0; $t < $totalDownloadProduct; $t++)
			{
				$downloadProduct                                                        = $downloadProducts[$t];
				$dproducts[$downloadProduct->product_id][$downloadProduct->download_id] = $downloadProduct;
			}

			// Get Downloadable Products Logs
			$downloadProductslog     = $this->_order_functions->getDownloadProductLog($rowitem[$i]->order_id);
			$totalDownloadProductlog = count($downloadProductslog);

			$dproductslog = array();

			for ($t = 0; $t < $totalDownloadProductlog; $t++)
			{
				$downloadProductlogs                              = $downloadProductslog[$t];
				$dproductslog[$downloadProductlogs->product_id][] = $downloadProductlogs;
			}

			// Download Product Tag Replace
			if (isset($dproducts[$product_id]) && count($dproducts[$product_id]) > 0 && $OrdersDetail->order_status == "C" && $OrdersDetail->order_payment_status == "Paid")
			{
				$downloadarray = $dproducts[$product_id];
				$dpData        = "<table class='download_token'>";
				$limit         = $dpData;
				$enddate       = $dpData;
				$g             = 1;

				foreach ($downloadarray as $downloads)
				{
					$file_name    = substr(basename($downloads->file_name), 11);
					$product_name = $downloadProduct->product_name;
					$download_id  = $downloads->download_id;
					$download_max = $downloads->download_max;
					$end_date     = $downloads->end_date;
					$mailtoken    = "<a href='" . JURI::root() . "index.php?option=com_redshop&view=product&layout=downloadproduct&tid=" . $download_id . "'>" . $file_name . "</a>";
					$dpData .= "</tr>";
					$dpData .= "<td>(" . $g . ") " . $product_name . ": " . $mailtoken . "</td>";
					$dpData .= "</tr>";
					$limit .= "</tr>";
					$limit .= "<td>(" . $g . ") " . $download_max . "</td>";
					$limit .= "</tr>";
					$enddate .= "</tr>";
					$enddate .= "<td>(" . $g . ") " . date("d-m-Y H:i", $end_date) . "</td>";
					$enddate .= "</tr>";
					$g++;
				}

				$dpData .= "</table>";
				$limit .= "</table>";
				$enddate .= "</table>";
				$cart_mdata = str_replace("{download_token_lbl}", JText::_('COM_REDSHOP_DOWNLOAD_TOKEN'), $cart_mdata);
				$cart_mdata = str_replace("{download_token}", $dpData, $cart_mdata);
				$cart_mdata = str_replace("{download_counter_lbl}", JText::_('COM_REDSHOP_DOWNLOAD_LEFT'), $cart_mdata);
				$cart_mdata = str_replace("{download_counter}", $limit, $cart_mdata);
				$cart_mdata = str_replace("{download_date_lbl}", JText::_('COM_REDSHOP_DOWNLOAD_ENDDATE'), $cart_mdata);
				$cart_mdata = str_replace("{download_date}", $enddate, $cart_mdata);
			}
			else
			{
				$cart_mdata = str_replace("{download_token_lbl}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_token}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_counter_lbl}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_counter}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_date_lbl}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_date}", "", $cart_mdata);
			}

			// Download Product log Tags Replace
			if (isset($dproductslog[$product_id]) && count($dproductslog[$product_id]) > 0 && $OrdersDetail->order_status == "C")
			{
				$downloadarraylog = $dproductslog[$product_id];
				$dpData           = "<table class='download_token'>";
				$g                = 1;

				foreach ($downloadarraylog as $downloads)
				{
					$file_name = substr(basename($downloads->file_name), 11);

					$download_id   = $downloads->download_id;
					$download_time = $downloads->download_time;
					$download_date = date("d-m-Y H:i:s", $download_time);
					$ip            = $downloads->ip;

					$mailtoken = "<a href='" . JURI::root() . "index.php?option=com_redshop&view=product&layout=downloadproduct&tid="
						. $download_id . "'>"
						. $file_name . "</a>";

					$dpData .= "</tr>";
					$dpData .= "<td>(" . $g . ") " . $mailtoken . " "
						. JText::_('COM_REDSHOP_ON') . " " . $download_date . " "
						. JText::_('COM_REDSHOP_FROM') . " " . $ip . "</td>";
					$dpData .= "</tr>";

					$g++;
				}

				$dpData .= "</table>";
				$cart_mdata = str_replace("{download_date_list_lbl}", JText::_('COM_REDSHOP_DOWNLOAD_LOG'), $cart_mdata);
				$cart_mdata = str_replace("{download_date_list}", $dpData, $cart_mdata);
			}
			else
			{
				$cart_mdata = str_replace("{download_date_list_lbl}", "", $cart_mdata);
				$cart_mdata = str_replace("{download_date_list}", "", $cart_mdata);
			}

			// Process the product plugin for cart item
			$dispatcher->trigger('onOrderItemDisplay', array(& $cart_mdata, &$rowitem, $i));

			$cart .= $cart_mdata;
		}

		$returnArr[0] = $cart;
		$returnArr[1] = $subtotal_excl_vat;

		return $returnArr;
	}
}


?>