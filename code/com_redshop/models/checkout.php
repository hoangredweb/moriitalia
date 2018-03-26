<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;



/**
 * Class checkoutModelcheckout
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelCheckout extends RedshopModelCheckoutDefault
{
	public function orderplace()
	{
		$app = JFactory::getApplication();

		$redconfig       = Redconfiguration::getInstance();
		$quotationHelper = quotationHelper::getInstance();
		$stockroomhelper = rsstockroomhelper::getInstance();
		$helper          = redhelper::getInstance();
		$shippinghelper  = shipping::getInstance();
		$order_functions = order_functions::getInstance();

		$post = JRequest::get('post');

		$Itemid     = JRequest::getVar('Itemid');
		$shop_id    = JRequest::getVar('shop_id');
		$gls_mobile = JRequest::getVar('gls_mobile');

		$customer_message = JRequest::getVar('rs_customer_message_ta');
		$referral_code    = JRequest::getVar('txt_referral_code');

		if ($gls_mobile)
		{
			$shop_id = $shop_id . '###' . $gls_mobile;
		}

		$user    = JFactory::getUser();
		$session = JFactory::getSession();
		$auth    = $session->get('auth');
		$userId  = $user->id;

		if (!$user->id && $auth['users_info_id'])
		{
			$userId = - $auth['users_info_id'];
		}

		$db      = JFactory::getDbo();
		$issplit = $session->get('issplit');

		// If user subscribe for the newsletter
		if (isset($post['newsletter_signup']) && $post['newsletter_signup'] == 1)
		{
			$this->_userhelper->newsletterSubscribe();
		}

		// If user unsubscribe for the newsletter

		if (isset($post['newsletter_signoff']) && $post['newsletter_signoff'] == 1)
		{
			$this->_userhelper->newsletterUnsubscribe();
		}

		$order_paymentstatus = 'Unpaid';
		$users_info_id       = JRequest::getInt('users_info_id');
		$shippingaddresses   = $this->shipaddress($users_info_id);
		$billingaddresses    = $this->billingaddresses();

		if (isset($shippingaddresses))
		{
			$d ["shippingaddress"]                 = $shippingaddresses;
			$d ["shippingaddress"]->country_2_code = $redconfig->getCountryCode2($d ["shippingaddress"]->country_code);
			$d ["shippingaddress"]->state_2_code   = $redconfig->getStateCode2($d ["shippingaddress"]->state_code);

			$shippingaddresses->country_2_code = $d ["shippingaddress"]->country_2_code;
			$shippingaddresses->state_2_code   = $d ["shippingaddress"]->state_2_code;
		}
		else
		{
			$shippingaddresses = $billingaddresses;
			$shippingaddresses->firstname = isset($post['firstname_ST']) ? $post['firstname_ST'] : $billingaddresses->firstname;
			$shippingaddresses->lastname = isset($post['lastname_ST']) ? $post['lastname_ST'] : $billingaddresses->lastname;
			$shippingaddresses->address = isset($post['address_ST']) ? $post['address_ST'] : $billingaddresses->address;
			$shippingaddresses->zipcode = isset($post['zipcode_ST']) ? $post['zipcode_ST'] : $billingaddresses->zipcode;
			$shippingaddresses->city = isset($post['city_ST']) ? $post['city_ST'] : $billingaddresses->city;
			$shippingaddresses->country_code = isset($post['country_code_ST']) ? $post['country_code_ST'] : $billingaddresses->country_code;
		}

		if (isset($billingaddresses))
		{
			$d ["billingaddress"] = $billingaddresses;

			if (isset($billingaddresses->country_code))
			{
				$d ["billingaddress"]->country_2_code = $redconfig->getCountryCode2($billingaddresses->country_code);
				$billingaddresses->country_2_code     = $d ["billingaddress"]->country_2_code;
			}

			if (isset($billingaddresses->state_code))
			{
				$d ["billingaddress"]->state_2_code = $redconfig->getStateCode2($billingaddresses->state_code);
				$billingaddresses->state_2_code     = $d ["billingaddress"]->state_2_code;
			}
		}

		$cart = $session->get('cart');

		if ($cart['idx'] < 1)
		{
			$msg = JText::_('COM_REDSHOP_EMPTY_CART');
			$app->redirect(JRoute::_('index.php?option=com_redshop&Itemid=' . $Itemid), $msg);
		}

		$shipping_rate_id = '';

		if ($cart['free_shipping'] != 1)
		{
			$shipping_rate_id = JRequest::getVar('shipping_rate_id');
		}

		$payment_method_id = JRequest::getVar('payment_method_id');

		if ($shipping_rate_id && $cart['free_shipping'] != 1)
		{
			$shipArr              = $this->calculateShipping($shipping_rate_id);
			$cart['shipping']     = $shipArr['order_shipping_rate'];
			$cart['shipping_vat'] = $shipArr['shipping_vat'];
		}

		$cart = $this->_carthelper->modifyDiscount($cart);

		// Get Payment information
		$paymentMethod = $this->_order_functions->getPaymentMethodInfo($payment_method_id);
		$paymentMethod = $paymentMethod[0];

		// Se payment method plugin params
		$paymentMethod->params = new JRegistry($paymentMethod->params);

		// Prepare payment Information Object for calculations
		$paymentInfo                              = new stdclass;
		$paymentInfo->payment_price               = $paymentMethod->params->get('payment_price', '');
		$paymentInfo->payment_oprand              = $paymentMethod->params->get('payment_oprand', '');
		$paymentInfo->payment_discount_is_percent = $paymentMethod->params->get('payment_discount_is_percent', '');
		$paymentAmount = $cart ['total'];

		if (Redshop::getConfig()->get('PAYMENT_CALCULATION_ON') == 'subtotal')
		{
			$paymentAmount = $cart ['product_subtotal'];
		}

		$paymentArray  = $this->_carthelper->calculatePayment($paymentAmount, $paymentInfo, $cart ['total']);
		$cart['total'] = $paymentArray[0];
		$session->set('cart', $cart);

		$order_shipping = RedshopShippingRate::decrypt($shipping_rate_id);
		$order_status   = 'P';
		$order_subtotal = $cart ['product_subtotal'];
		$cdiscount      = $cart ['coupon_discount'];
		$order_tax      = $cart ['tax'];
		$d['order_tax'] = $order_tax;

		$tax_after_discount = 0;

		if (isset($cart ['tax_after_discount']))
		{
			$tax_after_discount = $cart ['tax_after_discount'];
		}

		$odiscount     = $cart['coupon_discount'] + $cart['voucher_discount'] + $cart['cart_discount'];
		$odiscount_vat = $cart['discount_vat'];

		$d["order_payment_trans_id"] = '';
		$d['discount']               = $odiscount;
		$order_total                 = $cart['total'];

		if ($issplit)
		{
			$order_total = $order_total / 2;
		}

		JRequest::setVar('order_ship', $order_shipping [3]);

		$paymentElementName = $paymentMethod->element;

		// Check for bank transfer payment type plugin - suffixed using `rs_payment_banktransfer`
		$isBankTransferPaymentType = RedshopHelperPayment::isPaymentType($paymentMethod->element);

		if ($isBankTransferPaymentType || $paymentMethod->element == "rs_payment_eantransfer")
		{
			$order_status        = $paymentMethod->params->get('verify_status', '');
			$order_paymentstatus = trim("Unpaid");
		}

		$paymentMethod->element = $paymentElementName;

		$payment_amount = 0;

		if (isset($cart['payment_amount']))
		{
			$payment_amount = $cart['payment_amount'];
		}

		$payment_oprand = "";

		if (isset($cart['payment_oprand']))
		{
			$payment_oprand = $cart['payment_oprand'];
		}

		$economic_payment_terms_id = $paymentMethod->params->get('economic_payment_terms_id');
		$economic_design_layout    = $paymentMethod->params->get('economic_design_layout');
		$is_creditcard             = $paymentMethod->params->get('is_creditcard', '');
		$is_redirected             = $paymentMethod->params->get('is_redirected', 0);

		JRequest::setVar('payment_status', $order_paymentstatus);

		$d['order_shipping']         = $order_shipping [3];
		$GLOBALS['billingaddresses'] = $billingaddresses;
		$timestamp                   = time();

		$dispatcher = JDispatcher::getInstance();

		$order_status_log = '';

		// For credit card payment gateway page will redirect to order detail page from plugin
		if ($is_creditcard == 1 && $is_redirected == 0 && $cart['total'] > 0)
		{
			$order_number = $order_functions->generateOrderNumber();

			JPluginHelper::importPlugin('redshop_payment');

			$values['order_shipping'] = $d['order_shipping'];
			$values['order_number']   = $order_number;
			$values['order_tax']      = $d['order_tax'];
			$values['shippinginfo']   = $d['shippingaddress'];
			$values['billinginfo']    = $d['billingaddress'];
			$values['order_total']    = $order_total;
			$values['order_subtotal'] = $order_subtotal;
			$values["order_id"]       = $app->input->get('order_id', 0);
			$values['payment_plugin'] = $paymentMethod->element;
			$values['odiscount']      = $odiscount;
			$paymentResponses         = $dispatcher->trigger('onPrePayment_' . $values['payment_plugin'], array($values['payment_plugin'], $values));
			$paymentResponse          = $paymentResponses[0];

			if ($paymentResponse->responsestatus == "Success")
			{
				$d ["order_payment_trans_id"] = $paymentResponse->transaction_id;
				$order_status_log             = $paymentResponse->message;

				if (!isset($paymentResponse->status))
				{
					$paymentResponse->status = 'C';
				}

				$order_status = $paymentResponse->status;

				if (!isset($paymentResponse->paymentStatus))
				{
					$paymentResponse->paymentStatus = 'Paid';
				}

				$order_paymentstatus = $paymentResponse->paymentStatus;
			}
			else
			{
				if ($values['payment_plugin'] != 'rs_payment_localcreditcard')
				{
					$errorMsg = $paymentResponse->message;
					$this->setError($errorMsg);

					return false;
				}
			}
		}

		// Get the IP Address
		$ip = 'unknown';

		if (!empty($_SERVER['REMOTE_ADDR']))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$row = $this->getTable('order_detail');

		if (!$row->bind($post))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		$shippingVatRate = 0;

		if (array_key_exists(6, $order_shipping))
		{
			$shippingVatRate = $order_shipping [6];
		}

		// Start code to track duplicate order number checking
		$order_number = $this->_order_functions->generateOrderNumber();

		$random_gen_enc_key      = $this->_order_functions->random_gen_enc_key(35);
		$users_info_id           = $billingaddresses->users_info_id;
		$row->user_id            = $userId;
		$row->order_number       = $order_number;
		$row->user_info_id       = $users_info_id;
		$row->order_total        = $order_total;
		$row->order_subtotal     = $order_subtotal;
		$row->order_tax          = $order_tax;
		$row->tax_after_discount = $tax_after_discount;
		$row->order_tax_details  = '';
		$row->analytics_status   = 0;
		$row->order_shipping     = $order_shipping [3];
		$row->order_shipping_tax = $shippingVatRate;
		$row->coupon_discount    = $cdiscount;
		$row->shop_id            = $shop_id;
		$row->customer_message   = $customer_message;
		$row->referral_code      = $referral_code;
		$db                      = JFactory::getDbo();

		if ($order_total <= 0)
		{
			$order_status        = $paymentMethod->params->get('verify_status', '');
			$order_paymentstatus = 'Paid';
		}

		if (Redshop::getConfig()->get('USE_AS_CATALOG'))
		{
			$order_status        = 'P';
			$order_paymentstatus = 'Unpaid';
		}

		// For barcode generation
		$row->order_discount       = $odiscount;
		$row->order_discount_vat   = $odiscount_vat;
		$row->payment_discount     = $payment_amount;
		$row->payment_oprand       = $payment_oprand;
		$row->order_status         = $order_status;
		$row->order_payment_status = $order_paymentstatus;
		$row->cdate                = $timestamp;
		$row->mdate                = $timestamp;
		$row->ship_method_id       = $shipping_rate_id;
		$row->customer_note        = $post['customer_note'];
		$row->requisition_number   = $post['requisition_number'];
		$row->ip_address           = $ip;
		$row->encr_key             = $random_gen_enc_key;
		$row->discount_type        = $this->discount_type;
		$row->order_id             = $app->input->getInt('order_id', 0);
		$row->barcode              = $order_functions->barcode_randon_number(12, 0);

		if (!$row->store())
		{
			$this->setError($this->_db->getErrorMsg());

			// Start code to track duplicate order number checking
			$this->deleteOrdernumberTrack();

			return false;
		}

		// Start code to track duplicate order number checking
		$this->deleteOrdernumberTrack();

		// Generate Invoice Number for confirmed credit card payment or for free order
		if (((boolean) Redshop::getConfig()->get('INVOICE_NUMBER_FOR_FREE_ORDER') || $is_creditcard)
			&& ('C' == $row->order_status && 'Paid' == $row->order_payment_status))
		{
			RedshopHelperOrder::generateInvoiceNumber($row->order_id);
		}

		$order_id = $row->order_id;

		$this->coupon($cart, $order_id);
		$this->voucher($cart, $order_id);

		$query = "UPDATE `#__redshop_orders` SET discount_type = " . $db->quote($this->discount_type) . " where order_id = " . (int) $order_id;
		$db->setQuery($query);
		$db->execute();

		if (Redshop::getConfig()->get('SHOW_TERMS_AND_CONDITIONS') == 1 && isset($post['termscondition']) && $post['termscondition'] == 1)
		{
			$this->_userhelper->updateUserTermsCondition($users_info_id, 1);
		}

		// Place order id in quotation table if it Quotation
		if (array_key_exists("quotation_id", $cart) && $cart['quotation_id'])
		{
			$quotationHelper->updateQuotationwithOrder($cart['quotation_id'], $row->order_id);
		}

		if ($row->order_status == Redshop::getConfig()->get('CLICKATELL_ORDER_STATUS'))
		{
			$helper->clickatellSMS($order_id);
		}

		$session->set('order_id', $order_id);

		// Add order status log
		$rowOrderStatus                = $this->getTable('order_status_log');
		$rowOrderStatus->order_id      = $order_id;
		$rowOrderStatus->order_status  = $order_status;
		$rowOrderStatus->date_changed  = time();
		$rowOrderStatus->customer_note = $order_status_log;
		$rowOrderStatus->store();

		JRequest::setVar('order_id', $row->order_id);
		JRequest::setVar('order_number', $row->order_number);

		if (!isset($order_shipping [5]))
		{
			$order_shipping [5] = "";
		}

		$product_delivery_time = $this->_producthelper->getProductMinDeliveryTime($cart[0]['product_id']);
		JRequest::setVar('order_delivery', $product_delivery_time);

		$idx = $cart ['idx'];

		for ($i = 0; $i < $idx; $i++)
		{
			$is_giftcard = 0;
			$product_id  = $cart [$i] ['product_id'];
			$product     = $this->_producthelper->getProductById($product_id);
			$rowitem     = $this->getTable('order_item_detail');

			if (!$rowitem->bind($post))
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			$rowitem->delivery_time = '';

			if (isset($cart [$i] ['giftcard_id']) && $cart [$i] ['giftcard_id'])
			{
				$is_giftcard = 1;
			}

			// Product stockroom update
			if (!$is_giftcard)
			{
				$updatestock                 = $stockroomhelper->updateStockroomQuantity($product_id, $cart [$i] ['quantity']);
				$stockroom_id_list           = $updatestock['stockroom_list'];
				$stockroom_quantity_list     = $updatestock['stockroom_quantity_list'];
				$rowitem->stockroom_id       = $stockroom_id_list;
				$rowitem->stockroom_quantity = $stockroom_quantity_list;
			}

			// End product stockroom update

			$vals = explode('product_attributes/', $cart[$i]['hidden_attribute_cartimage']);

			if (!empty($cart[$i]['attributeImage']) && file_exists(JPATH_ROOT . '/components/com_redshop/assets/images/mergeImages/' . $cart[$i]['attributeImage']))
			{
				$rowitem->attribute_image = $order_id . $cart[$i]['attributeImage'];
				$old_media                = JPATH_ROOT . '/components/com_redshop/assets/images/mergeImages/' . $cart[$i]['attributeImage'];
				$new_media                = JPATH_ROOT . '/components/com_redshop/assets/images/orderMergeImages/' . $rowitem->attribute_image;
				copy($old_media, $new_media);
			}
			elseif (!empty($vals[1]))
			{
				$rowitem->attribute_image = $vals[1];
			}

			$wrapper_price = 0;

			if (@$cart[$i]['wrapper_id'])
			{
				$wrapper_price = $cart[$i]['wrapper_price'];
			}

			if ($is_giftcard == 1)
			{
				$giftcardData                    = $this->_producthelper->getGiftcardData($cart [$i] ['giftcard_id']);
				$rowitem->product_id             = $cart [$i] ['giftcard_id'];
				$rowitem->order_item_name        = $giftcardData->giftcard_name;
				$rowitem->product_item_old_price = $cart [$i] ['product_price'];
			}
			else
			{
				$rowitem->product_id             = $product_id;
				$rowitem->product_item_old_price = $cart [$i] ['product_old_price'];
				$rowitem->supplier_id            = $product->manufacturer_id;
				$rowitem->order_item_sku         = $product->product_number;
				$rowitem->order_item_name        = $product->product_name;
			}

			$rowitem->product_item_price          = $cart [$i] ['product_price'];
			$rowitem->product_quantity            = $cart [$i] ['quantity'];
			$rowitem->product_item_price_excl_vat = $cart [$i] ['product_price_excl_vat'];
			$rowitem->product_final_price         = ($cart [$i] ['product_price'] * $cart [$i] ['quantity']);
			$rowitem->is_giftcard                 = $is_giftcard;

			$retAttArr      = $this->_producthelper->makeAttributeCart($cart [$i] ['cart_attribute'], $product_id, 0, 0, $cart [$i] ['quantity']);
			$cart_attribute = $retAttArr[0];

			// For discount calc data
			$cart_calc_data = "";

			if (isset($cart[$i]['discount_calc_output']))
			{
				$cart_calc_data = $cart[$i]['discount_calc_output'];
			}

			$retAccArr                    = $this->_producthelper->makeAccessoryCart($cart[$i]['cart_accessory'], $product_id);
			$cart_accessory               = $retAccArr[0];
			$rowitem->order_id            = $order_id;
			$rowitem->user_info_id        = $users_info_id;
			$rowitem->order_item_currency = Redshop::getConfig()->get('REDCURRENCY_SYMBOL');
			$rowitem->order_status        = $order_status;
			$rowitem->cdate               = $timestamp;
			$rowitem->mdate               = $timestamp;
			$rowitem->product_attribute   = $cart_attribute;
			$rowitem->discount_calc_data  = $cart_calc_data;
			$rowitem->product_accessory   = $cart_accessory;
			$rowitem->wrapper_price       = $wrapper_price;

			if (!empty($cart[$i]['wrapper_id']))
			{
				$rowitem->wrapper_id = $cart[$i]['wrapper_id'];
			}

			if (!empty($cart[$i]['reciver_email']))
			{
				$rowitem->giftcard_user_email = $cart[$i]['reciver_email'];
			}

			if (!empty($cart[$i]['reciver_name']))
			{
				$rowitem->giftcard_user_name  = $cart[$i]['reciver_name'];
			}

			if ($this->_producthelper->checkProductDownload($rowitem->product_id))
			{
				$medianame = $this->_producthelper->getProductMediaName($rowitem->product_id);

				for ($j = 0, $jn = count($medianame); $j < $jn; $j++)
				{
					$product_serial_number = $this->_producthelper->getProdcutSerialNumber($rowitem->product_id);
					$this->_producthelper->insertProductDownload($rowitem->product_id, $user->id, $rowitem->order_id, $medianame[$j]->media_name, $product_serial_number->serial_number);
				}
			}

			// Import files for plugin
			JPluginHelper::importPlugin('redshop_product');

			if (!$rowitem->store())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			// Add plugin support
			$dispatcher->trigger('afterOrderItemSave', array($cart, $rowitem, $i));

			// End

			if (isset($cart [$i] ['giftcard_id']) && $cart [$i] ['giftcard_id'])
			{
				$section_id = 13;
			}
			else
			{
				$section_id = 12;
			}

			$this->_producthelper->insertProdcutUserfield($i, $cart, $rowitem->order_item_id, $section_id);

			// My accessory save in table start
			if (count($cart [$i] ['cart_accessory']) > 0)
			{
				$setPropEqual    = true;
				$setSubpropEqual = true;
				$attArr          = $cart [$i] ['cart_accessory'];

				for ($a = 0, $an = count($attArr); $a < $an; $a++)
				{
					$accessory_vat_price = 0;
					$accessory_attribute = "";

					$accessory_id        = $attArr[$a]['accessory_id'];
					$accessory_name      = $attArr[$a]['accessory_name'];
					$accessory_price     = $attArr[$a]['accessory_price'];
					$accessory_quantity  = $attArr[$a]['accessory_quantity'];
					$accessory_org_price = $accessory_price;

					if ($accessory_price > 0)
					{
						$accessory_vat_price = $this->_producthelper->getProductTax($rowitem->product_id, $accessory_price);
					}

					$attchildArr = $attArr[$a]['accessory_childs'];

					for ($j = 0, $jn = count($attchildArr); $j < $jn; $j++)
					{
						$prooprand     = array();
						$proprice      = array();

						$propArr       = $attchildArr[$j]['attribute_childs'];
						$totalProperty = count($propArr);

						if ($totalProperty)
						{

							$attribute_id = $attchildArr[$j]['attribute_id'];
							$accessory_attribute .= urldecode($attchildArr[$j]['attribute_name']) . ":<br/>";

							$rowattitem                    = $this->getTable('order_attribute_item');
							$rowattitem->order_att_item_id = 0;
							$rowattitem->order_item_id     = $rowitem->order_item_id;
							$rowattitem->section_id        = $attribute_id;
							$rowattitem->section           = "attribute";
							$rowattitem->parent_section_id = $accessory_id;
							$rowattitem->section_name      = $attchildArr[$j]['attribute_name'];
							$rowattitem->is_accessory_att  = 1;

							if ($attribute_id > 0)
							{
								if (!$rowattitem->store())
								{
									$this->setError($this->_db->getErrorMsg());

									return false;
								}
							}
						}

						for ($k = 0; $k < $totalProperty; $k++)
						{
							$prooprand[$k] = $propArr[$k]['property_oprand'];
							$proprice[$k]  = $propArr[$k]['property_price'];
							$section_vat   = 0;

							if ($propArr[$k]['property_price'] > 0)
							{
								$section_vat = $this->_producthelper->getProducttax($rowitem->product_id, $propArr[$k]['property_price']);
							}

							$property_id = $propArr[$k]['property_id'];
							$accessory_attribute .= urldecode($propArr[$k]['property_name']) . " (" . $propArr[$k]['property_oprand'] . $this->_producthelper->getProductFormattedPrice($propArr[$k]['property_price'] + $section_vat) . ")<br/>";
							$subpropArr                    = $propArr[$k]['property_childs'];
							$rowattitem                    = $this->getTable('order_attribute_item');
							$rowattitem->order_att_item_id = 0;
							$rowattitem->order_item_id     = $rowitem->order_item_id;
							$rowattitem->section_id        = $property_id;
							$rowattitem->section           = "property";
							$rowattitem->parent_section_id = $attribute_id;
							$rowattitem->section_name      = $propArr[$k]['property_name'];
							$rowattitem->section_price     = $propArr[$k]['property_price'];
							$rowattitem->section_vat       = $section_vat;
							$rowattitem->section_oprand    = $propArr[$k]['property_oprand'];
							$rowattitem->is_accessory_att  = 1;

							if ($property_id > 0)
							{
								if (!$rowattitem->store())
								{
									$this->setError($this->_db->getErrorMsg());

									return false;
								}
							}

							for ($l = 0, $nl = count($subpropArr); $l < $nl; $l++)
							{
								$section_vat = 0;

								if ($subpropArr[$l]['subproperty_price'] > 0)
								{
									$section_vat = $this->_producthelper->getProducttax($rowitem->product_id, $subpropArr[$l]['subproperty_price']);
								}

								$subproperty_id = $subpropArr[$l]['subproperty_id'];
								$accessory_attribute .= urldecode($subpropArr[$l]['subproperty_name']) . " (" . $subpropArr[$l]['subproperty_oprand'] . $this->_producthelper->getProductFormattedPrice($subpropArr[$l]['subproperty_price'] + $section_vat) . ")<br/>";
								$rowattitem                    = $this->getTable('order_attribute_item');
								$rowattitem->order_att_item_id = 0;
								$rowattitem->order_item_id     = $rowitem->order_item_id;
								$rowattitem->section_id        = $subproperty_id;
								$rowattitem->section           = "subproperty";
								$rowattitem->parent_section_id = $property_id;
								$rowattitem->section_name      = $subpropArr[$l]['subproperty_name'];
								$rowattitem->section_price     = $subpropArr[$l]['subproperty_price'];
								$rowattitem->section_vat       = $section_vat;
								$rowattitem->section_oprand    = $subpropArr[$l]['subproperty_oprand'];
								$rowattitem->is_accessory_att  = 1;

								if ($subproperty_id > 0)
								{
									if (!$rowattitem->store())
									{
										$this->setError($this->_db->getErrorMsg());

										return false;
									}
								}
							}
						}

						// FOR ACCESSORY PROPERTY AND SUBPROPERTY PRICE CALCULATION
						if ($setPropEqual && $setSubpropEqual)
						{
							$accessory_priceArr = $this->_producthelper->makeTotalPriceByOprand($accessory_price, $prooprand, $proprice);
							$setPropEqual       = $accessory_priceArr[0];
							$accessory_price    = $accessory_priceArr[1];
						}

						for ($t = 0, $tn = count($propArr); $t < $tn; $t++)
						{
							$subprooprand  = array();
							$subproprice   = array();
							$subElementArr = $propArr[$t]['property_childs'];

							for ($tp = 0; $tp < count($subElementArr); $tp++)
							{
								$subprooprand[$tp] = $subElementArr[$tp]['subproperty_oprand'];
								$subproprice[$tp]  = $subElementArr[$tp]['subproperty_price'];
							}

							if ($setPropEqual && $setSubpropEqual)
							{
								$accessory_priceArr = $this->_producthelper->makeTotalPriceByOprand($accessory_price, $subprooprand, $subproprice);
								$setSubpropEqual    = $accessory_priceArr[0];
								$accessory_price    = $accessory_priceArr[1];
							}
						}
					}

					$accdata = $this->getTable('accessory_detail');

					if ($accessory_id > 0)
					{
						$accdata->load($accessory_id);
					}

					$accProductinfo                      = $this->_producthelper->getProductById($accdata->child_product_id);
					$rowaccitem                          = $this->getTable('order_acc_item');
					$rowaccitem->order_item_acc_id       = 0;
					$rowaccitem->order_item_id           = $rowitem->order_item_id;
					$rowaccitem->product_id              = $accessory_id;
					$rowaccitem->order_acc_item_sku      = $accProductinfo->product_number;
					$rowaccitem->order_acc_item_name     = $accessory_name;
					$rowaccitem->order_acc_price         = $accessory_org_price;
					$rowaccitem->order_acc_vat           = $accessory_vat_price;
					$rowaccitem->product_quantity        = $accessory_quantity;
					$rowaccitem->product_acc_item_price  = $accessory_price;
					$rowaccitem->product_acc_final_price = ($accessory_price * $accessory_quantity);
					$rowaccitem->product_attribute       = $accessory_attribute;

					if ($accessory_id > 0)
					{
						if (!$rowaccitem->store())
						{
							$this->setError($this->_db->getErrorMsg());

							return false;
						}
					}
				}
			}

			// Storing attribute in database
			if (count($cart [$i] ['cart_attribute']) > 0)
			{
				$attchildArr = $cart [$i] ['cart_attribute'];

				for ($j = 0, $jn = count($attchildArr); $j < $jn; $j++)
				{
					$propArr       = $attchildArr[$j]['attribute_childs'];
					$totalProperty = count($propArr);

					if ($totalProperty > 0)
					{
						$attribute_id                  = $attchildArr[$j]['attribute_id'];
						$rowattitem                    = $this->getTable('order_attribute_item');
						$rowattitem->order_att_item_id = 0;
						$rowattitem->order_item_id     = $rowitem->order_item_id;
						$rowattitem->section_id        = $attribute_id;
						$rowattitem->section           = "attribute";
						$rowattitem->parent_section_id = $rowitem->product_id;
						$rowattitem->section_name      = $attchildArr[$j]['attribute_name'];
						$rowattitem->is_accessory_att  = 0;

						if ($attribute_id > 0)
						{
							if (!$rowattitem->store())
							{
								$this->setError($this->_db->getErrorMsg());

								return false;
							}
						}

						for ($k = 0; $k < $totalProperty; $k++)
						{
							$section_vat = 0;

							if ($propArr[$k]['property_price'] > 0)
							{
								$section_vat = $this->_producthelper->getProducttax($rowitem->product_id, $propArr[$k]['property_price']);
							}

							$property_id = $propArr[$k]['property_id'];

							//  Product property STOCKROOM update start
							$updatestock_att             = $stockroomhelper->updateStockroomQuantity($property_id, $cart [$i] ['quantity'], "property", $product_id);
							$stockroom_att_id_list       = $updatestock_att['stockroom_list'];
							$stockroom_att_quantity_list = $updatestock_att['stockroom_quantity_list'];

							$rowattitem                     = $this->getTable('order_attribute_item');
							$rowattitem->order_att_item_id  = 0;
							$rowattitem->order_item_id      = $rowitem->order_item_id;
							$rowattitem->section_id         = $property_id;
							$rowattitem->section            = "property";
							$rowattitem->parent_section_id  = $attribute_id;
							$rowattitem->section_name       = $propArr[$k]['property_name'];
							$rowattitem->section_price      = $propArr[$k]['property_price'];
							$rowattitem->section_vat        = $section_vat;
							$rowattitem->section_oprand     = $propArr[$k]['property_oprand'];
							$rowattitem->is_accessory_att   = 0;
							$rowattitem->stockroom_id       = $stockroom_att_id_list;
							$rowattitem->stockroom_quantity = $stockroom_att_quantity_list;

							if ($property_id > 0)
							{
								if (!$rowattitem->store())
								{
									$this->setError($this->_db->getErrorMsg());

									return false;
								}
							}

							$subpropArr = $propArr[$k]['property_childs'];

							for ($l = 0, $nl = count($subpropArr); $l < $nl; $l++)
							{
								$section_vat = 0;

								if ($subpropArr[$l]['subproperty_price'] > 0)
								{
									$section_vat = $this->_producthelper->getProducttax($rowitem->product_id, $subpropArr[$l]['subproperty_price']);
								}

								$subproperty_id = $subpropArr[$l]['subproperty_id'];

								// Product subproperty STOCKROOM update start
								$updatestock_subatt             = $stockroomhelper->updateStockroomQuantity($subproperty_id, $cart [$i] ['quantity'], "subproperty", $product_id);
								$stockroom_subatt_id_list       = $updatestock_subatt['stockroom_list'];
								$stockroom_subatt_quantity_list = $updatestock_subatt['stockroom_quantity_list'];

								$rowattitem                     = $this->getTable('order_attribute_item');
								$rowattitem->order_att_item_id  = 0;
								$rowattitem->order_item_id      = $rowitem->order_item_id;
								$rowattitem->section_id         = $subproperty_id;
								$rowattitem->section            = "subproperty";
								$rowattitem->parent_section_id  = $property_id;
								$rowattitem->section_name       = $subpropArr[$l]['subproperty_name'];
								$rowattitem->section_price      = $subpropArr[$l]['subproperty_price'];
								$rowattitem->section_vat        = $section_vat;
								$rowattitem->section_oprand     = $subpropArr[$l]['subproperty_oprand'];
								$rowattitem->is_accessory_att   = 0;
								$rowattitem->stockroom_id       = $stockroom_subatt_id_list;
								$rowattitem->stockroom_quantity = $stockroom_subatt_quantity_list;

								if ($subproperty_id > 0)
								{
									if (!$rowattitem->store())
									{
										$this->setError($this->_db->getErrorMsg());

										return false;
									}
								}
							}
						}
					}
				}
			}

			// Store user product subscription detail
			if ($product->product_type == 'subscription')
			{
				$subscribe           = $this->getTable('product_subscribe_detail');
				$subscription_detail = $this->_producthelper->getProductSubscriptionDetail($product_id, $cart[$i]['subscription_id']);

				$add_day                    = $subscription_detail->period_type == 'days' ? $subscription_detail->subscription_period : 0;
				$add_month                  = $subscription_detail->period_type == 'month' ? $subscription_detail->subscription_period : 0;
				$add_year                   = $subscription_detail->period_type == 'year' ? $subscription_detail->subscription_period : 0;
				$subscribe->order_id        = $order_id;
				$subscribe->order_item_id   = $rowitem->order_item_id;
				$subscribe->product_id      = $product_id;
				$subscribe->subscription_id = $cart[$i]['subscription_id'];
				$subscribe->user_id         = $user->id;
				$subscribe->start_date      = time();
				$subscribe->end_date        = mktime(0, 0, 0, date('m') + $add_month, date('d') + $add_day, date('Y') + $add_year);

				if (!$subscribe->store())
				{
					$this->setError($this->_db->getErrorMsg());

					return false;
				}
			}
		}

		$rowpayment = $this->getTable('order_payment');

		if (!$rowpayment->bind($post))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		$rowpayment->order_id          = $order_id;
		$rowpayment->payment_method_id = $payment_method_id;

		$ccdata = $session->get('ccdata');

		if (!isset($ccdata['creditcard_code']))
		{
			$ccdata['creditcard_code'] = 0;
		}

		if (!isset($ccdata['order_payment_number']))
		{
			$ccdata['order_payment_number'] = 0;
		}

		if (!isset($ccdata['order_payment_expire_month']))
		{
			$ccdata['order_payment_expire_month'] = 0;
		}

		if (!isset($ccdata['order_payment_expire_year']))
		{
			$ccdata['order_payment_expire_year'] = 0;
		}

		$rowpayment->order_payment_code     = $ccdata['creditcard_code'];
		$rowpayment->order_payment_cardname = base64_encode($ccdata['order_payment_name']);
		$rowpayment->order_payment_number   = base64_encode($ccdata['order_payment_number']);

		// This is ccv code
		$rowpayment->order_payment_ccv      = base64_encode($ccdata['credit_card_code']);
		$rowpayment->order_payment_amount   = $order_total;
		$rowpayment->order_payment_expire   = $ccdata['order_payment_expire_month'] . $ccdata['order_payment_expire_year'];
		$rowpayment->order_payment_name     = $paymentMethod->name;
		$rowpayment->payment_method_class   = $paymentMethod->element;
		$rowpayment->order_payment_trans_id = $d ["order_payment_trans_id"];
		$rowpayment->authorize_status       = "";

		if (!$rowpayment->store())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		// For authorize status
		JPluginHelper::importPlugin('redshop_payment');
		JDispatcher::getInstance()->trigger('onAuthorizeStatus_' . $paymentMethod->element, array($paymentMethod->element, $order_id));

		$GLOBALS['shippingaddresses'] = $shippingaddresses;

		// Add billing Info
		$userrow = $this->getTable('user_detail');
		$userrow->load($billingaddresses->users_info_id);
		$userrow->thirdparty_email = $post['thirdparty_email'];
		$orderuserrow              = $this->getTable('order_user_detail');

		if (!$orderuserrow->bind($userrow))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		$orderuserrow->order_id     = $order_id;
		$orderuserrow->address_type = 'BT';

		JPluginHelper::importPlugin('redshop_checkout');
		$dispatcher->trigger('onBeforeUserBillingStore', array(&$orderuserrow));

		if (!$orderuserrow->store())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		// Add shipping Info
		$userrow = $this->getTable('user_detail');

		if (isset($shippingaddresses->users_info_id))
		{
			$userrow->load($shippingaddresses->users_info_id);
		}

		$orderuserrow = $this->getTable('order_user_detail');

		if (!$orderuserrow->bind($userrow))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		$orderuserrow->order_id     = $order_id;
		$orderuserrow->address_type = 'ST';

		$dispatcher->trigger('onBeforeUserShippingStore', array(&$orderuserrow));

		if (!$orderuserrow->store())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		if (isset($cart['extrafields_values']))
		{
			if (count($cart['extrafields_values']) > 0)
			{
				$this->_producthelper->insertPaymentShippingField($cart, $order_id, 18);
				$this->_producthelper->insertPaymentShippingField($cart, $order_id, 19);
			}
		}

		$stockroomhelper->deleteCartAfterEmpty();

		// Economic Integration start for invoice generate and book current invoice
		if (Redshop::getConfig()->get('ECONOMIC_INTEGRATION') == 1 && Redshop::getConfig()->get('ECONOMIC_INVOICE_DRAFT') != 2)
		{
			$economic = economic::getInstance();

			$economicdata['economic_payment_terms_id'] = $economic_payment_terms_id;
			$economicdata['economic_design_layout']    = $economic_design_layout;
			$economicdata['economic_is_creditcard']    = $is_creditcard;
			$payment_name                              = $paymentMethod->element;
			$paymentArr                                = explode("rs_payment_", $paymentMethod->element);

			if (count($paymentArr) > 0)
			{
				$payment_name = $paymentArr[1];
			}

			$economicdata['economic_payment_method'] = $payment_name;
			$economic->createInvoiceInEconomic($row->order_id, $economicdata);

			if (Redshop::getConfig()->get('ECONOMIC_INVOICE_DRAFT') == 0)
			{
				$checkOrderStatus = ($isBankTransferPaymentType) ? 0 : 1;

				$bookinvoicepdf = $economic->bookInvoiceInEconomic($row->order_id, $checkOrderStatus);

				if (is_file($bookinvoicepdf))
				{
					$this->_redshopMail->sendEconomicBookInvoiceMail($row->order_id, $bookinvoicepdf);
				}
			}
		}

		//Update user point
		JPluginHelper::importPlugin('redshop_user');
		$dispatcher->trigger(
					'updateUserPoint',
					array(
						$user->id,
						$cart['user_point']
					)
				);

		// Send the Order mail before payment
		if (!Redshop::getConfig()->get('ORDER_MAIL_AFTER') || (Redshop::getConfig()->get('ORDER_MAIL_AFTER') && $row->order_payment_status == "Paid"))
		{
			$this->_redshopMail->sendOrderMail($row->order_id);
		}
		elseif (Redshop::getConfig()->get('ORDER_MAIL_AFTER') == 1)
		{
			// If Order mail set to send after payment then send mail to administrator only.
			$this->_redshopMail->sendOrderMail($row->order_id, true);
		}

		if ($row->order_status == "C" && $row->order_payment_status == "Paid")
		{
			$this->_order_functions->SendDownload($row->order_id);
		}

		return $row;
	}
}