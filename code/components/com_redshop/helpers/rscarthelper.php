<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class rsCarthelper extends rsCarthelperDefault
{
	// public function replaceOrderTemplate($row, $ReceiptTemplate, $sendmail = false)
	// {
	// 	$url       = JURI::base();
	// 	$redconfig = Redconfiguration::getInstance();
	// 	$order_id  = $row->order_id;
	// 	$session   = JFactory::getSession();
	// 	$orderitem = $this->_order_functions->getOrderItemDetail($order_id);

	// 	$search    = array();
	// 	$replace   = array();

	// 	if (strpos($ReceiptTemplate, "{product_loop_start}") !== false && strpos($ReceiptTemplate, "{product_loop_end}") !== false)
	// 	{
	// 		$template_sdata  = explode('{product_loop_start}', $ReceiptTemplate);
	// 		$template_start  = $template_sdata[0];
	// 		$template_edata  = explode('{product_loop_end}', $template_sdata[1]);
	// 		$template_end    = $template_edata[1];
	// 		$template_middle = $template_edata[0];
	// 		$cartArr         = $this->repalceOrderItems($template_middle, $orderitem);
	// 		$ReceiptTemplate = $template_start . $cartArr[0] . $template_end;
	// 	}

	// 	$app = JFactory::getApplication();
	// 	$menu = $app->getMenu();
	// 	$menuItem = $menu->getItems('link', 'index.php?option=com_redshop&view=orders&template_id=15', true);

	// 	$orderdetailurl   = JURI::root() . 'index.php?option=com_redshop&view=order_detail&oid=' . $order_id . '&encr=' . $row->encr_key . '&Itemid=' . $menuItem->id;

	// 	$downloadProducts     = $this->_order_functions->getDownloadProduct($order_id);
	// 	$paymentmethod        = $this->_order_functions->getOrderPaymentDetail($order_id);
	// 	$paymentmethod        = $paymentmethod[0];

	// 	// Initialize Transaction label
	// 	$transactionIdLabel = '';

	// 	// Check if transaction Id is set
	// 	if ($paymentmethod->order_payment_trans_id != null)
	// 	{
	// 		$transactionIdLabel = JText::_('COM_REDSHOP_PAYMENT_TRANSACTION_ID_LABEL');
	// 	}

	// 	// Replace Transaction Id and Label
	// 	$ReceiptTemplate      = str_replace("{transaction_id_label}", $transactionIdLabel, $ReceiptTemplate);
	// 	$ReceiptTemplate      = str_replace("{transaction_id}", $paymentmethod->order_payment_trans_id, $ReceiptTemplate);

	// 	// Get Payment Method information
	// 	$paymentmethod_detail = $this->_order_functions->getPaymentMethodInfo($paymentmethod->payment_method_class);
	// 	$paymentmethod_detail = $paymentmethod_detail [0];
	// 	$OrderStatus          = $this->_order_functions->getOrderStatusTitle($row->order_status);

	// 	$product_name         = "";
	// 	$product_price        = "";
	// 	$subtotal_excl_vat    = $cartArr[1];
	// 	$barcode_code         = $row->barcode;
	// 	$img_url              = REDSHOP_FRONT_IMAGES_ABSPATH . "barcode/" . $barcode_code . ".png";
	// 	$bar_replace          = '<img alt="" src="' . $img_url . '">';

	// 	$total_excl_vat       = $subtotal_excl_vat + ($row->order_shipping - $row->order_shipping_tax) - ($row->order_discount - $row->order_discount_vat);
	// 	$sub_total_vat        = $row->order_tax + $row->order_shipping_tax;

	// 	if (isset($row->voucher_discount) === false)
	// 	{
	// 		$row->voucher_discount = 0;
	// 	}

	// 	$Total_discount = $row->coupon_discount + $row->order_discount + $row->special_discount + $row->tax_after_discount + $row->voucher_discount;

	// 	// For Payment and Shipping Extra Fields
	// 	if (strpos($ReceiptTemplate, '{payment_extrafields}') !== false)
	// 	{
	// 		$PaymentExtrafields = $this->_producthelper->getPaymentandShippingExtrafields($row, 18);

	// 		if ($PaymentExtrafields == "")
	// 		{
	// 			$ReceiptTemplate = str_replace("{payment_extrafields_lbl}", "", $ReceiptTemplate);
	// 			$ReceiptTemplate = str_replace("{payment_extrafields}", "", $ReceiptTemplate);
	// 		}
	// 		else
	// 		{
	// 			$ReceiptTemplate = str_replace("{payment_extrafields_lbl}", JText::_("COM_REDSHOP_ORDER_PAYMENT_EXTRA_FILEDS"), $ReceiptTemplate);
	// 			$ReceiptTemplate = str_replace("{payment_extrafields}", $PaymentExtrafields, $ReceiptTemplate);
	// 		}
	// 	}

	// 	if (strpos($ReceiptTemplate, '{shipping_extrafields}') !== false)
	// 	{
	// 		$ShippingExtrafields = $this->_producthelper->getPaymentandShippingExtrafields($row, 19);

	// 		if ($ShippingExtrafields == "")
	// 		{
	// 			$ReceiptTemplate = str_replace("{shipping_extrafields_lbl}", "", $ReceiptTemplate);
	// 			$ReceiptTemplate = str_replace("{shipping_extrafields}", "", $ReceiptTemplate);
	// 		}
	// 		else
	// 		{
	// 			$ReceiptTemplate = str_replace("{shipping_extrafields_lbl}", JText::_("COM_REDSHOP_ORDER_SHIPPING_EXTRA_FILEDS"), $ReceiptTemplate);
	// 			$ReceiptTemplate = str_replace("{shipping_extrafields}", $ShippingExtrafields, $ReceiptTemplate);
	// 		}
	// 	}

	// 	// End
	// 	$ReceiptTemplate = $this->replaceShippingMethod($row, $ReceiptTemplate);

	// 	if (!APPLY_VAT_ON_DISCOUNT)
	// 	{
	// 		$total_for_discount = $subtotal_excl_vat;
	// 	}
	// 	else
	// 	{
	// 		$total_for_discount = $row->order_subtotal;
	// 	}

	// 	$ReceiptTemplate = $this->replaceLabel($ReceiptTemplate);
	// 	$search[]        = "{order_subtotal}";
	// 	$chktag          = $this->_producthelper->getApplyVatOrNot($ReceiptTemplate);

	// 	if (!empty($chktag))
	// 	{
	// 		$replace[] = $this->_producthelper->getProductFormattedPrice($row->order_total);
	// 	}
	// 	else
	// 	{
	// 		$replace[] = $this->_producthelper->getProductFormattedPrice($total_excl_vat);
	// 	}

	// 	$search[]  = "{subtotal_excl_vat}";
	// 	$replace[] = $this->_producthelper->getProductFormattedPrice($total_excl_vat);
	// 	$search[]  = "{product_subtotal}";

	// 	if (!empty($chktag))
	// 	{
	// 		$replace[] = $this->_producthelper->getProductFormattedPrice($row->order_subtotal);
	// 	}
	// 	else
	// 	{
	// 		$replace[] = $this->_producthelper->getProductFormattedPrice($subtotal_excl_vat);
	// 	}

	// 	$search[]   = "{product_subtotal_excl_vat}";
	// 	$replace[]  = $this->_producthelper->getProductFormattedPrice($subtotal_excl_vat);
	// 	$search[]   = "{order_subtotal_excl_vat}";
	// 	$replace[]  = $this->_producthelper->getProductFormattedPrice($total_excl_vat);
	// 	$search[]   = "{order_number_lbl}";
	// 	$replace[]  = JText::_('COM_REDSHOP_ORDER_NUMBER_LBL');
	// 	$search[]   = "{order_number}";
	// 	$replace[]  = $row->order_number;
	// 	$search  [] = "{special_discount}";
	// 	$replace [] = $row->special_discount . '%';
	// 	$search  [] = "{special_discount_amount}";
	// 	$replace [] = $this->_producthelper->getProductFormattedPrice($row->special_discount_amount);
	// 	$search[]   = "{special_discount_lbl}";
	// 	$replace[]  = JText::_('COM_REDSHOP_SPECIAL_DISCOUNT');

	// 	$search[]   = "{order_detail_link}";
	// 	$replace[]  = "<a href='" . $orderdetailurl . "'>" . JText::_("COM_REDSHOP_ORDER_MAIL") . "</a>";

	// 	$dpData = "";

	// 	if (count($downloadProducts) > 0)
	// 	{
	// 		$dpData .= "<table>";

	// 		for ($d = 0, $dn = count($downloadProducts); $d < $dn; $d++)
	// 		{
	// 			$g                = $d + 1;
	// 			$downloadProduct  = $downloadProducts[$d];
	// 			$downloadfilename = substr(basename($downloadProduct->file_name), 11);
	// 			$downloadToken    = $downloadProduct->download_id;
	// 			$product_name     = $downloadProduct->product_name;
	// 			$mailtoken        = $product_name . ": <a href='" . JURI::root() . "index.php?option=com_redshop&view=product&layout=downloadproduct&tid=" . $downloadToken . "'>" . $downloadfilename . "</a>";

	// 			$dpData .= "</tr>";
	// 			$dpData .= "<td>(" . $g . ") " . $mailtoken . "</td>";
	// 			$dpData .= "</tr>";
	// 		}

	// 		$dpData .= "</table>";
	// 	}

	// 	if ($row->order_status == "C" && $row->order_payment_status == "Paid")
	// 	{
	// 		$search  [] = "{download_token}";
	// 		$replace [] = $dpData;

	// 		$search  [] = "{download_token_lbl}";

	// 		if ($dpData != "")
	// 		{
	// 			$replace [] = JText::_('COM_REDSHOP_DOWNLOAD_TOKEN');
	// 		}
	// 		else
	// 		{
	// 			$replace [] = "";
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$search  [] = "{download_token}";
	// 		$replace [] = "";
	// 		$search  [] = "{download_token_lbl}";
	// 		$replace [] = "";
	// 	}

	// 	$issplitdisplay  = "";
	// 	$issplitdisplay2 = "";

	// 	if ((strpos($ReceiptTemplate, "{discount_denotation}") !== false || strpos($ReceiptTemplate, "{shipping_denotation}") !== false) && ($Total_discount != 0 || $row->order_shipping != 0))
	// 	{
	// 		$search  [] = "{denotation_label}";
	// 		$replace [] = JText::_('COM_REDSHOP_DENOTATION_TXT');
	// 	}
	// 	else
	// 	{
	// 		$search  [] = "{denotation_label}";
	// 		$replace [] = "";

	// 	}

	// 	$search  [] = "{discount_denotation}";

	// 	if (strpos($ReceiptTemplate, "{discount_excl_vat}") !== false)
	// 	{
	// 		$replace [] = "*";
	// 	}
	// 	else
	// 	{
	// 		$replace [] = "";
	// 	}

	// 	$search  [] = "{shipping_denotation}";

	// 	if (strpos($ReceiptTemplate, "{shipping_excl_vat}") !== false)
	// 	{
	// 		$replace [] = "*";
	// 	}
	// 	else
	// 	{
	// 		$replace [] = "";
	// 	}

	// 	$search[] = "{payment_status}";

	// 	if (trim($row->order_payment_status) == 'Paid')
	// 	{
	// 		$orderPaymentStatus = JText::_('COM_REDSHOP_PAYMENT_STA_PAID');
	// 	}
	// 	elseif (trim($row->order_payment_status) == 'Unpaid')
	// 	{
	// 		$orderPaymentStatus = JText::_('COM_REDSHOP_PAYMENT_STA_UNPAID');
	// 	}
	// 	elseif (trim($row->order_payment_status) == 'Partial Paid')
	// 	{
	// 		$orderPaymentStatus = JText::_('COM_REDSHOP_PAYMENT_STA_PARTIAL_PAID');
	// 	}
	// 	else
	// 	{
	// 		$orderPaymentStatus = $row->order_payment_status;
	// 	}

	// 	$replace[] = $orderPaymentStatus . " " . JRequest::getVar('order_payment_log') . $issplitdisplay . $issplitdisplay2;
	// 	$search[]  = "{order_payment_status}";
	// 	$replace[] = $orderPaymentStatus . " " . JRequest::getVar('order_payment_log') . $issplitdisplay . $issplitdisplay2;

	// 	$search  [] = "{order_total}";
	// 	$replace [] = $this->_producthelper->getProductFormattedPrice($row->order_total);
	// 	$search  [] = "{total_excl_vat}";
	// 	$replace [] = $this->_producthelper->getProductFormattedPrice($total_excl_vat);
	// 	$search  [] = "{sub_total_vat}";
	// 	$replace [] = $this->_producthelper->getProductFormattedPrice($sub_total_vat);
	// 	$search  [] = "{order_id}";
	// 	$replace [] = $order_id;
	// 	$search  [] = "{discount_denotation}";
	// 	$replace [] = "*";

	// 	$arr_discount_type = array();
	// 	$arr_discount      = explode('@', $row->discount_type);
	// 	$discount_type     = '';

	// 	for ($d = 0, $dn = count($arr_discount); $d < $dn; $d++)
	// 	{
	// 		if ($arr_discount[$d])
	// 		{
	// 			$arr_discount_type = explode(':', $arr_discount[$d]);

	// 			if ($arr_discount_type[0] == 'c')
	// 			{
	// 				$discount_type .= JText::_('COM_REDSHOP_COUPON_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
	// 			}

	// 			if ($arr_discount_type[0] == 'v')
	// 			{
	// 				$discount_type .= JText::_('COM_REDSHOP_VOUCHER_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
	// 			}
	// 		}
	// 	}

	// 	$search[]  = "{discount_type}";
	// 	$replace[] = $discount_type;

	// 	$search  [] = "{discount_excl_vat}";
	// 	$replace [] = $this->_producthelper->getProductFormattedPrice($row->order_discount - $row->order_discount_vat);
	// 	$search  [] = "{order_status}";
	// 	$replace [] = $OrderStatus;
	// 	$search  [] = "{order_id_lbl}";
	// 	$replace [] = JText::_('COM_REDSHOP_ORDER_ID_LBL');
	// 	$search  [] = "{order_date}";
	// 	$replace [] = $redconfig->convertDateFormat($row->cdate);
	// 	$search  [] = "{customer_note}";
	// 	$replace [] = $row->customer_note;
	// 	$search  [] = "{customer_message}";
	// 	$replace [] = $row->customer_message;
	// 	$search  [] = "{referral_code}";
	// 	$replace [] = $row->referral_code;

	// 	$search  [] = "{payment_method}";
	// 	$replace [] = JText::_($paymentmethod->order_payment_name);

	// 	$txtextra_info = '';

	// 	// Check for bank transfer payment type plugin - `rs_payment_banktransfer` suffixed
	// 	$isBankTransferPaymentType = RedshopHelperPayment::isPaymentType($paymentmethod_detail->element);

	// 	if ($isBankTransferPaymentType)
	// 	{
	// 		$paymentpath   = JPATH_SITE . '/plugins/redshop_payment/'
	// 			. $paymentmethod_detail->element . '/' . $paymentmethod_detail->element . '.xml';
	// 		$paymentparams = new JRegistry($paymentmethod_detail->params);
	// 		$txtextra_info = $paymentparams->get('txtextra_info', '');
	// 	}

	// 	$search  [] = "{payment_extrainfo}";
	// 	$replace [] = $txtextra_info;

	// 	// Set order transaction fee tag
	// 	$orderTransFeeLabel = '';
	// 	$orderTransFee      = '';

	// 	if ($paymentmethod->order_transfee > 0)
	// 	{
	// 		$orderTransFeeLabel = JText::_('COM_REDSHOP_ORDER_TRANSACTION_FEE_LABEL');
	// 		$orderTransFee      = $this->_producthelper->getProductFormattedPrice($paymentmethod->order_transfee);
	// 	}

	// 	$search [] = "{order_transfee_label}";
	// 	$replace[] = $orderTransFeeLabel;

	// 	$search [] = "{order_transfee}";
	// 	$replace[] = $orderTransFee;

	// 	$search [] = "{order_total_incl_transfee}";
	// 	$replace[] = $this->_producthelper->getProductFormattedPrice(
	// 		$paymentmethod->order_transfee + $row->order_total
	// 	);

	// 	if (JRequest::getVar('order_delivery'))
	// 	{
	// 		$search  [] = "{delivery_time_lbl}";
	// 		$replace [] = JText::_('COM_REDSHOP_DELIVERY_TIME');
	// 	}
	// 	else
	// 	{
	// 		$search  [] = "{delivery_time_lbl}";
	// 		$replace [] = " ";
	// 	}

	// 	$search  [] = "{delivery_time}";
	// 	$replace [] = JRequest::getVar('order_delivery');
	// 	$search  [] = "{without_vat}";
	// 	$replace [] = '';
	// 	$search  [] = "{with_vat}";
	// 	$replace [] = '';

	// 	if (strpos($ReceiptTemplate, '{order_detail_link_lbl}') !== false)
	// 	{
	// 		$search [] = "{order_detail_link_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_ORDER_DETAIL_LINK_LBL');
	// 	}

	// 	if (strpos($ReceiptTemplate, '{product_subtotal_lbl}') !== false)
	// 	{
	// 		$search [] = "{product_subtotal_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_PRODUCT_SUBTOTAL_LBL');
	// 	}

	// 	if (strpos($ReceiptTemplate, '{product_subtotal_excl_vat_lbl}') !== false)
	// 	{
	// 		$search [] = "{product_subtotal_excl_vat_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_PRODUCT_SUBTOTAL_EXCL_LBL');
	// 	}

	// 	if (strpos($ReceiptTemplate, '{shipping_with_vat_lbl}') !== false)
	// 	{
	// 		$search [] = "{shipping_with_vat_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_SHIPPING_WITH_VAT_LBL');
	// 	}

	// 	if (strpos($ReceiptTemplate, '{shipping_excl_vat_lbl}') !== false)
	// 	{
	// 		$search [] = "{shipping_excl_vat_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_SHIPPING_EXCL_VAT_LBL');
	// 	}

	// 	if (strpos($ReceiptTemplate, '{product_price_excl_lbl}') !== false)
	// 	{
	// 		$search [] = "{product_price_excl_lbl}";
	// 		$replace[] = JText::_('COM_REDSHOP_PRODUCT_PRICE_EXCL_LBL');
	// 	}

	// 	$billingaddresses  = RedshopHelperOrder::getOrderBillingUserInfo($order_id);
	// 	$shippingaddresses = RedshopHelperOrder::getOrderShippingUserInfo($order_id);

	// 	$search [] = "{requisition_number}";
	// 	$replace[] = ($row->requisition_number) ? $row->requisition_number : "N/A";

	// 	$search [] = "{requisition_number_lbl}";
	// 	$replace[] = JText::_('COM_REDSHOP_REQUISITION_NUMBER');

	// 	$ReceiptTemplate = $this->replaceBillingAddress($ReceiptTemplate, $billingaddresses, $sendmail);
	// 	$ReceiptTemplate = $this->replaceShippingAddress($ReceiptTemplate, $shippingaddresses, $sendmail);

	// 	$message = str_replace($search, $replace, $ReceiptTemplate);
	// 	$message = $this->replacePayment($message, $row->payment_discount, 0, $row->payment_oprand);
	// 	$message = $this->replaceDiscount($message, $row->order_discount, $total_for_discount);
	// 	$message = $this->replaceTax($message, $row->order_tax + $row->order_shipping_tax, $row->tax_after_discount, 1);

	// 	return $message;
	// }

	public function modifyDiscount($cart)
	{
		$calArr                            = $this->calculation($cart);
		$cart['product_subtotal']          = $calArr[1];
		$cart['product_subtotal_excl_vat'] = $calArr[2];
		$c_index                           = 0;
		$v_index                           = 0;
		$discount_amount                   = 0;
		$voucherDiscount                   = 0;
		$couponDiscount                    = 0;
		$discount_excl_vat                 = 0;

		if (!empty($cart['coupon']))
		{
			$c_index = count($cart['coupon']);
		}

		if (!empty($cart['voucher']))
		{
			$v_index = count($cart['voucher']);
		}

		$totaldiscount = 0;

		if (Redshop::getConfig()->get('DISCOUNT_ENABLE') == 1)
		{
			$discount_amount = $this->_producthelper->getDiscountAmount($cart);

			if ($discount_amount > 0)
			{
				$cart = $this->_session->get('cart');
			}
		}

		if (!isset($cart['quotation_id']) || (isset($cart['quotation_id']) && !$cart['quotation_id']))
		{
			$cart['cart_discount'] = $discount_amount;
		}

		for ($v = 0; $v < $v_index; $v++)
		{
			$voucher_code = $cart['voucher'][$v]['voucher_code'];
			unset($cart['voucher'][$v]);
			$voucher_code = JRequest::setVar('discount_code', $voucher_code);
			$cart         = $this->voucher($cart);
		}

		if (array_key_exists('voucher', $cart))
		{
			$voucherDiscount = $this->calculateDiscount('voucher', $cart['voucher']);
		}

		$cart['voucher_discount'] = $voucherDiscount;

		for ($c = 0; $c < $c_index; $c++)
		{
			$coupon_code = $cart['coupon'][$c]['coupon_code'];
			unset($cart['coupon'][$c]);
			$coupon_code = JRequest::setVar('discount_code', $coupon_code);
			$cart        = $this->coupon($cart);
		}

		if (array_key_exists('coupon', $cart))
		{
			$couponDiscount = $this->calculateDiscount('coupon', $cart['coupon']);
		}

		// Point discount
		$pointDiscount = 0;
		
		if (!empty($cart['point_discount']))
		{
			$pointDiscount = $cart['point_discount'];
		}

		$cart['coupon_discount'] = $couponDiscount;
		$codeDsicount            = $voucherDiscount + $couponDiscount;
		$totaldiscount           = $cart['cart_discount'] + $codeDsicount + $pointDiscount;

		$calArr 	 = $this->calculation($cart);
		$tax         = $calArr[5];
		$Discountvat = 0;
		$chktag      = $this->_producthelper->taxexempt_addtocart();

		if ((float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT') && !empty($chktag) && !Redshop::getConfig()->get('APPLY_VAT_ON_DISCOUNT'))
		{
			$vatData = $this->_producthelper->getVatRates();

			if (isset($vatData->tax_rate) && !empty($vatData->tax_rate))
			{
				$productPriceExclVAT = $cart['product_subtotal_excl_vat'];
				$productVAT 		 = $cart['product_subtotal'] - $cart['product_subtotal_excl_vat'];

				if ((int) $productPriceExclVAT > 0)
				{
					$avgVAT      = (($productPriceExclVAT + $productVAT) / $productPriceExclVAT) - 1;
					$Discountvat = ($avgVAT * $totaldiscount) / (1 + $avgVAT);
				}
			}
		}

		$cart['total'] = $calArr[0] - $totaldiscount;

		if ($cart['total'] < 0)
		{
			$cart['total'] = 0;
		}

		$cart['subtotal'] = $calArr[1] + $calArr[3] - $totaldiscount;

		if ($cart['subtotal'] < 0)
		{
			$cart['subtotal'] = 0;
		}

		$cart['subtotal_excl_vat'] = $calArr[2] + ($calArr[3] - $calArr[6]) - ($totaldiscount - $Discountvat);

		if ($cart['total'] <= 0)
		{
			$cart['subtotal_excl_vat'] = 0;
		}

		$cart['product_subtotal']          = $calArr[1];
		$cart['product_subtotal_excl_vat'] = $calArr[2];
		$cart['shipping']                  = $calArr[3];
		$cart['tax']                       = $tax;
		$cart['sub_total_vat']             = $tax + $calArr[6];
		$cart['discount_vat']              = $Discountvat;
		$cart['shipping_tax']              = $calArr[6];
		$cart['discount_ex_vat']           = $totaldiscount - $Discountvat;
		$cart['mod_cart_total']            = $this->GetCartModuleCalc($cart);

		$this->_session->set('cart', $cart);

		return $cart;
	}

	public function replaceTemplate($cart, $cart_data, $checkout = 1)
	{
		if (strpos($cart_data, "{product_loop_start}") !== false && strpos($cart_data, "{product_loop_end}") !== false)
		{
			$template_sdata  = explode('{product_loop_start}', $cart_data);
			$template_start  = $template_sdata[0];
			$template_edata  = explode('{product_loop_end}', $template_sdata[1]);
			$template_end    = $template_edata[1];
			$template_middle = $template_edata[0];
			$template_middle = $this->replaceCartItem($template_middle, $cart, 1, Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE'));
			$cart_data       = $template_start . $template_middle . $template_end;
		}

		$cart_data = $this->replaceLabel($cart_data);

		$total                     = $cart ['total'];
		$subtotal_excl_vat         = $cart ['subtotal_excl_vat'];
		$product_subtotal          = $cart ['product_subtotal'];
		$product_subtotal_excl_vat = $cart ['product_subtotal_excl_vat'];
		$subtotal                  = $cart ['subtotal'];
		$discount_ex_vat           = $cart['discount_ex_vat'];
		$dis_tax                   = 0;
		if (isset($cart['point_discount']))
		{
			$discount_total            = $cart['voucher_discount'] + $cart['coupon_discount'] + $cart['point_discount'];	
		}
		else
		{
			$discount_total            = $cart['voucher_discount'] + $cart['coupon_discount'];
		}
		
		$discount_amount           = $cart ["cart_discount"];
		$tax                       = $cart ['tax'];
		$sub_total_vat             = $cart ['sub_total_vat'];
		$shipping                  = $cart ['shipping'];
		$shippingVat               = $cart ['shipping_tax'];

		if (isset($cart ['discount_type']) === false)
		{
			$cart ['discount_type'] = 0;
		}

		$check_type                = $cart ['discount_type'];
		$chktotal                  = 0;
		$tmp_discount              = $discount_total;
		$discount_total            = $this->_producthelper->getProductFormattedPrice($discount_total + $discount_amount, true);

		if (!Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE') || (Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE') && Redshop::getConfig()->get('SHOW_QUOTATION_PRICE')))
		{
			if (strpos($cart_data, '{product_subtotal_lbl}') !== false)
			{
				$cart_data = str_replace("{product_subtotal_lbl}", JText::_('COM_REDSHOP_PRODUCT_SUBTOTAL_LBL'), $cart_data);
			}

			if (strpos($cart_data, '{product_subtotal_excl_vat_lbl}') !== false)
			{
				$cart_data = str_replace("{product_subtotal_excl_vat_lbl}", JText::_('COM_REDSHOP_PRODUCT_SUBTOTAL_EXCL_LBL'), $cart_data);
			}

			if (strpos($cart_data, '{shipping_with_vat_lbl}') !== false)
			{
				$cart_data = str_replace("{shipping_with_vat_lbl}", JText::_('COM_REDSHOP_SHIPPING_WITH_VAT_LBL'), $cart_data);
			}

			if (strpos($cart_data, '{shipping_excl_vat_lbl}') !== false)
			{
				$cart_data = str_replace("{shipping_excl_vat_lbl}", JText::_('COM_REDSHOP_SHIPPING_EXCL_VAT_LBL'), $cart_data);
			}

			if (strpos($cart_data, '{product_price_excl_lbl}') !== false)
			{
				$cart_data = str_replace("{product_price_excl_lbl}", JText::_('COM_REDSHOP_PRODUCT_PRICE_EXCL_LBL'), $cart_data);
			}

			$cart_data = str_replace("{total}", "<span id='spnTotal'>" . $this->_producthelper->getProductFormattedPrice($total, true) . "</span>", $cart_data);
			$cart_data = str_replace("{total_excl_vat}", "<span id='spnTotal'>" . $this->_producthelper->getProductFormattedPrice($subtotal_excl_vat) . "</span>", $cart_data);

			$chktag = $this->_producthelper->getApplyVatOrNot($cart_data);

			if (!empty($chktag))
			{
				$cart_data = str_replace("{subtotal}", $this->_producthelper->getProductFormattedPrice($subtotal), $cart_data);
				$cart_data = str_replace("{product_subtotal}", $this->_producthelper->getProductFormattedPrice($product_subtotal), $cart_data);
			}
			else
			{
				$cart_data = str_replace("{subtotal}", $this->_producthelper->getProductFormattedPrice($subtotal_excl_vat), $cart_data);
				$cart_data = str_replace("{product_subtotal}", $this->_producthelper->getProductFormattedPrice($product_subtotal_excl_vat), $cart_data);
			}

			if ((strpos($cart_data, "{discount_denotation}") !== false || strpos($cart_data, "{shipping_denotation}") !== false) && ($discount_total != 0 || $shipping != 0))
			{
				$cart_data = str_replace("{denotation_label}", JText::_('COM_REDSHOP_DENOTATION_TXT'), $cart_data);
			}
			else
			{
				$cart_data = str_replace("{denotation_label}", "", $cart_data);
			}

			if (strpos($cart_data, "{discount_excl_vat}") !== false)
			{
				$cart_data = str_replace("{discount_denotation}", "*", $cart_data);
			}
			else
			{
				$cart_data = str_replace("{discount_denotation}", "", $cart_data);
			}

			$cart_data = str_replace("{subtotal_excl_vat}", $this->_producthelper->getProductFormattedPrice($subtotal_excl_vat), $cart_data);
			$cart_data = str_replace("{product_subtotal_excl_vat}", $this->_producthelper->getProductFormattedPrice($product_subtotal_excl_vat), $cart_data);
			$cart_data = str_replace("{sub_total_vat}", $this->_producthelper->getProductFormattedPrice($sub_total_vat), $cart_data);
			$cart_data = str_replace("{discount_excl_vat}", $this->_producthelper->getProductFormattedPrice($discount_ex_vat), $cart_data);

			$rep = true;

			if (!$checkout)
			{
				if (!Redshop::getConfig()->get('SHOW_SHIPPING_IN_CART') || !Redshop::getConfig()->get('SHIPPING_METHOD_ENABLE'))
				{
					$rep = false;
				}
			}
			else
			{
				if (!Redshop::getConfig()->get('SHIPPING_METHOD_ENABLE'))
				{
					$rep = false;
				}
			}

			if (!empty($rep))
			{
				if (strpos($cart_data, "{shipping_excl_vat}") !== false)
				{
					$cart_data = str_replace("{shipping_denotation}", "*", $cart_data);
				}
				else
				{
					$cart_data = str_replace("{shipping_denotation}", "", $cart_data);
				}

				$cart_data = str_replace("{order_shipping}", $this->_producthelper->getProductFormattedPrice($shipping, true), $cart_data);
				$cart_data = str_replace("{shipping_excl_vat}", "<span id='spnShippingrate'>" . $this->_producthelper->getProductFormattedPrice($shipping - $cart['shipping_tax'], true) . "</span>", $cart_data);
				$cart_data = str_replace("{shipping_lbl}", JText::_('COM_REDSHOP_CHECKOUT_SHIPPING_LBL'), $cart_data);
				$cart_data = str_replace("{shipping}", $this->_producthelper->getProductFormattedPrice($shipping, true), $cart_data);
				$cart_data = str_replace("{tax_with_shipping_lbl}", JText::_('COM_REDSHOP_CHECKOUT_SHIPPING_LBL'), $cart_data);
				$cart_data = str_replace("{vat_shipping}", $this->_producthelper->getProductFormattedPrice($shippingVat), $cart_data);
			}
			else
			{
				$cart_data = str_replace("{order_shipping}", '', $cart_data);
				$cart_data = str_replace("{shipping_excl_vat}", '', $cart_data);
				$cart_data = str_replace("{shipping_lbl}", '', $cart_data);
				$cart_data = str_replace("{shipping}", '', $cart_data);
				$cart_data = str_replace("{tax_with_shipping_lbl}", '', $cart_data);
				$cart_data = str_replace("{vat_shipping}", '', $cart_data);
				$cart_data = str_replace("{shipping_denotation}", "", $cart_data);
			}
		}
		else
		{
			$cart_data = str_replace("{total}", "<span id='spnTotal'></span>", $cart_data);
			$cart_data = str_replace("{shipping_excl_vat}", "<span id='spnShippingrate'></span>", $cart_data);
			$cart_data = str_replace("{order_shipping}", "", $cart_data);
			$cart_data = str_replace("{shipping_lbl}", '', $cart_data);
			$cart_data = str_replace("{shipping}", '', $cart_data);
			$cart_data = str_replace("{subtotal}", "", $cart_data);
			$cart_data = str_replace("{tax_with_shipping_lbl}", '', $cart_data);
			$cart_data = str_replace("{vat_shipping}", '', $cart_data);
			$cart_data = str_replace("{subtotal_excl_vat}", "", $cart_data);
			$cart_data = str_replace("{shipping_excl_vat}", "", $cart_data);
			$cart_data = str_replace("{subtotal_excl_vat}", "", $cart_data);
			$cart_data = str_replace("{product_subtotal_excl_vat}", "", $cart_data);
			$cart_data = str_replace("{product_subtotal}", "", $cart_data);
			$cart_data = str_replace("{sub_total_vat}", "", $cart_data);
			$cart_data = str_replace("{discount_excl_vat}", "", $cart_data);
			$cart_data = str_replace("{discount_denotation}", "", $cart_data);
			$cart_data = str_replace("{shipping_denotation}", "", $cart_data);
			$cart_data = str_replace("{denotation_label}", "", $cart_data);
			$cart_data = str_replace("{total_excl_vat}", "", $cart_data);
		}

		if (!Redshop::getConfig()->get('APPLY_VAT_ON_DISCOUNT'))
		{
			$total_for_discount = $subtotal_excl_vat;
		}
		else
		{
			$total_for_discount = $subtotal;
		}

		$cart_data = $this->replaceDiscount($cart_data, $discount_amount + $tmp_discount, $total_for_discount, Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE'));

		if ($checkout)
		{
			$cart_data = $this->replacePayment($cart_data, $cart['payment_amount'], 0, $cart['payment_oprand']);
		}
		else
		{
			$paymentOprand = (isset($cart['payment_oprand'])) ? $cart['payment_oprand'] : '-';
			$cart_data     = $this->replacePayment($cart_data, 0, 1, $paymentOprand);
		}

		$cart_data = $this->replaceTax($cart_data, $tax + $shippingVat, $discount_amount + $tmp_discount, 0, Redshop::getConfig()->get('DEFAULT_QUOTATION_MODE'));

		return $cart_data;
	}
}