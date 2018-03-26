<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class redshopMail extends redShopMailDefault
{
	protected $_order_functions;

	/**
	 * sendOrderMail function.
	 *
	 * @param   int  $order_id  Order ID.
	 *
	 * @return bool
	 */
	public function sendOrderMail($order_id, $onlyAdmin = false)
	{
		$redconfig = Redconfiguration::getInstance();
		$producthelper = productHelper::getInstance();
		$session = JFactory::getSession();
		$config = JFactory::getConfig();

		$this->_order_functions = order_functions::getInstance();
		$this->_carthelper = rsCarthelper::getInstance();

		// Set the e-mail parameters
		$from = $config->get('mailfrom');
		$fromname = $config->get('fromname');
		$user = JFactory::getUser();

		if (USE_AS_CATALOG)
		{
			$mailinfo = $this->getMailtemplate(0, "catalogue_order");
		}
		else
		{
			$mailinfo = $this->getMailtemplate(0, "order");
		}

		if (count($mailinfo) > 0)
		{
			$message = $mailinfo[0]->mail_body;
			$subject = $mailinfo[0]->mail_subject;
		}
		else
		{
			return false;
		}

		$row = $this->_order_functions->getOrderDetails($order_id);

		$orderpayment = $this->_order_functions->getOrderPaymentDetail($order_id);
		$paymentmethod = $this->_order_functions->getPaymentMethodInfo($orderpayment[0]->payment_method_class);

		$paymentmethod = $paymentmethod[0];

		// It is necessory to take billing info from order user info table
		// Order mail output should reflect the checkout process"

		$message = str_replace("{order_mail_intro_text_title}", JText::_('COM_REDSHOP_ORDER_MAIL_INTRO_TEXT_TITLE'), $message);
		$message = str_replace("{order_mail_intro_text}", JText::_('COM_REDSHOP_ORDER_MAIL_INTRO_TEXT'), $message);

		$message = $this->_carthelper->replaceOrderTemplate($row, $message, true);
		$rowitem = $this->_order_functions->getOrderItemDetail($order_id);

		$manufacturer_email = array();
		$supplier_email = array();

		for ($i = 0, $in = count($rowitem); $i < $in; $i++)
		{
			$product          = Redshop::product((int) $rowitem[$i]->product_id);
			$manufacturerData = $producthelper->getSection("manufacturer", $product->manufacturer_id);

			if (count($manufacturerData) > 0)
			{
				if ($manufacturerData->manufacturer_email != '')
				{
					$manufacturer_email[$i] = $manufacturerData->manufacturer_email;
				}
			}

			$supplierData = $producthelper->getSection("supplier", $product->supplier_id);

			if (count($supplierData) > 0)
			{
				if ($supplierData->supplier_email != '')
				{
					$supplier_email[$i] = $supplierData->supplier_email;
				}
			}
		}

		$arr_discount_type = array();
		$arr_discount = explode('@', $row->discount_type);
		$discount_type = '';

		for ($d = 0, $dn = count($arr_discount); $d < $dn; $d++)
		{
			if ($arr_discount[$d])
			{
				$arr_discount_type = explode(':', $arr_discount[$d]);

				if ($arr_discount_type[0] == 'c')
				{
					$discount_type .= JText::_('COM_REDSHOP_COUPON_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
				}

				if ($arr_discount_type[0] == 'v')
				{
					$discount_type .= JText::_('COM_REDSHOP_VOUCHER_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
				}
			}
		}

		if (!$discount_type)
		{
			$discount_type = JText::_('COM_REDSHOP_NO_DISCOUNT_AVAILABLE');
		}

		$search[]        = "{discount_type}";
		$replace[]       = $discount_type;
		$split_amount    = 0;

		$issplitdisplay  = '';
		$issplitdisplay2 = '';

		if ($row->split_payment)
		{
			$issplitdisplay = "<br/>" . JText::_('COM_REDSHOP_RECEIPT_PARTIALLY_PAID_AMOUNT') . ": "
				. $producthelper->getProductFormattedPrice($split_amount);
			$issplitdisplay2 = "<br/>" . JText::_('COM_REDSHOP_REMAINING_PARTIALLY_AMOUNT') . ": "
				. $producthelper->getProductFormattedPrice($split_amount);
		}

		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuItem = $menu->getItems('link', 'index.php?option=com_redshop&view=order_detail', true);

		$orderdetailurl   = JURI::root() . 'index.php?option=com_redshop&view=order_detail&oid=' . $order_id . '&encr=' . $row->encr_key . '&Itemid=' . $menuItem->id;
		$search[]         = "{order_detail_link}";
		$replace[]        = "<a href='" . $orderdetailurl . "'>" . JText::_("COM_REDSHOP_ORDER_MAIL") . "</a>";

		$billingaddresses = RedshopHelperOrder::getOrderBillingUserInfo($order_id);
		$message          = str_replace($search, $replace, $message);
		$message          = $this->imginmail($message);
		$thirdpartyemail  = $billingaddresses->thirdparty_email;
		$email = $billingaddresses->user_email;

		$search[]      = "{order_id}";
		$replace[]     = $row->order_id;
		$search[]      = "{order_number}";
		$replace[]     = $row->order_number;
		$search_sub[]  = "{order_id}";
		$replace_sub[] = $row->order_id;
		$search_sub[]  = "{order_number}";
		$replace_sub[] = $row->order_number;
		$search_sub[]  = "{shopname}";
		$replace_sub[] = SHOP_NAME;
		$search_sub[]  = "{order_date}";
		$replace_sub[] = $redconfig->convertDateFormat($row->cdate);
		$subject       = str_replace($search_sub, $replace_sub, $subject);

		// Send the e-mail
		if ($email != "")
		{
			$mailbcc = array();

			if (trim($mailinfo[0]->mail_bcc) != "")
			{
				$mailbcc = explode(",", $mailinfo[0]->mail_bcc);
			}

			$bcc      = (trim(ADMINISTRATOR_EMAIL) != '') ? explode(",", trim(ADMINISTRATOR_EMAIL)) : array();
			$bcc      = array_merge($bcc, $mailbcc);
			$fullname = $billingaddresses->firstname . " " . $billingaddresses->lastname;

			if ($billingaddresses->is_company == 1 && $billingaddresses->company_name != "")
			{
				$fullname = $billingaddresses->company_name;
			}

			$subject = str_replace("{fullname}", $fullname, $subject);
			$subject = str_replace("{firstname}", $billingaddresses->firstname, $subject);
			$subject = str_replace("{lastname}", $billingaddresses->lastname, $subject);
			$message = str_replace("{fullname}", $fullname, $message);
			$message = str_replace("{firstname}", $billingaddresses->firstname, $message);
			$message = str_replace("{lastname}", $billingaddresses->lastname, $message);
			$body    = $message;

			// As only need to send email to administrator,
			// Here variables are changed to use bcc email - from redSHOP configuration - Administrator Email
			if ($onlyAdmin)
			{
				$email           = $bcc;
				$thirdpartyemail = '';
				$bcc             = null;
			}

			if ($thirdpartyemail != '')
			{
				if (!JFactory::getMailer()->sendMail($from, $fromname, $thirdpartyemail, $subject, $body, 1, null, $bcc))
				{
					JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
				}
			}

			if (!JFactory::getMailer()->sendMail($from, $fromname, $email, $subject, $body, 1, null, $bcc))
			{
				JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
			}
		}

		// As email only need to send admin no need to send email to others.
		if ($onlyAdmin)
		{
			return true;
		}

		if (MANUFACTURER_MAIL_ENABLE)
		{
			sort($manufacturer_email);

			for ($man = 0; $man < count($manufacturer_email); $man++)
			{
				if (!JFactory::getMailer()->sendMail($from, $fromname, $manufacturer_email[$man], $subject, $body, 1))
				{
					JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
				}
			}
		}

		if (SUPPLIER_MAIL_ENABLE)
		{
			sort($supplier_email);

			for ($sup = 0; $sup < count($supplier_email); $sup++)
			{
				if (!JFactory::getMailer()->sendMail($from, $fromname, $supplier_email[$sup], $subject, $body, 1))
				{
					JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
				}
			}
		}

		// Invoice mail send
		if (INVOICE_MAIL_ENABLE && $row->order_payment_status == "Paid")
		{
			$this->sendInvoiceMail($order_id);
		}

		return true;
	}

	public function sendOrderSpecialDiscountMail($order_id)
	{
		$producthelper = productHelper::getInstance();

		$config        = JFactory::getConfig();
		$mailbcc       = array();
		$mailinfo      = $this->getMailtemplate(0, "order_special_discount");

		if (count($mailinfo) > 0)
		{
			$message = $mailinfo[0]->mail_body;
			$subject = $mailinfo[0]->mail_subject;

			if (trim($mailinfo[0]->mail_bcc) != "")
			{
				$mailbcc = explode(",", $mailinfo[0]->mail_bcc);
			}
		}
		else
		{
			return false;
		}

		$manufacturer_email = array();

		$row              = $this->_order_functions->getOrderDetails($order_id);
		$billingaddresses = RedshopHelperOrder::getOrderBillingUserInfo($order_id);
		$orderpayment     = $this->_order_functions->getOrderPaymentDetail($order_id);
		$paymentmethod    = $this->_order_functions->getPaymentMethodInfo($orderpayment[0]->payment_method_class);
		$paymentmethod    = $paymentmethod[0];
		$message          = $this->_carthelper->replaceOrderTemplate($row, $message, true);

		// Set order paymethod name
		$search[]       = "{shopname}";
		$replace[]      = SHOP_NAME;
		$search[]       = "{payment_lbl}";
		$replace[]      = JText::_('COM_REDSHOP_PAYMENT_METHOD');
		$search[]       = "{payment_method}";
		$replace[]      = "";
		$search[]       = "{special_discount}";
		$replace[]      = $row->special_discount . '%';
		$search[]       = "{special_discount_amount}";
		$replace[]      = $producthelper->getProductFormattedPrice($row->special_discount_amount);
		$search[]       = "{special_discount_lbl}";
		$replace[]      = JText::_('COM_REDSHOP_SPECIAL_DISCOUNT');

		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuItem = $menu->getItems('link', 'index.php?option=com_redshop&view=order_detail', true);

		$orderdetailurl   = JURI::root() . 'index.php?option=com_redshop&view=order_detail&oid=' . $order_id . '&encr=' . $row->encr_key . '&Itemid=' . $menuItem->id;

		$search[]       = "{order_detail_link}";
		$replace[]      = "<a href='" . $orderdetailurl . "'>" . JText::_("COM_REDSHOP_ORDER_MAIL") . "</a>";

		// Check for bank transfer payment type plugin - `rs_payment_banktransfer` suffixed
		$isBankTransferPaymentType = RedshopHelperPayment::isPaymentType($paymentmethod->element);

		if ($isBankTransferPaymentType)
		{
			$paymentparams = new JRegistry($paymentmethod->params);
			$txtextra_info = $paymentparams->get('txtextra_info', '');

			$search[] = "{payment_extrainfo}";
			$replace[] = $txtextra_info;
		}

		$message  = str_replace($search, $replace, $message);
		$message  = $this->imginmail($message);

		$email    = $billingaddresses->user_email;
		$from     = $config->get('mailfrom');
		$fromname = $config->get('fromname');
		$body     = $message;
		$subject  = str_replace($search, $replace, $subject);

		if ($email != "")
		{
			$bcc = null;

			if (trim(ADMINISTRATOR_EMAIL) != '')
			{
				$bcc = explode(",", trim(ADMINISTRATOR_EMAIL));
			}

			$bcc = array_merge($bcc, $mailbcc);

			if (SPECIAL_DISCOUNT_MAIL_SEND == '1')
			{
				if (!JFactory::getMailer()->sendMail($from, $fromname, $email, $subject, $body, 1, null, $bcc))
				{
					JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
				}
			}
		}

		if (MANUFACTURER_MAIL_ENABLE)
		{
			sort($manufacturer_email);

			for ($man = 0; $man < count($manufacturer_email); $man++)
			{
				if (!JFactory::getMailer()->sendMail($from, $fromname, $manufacturer_email[$man], $subject, $body, 1))
				{
					JError::raiseWarning(JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
				}
			}
		}

		return true;
	}
}
