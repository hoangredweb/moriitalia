<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder instead
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');

/**
 * Order helper for backend
 *
 * @since       __DEPLOY_VERSION__
 *
 * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder instead
 */
class order_functions extends order_functionsDefault
{
	/**
	 * Update order status and trigger emails based on status.
	 *
	 * @return  void
	 *
	 * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder::updateStatus() instead
	 */
	public function update_status()
	{
		$app             = JFactory::getApplication();
		$helper          = redhelper::getInstance();
		$productHelper   = productHelper::getInstance();
		$stockroomHelper = rsstockroomhelper::getInstance();

		$newStatus       = $app->input->getCmd('status');
		$paymentStatus   = $app->input->getString('order_paymentstatus');
		$return          = $app->input->getCmd('return');

		$customerNote    = $app->input->get('customer_note', array(), 'array');
		$customerNote    = stripslashes($customerNote[0]);

		$oid             = $app->input->get('order_id', array(), 'method', 'array');
		$orderId         = $oid[0];

		$isProduct       = $app->input->getInt('isproduct', 0);
		$productId       = $app->input->getInt('product_id', 0);
		$orderItemId     = $app->input->getInt('order_item_id', 0);

		if (isset($paymentStatus))
		{
			$this->updateOrderPaymentStatus($orderId, $paymentStatus);
		}

		if ($paymentStatus == "Paid")
		{
			$orderDetail = $this->getOrderDetails($orderId);
			JPluginHelper::importPlugin('redshop_user');
			JPluginHelper::importPlugin('redshop_coupon');
			$dispatcher = JDispatcher::getInstance();

			if ((float) $orderDetail->order_total >= (float) Redshop::getConfig()->get('USER_AMOUNT_TO_POINT'))
			{
				$dispatcher->trigger(
					'onAfterOrderSuccess',
					array(
						$orderDetail->user_id
					)
				);
			}

			$billing = $this->getBillingAddress($orderDetail->user_id);
			$dispatcher->trigger('updateCoupon', array($billing->user_email));
		}

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redshop/tables');
		$orderLog = JTable::getInstance('order_status_log', 'Table');

		if (!$isProduct)
		{
			$data['order_id']             = $orderId;
			$data['order_status']         = $newStatus;
			$data['order_payment_status'] = $paymentStatus;
			$data['date_changed']         = time();
			$data['customer_note']        = $customerNote;

			if (!$orderLog->bind($data))
			{
				JFactory::getApplication()->enqueueMessage($orderLog->getError(), 'error');

				return;
			}

			if (!$orderLog->store())
			{
				throw new Exception($orderLog->getError());
			}

			$this->updateOrderComment($orderId, $customerNote);

			$requisitionNumber = $app->input->getString('requisition_number', '');

			if ('' != $requisitionNumber)
			{
				$this->updateOrderRequisitionNumber($orderId, $requisitionNumber);
			}

			// Changing the status of the order
			$this->updateOrderStatus($orderId, $newStatus);

			// Trigger function on Order Status change
			JPluginHelper::importPlugin('order');
			RedshopHelperUtility::getDispatcher()->trigger(
				'onAfterOrderStatusUpdate',
				array($this->getOrderDetails($orderId))
			);

			if ($paymentStatus == "Paid")
			{
				JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_redshop/models');
				$checkoutModel = JModelLegacy::getInstance('Checkout', 'RedshopModel');
				$checkoutModel->sendGiftCard($orderId);

				// Send the Order mail
				$redshopMail = redshopMail::getInstance();

				if (Redshop::getConfig()->get('ORDER_MAIL_AFTER') && $newStatus == 'C')
				{
					$redshopMail->sendOrderMail($orderId);
				}

				elseif (Redshop::getConfig()->get('INVOICE_MAIL_ENABLE'))
				{
					$redshopMail->sendInvoiceMail($orderId);
				}
			}

			$this->createWebPacklabel($orderId, $newStatus, $paymentStatus);
		}

		$this->updateOrderItemStatus($orderId, $productId, $newStatus, $customerNote, $orderItemId);
		$helper->clickatellSMS($orderId);

		switch ($newStatus)
		{
			case "X";

				$orderProducts = $this->getOrderItemDetail($orderId);

				for ($i = 0, $in = count($orderProducts); $i < $in; $i++)
				{
					$prodid = $orderProducts[$i]->product_id;
					$prodqty = $orderProducts[$i]->stockroom_quantity;

					// When the order is set to "cancelled",product will return to stock
					RedshopHelperStockroom::manageStockAmount($prodid, $prodqty, $orderProducts[$i]->stockroom_id);
					$productHelper->makeAttributeOrder($orderProducts[$i]->order_item_id, 0, $prodid, 1);
				}
				break;

			case "RT":

				if ($isProduct)
				{
					// Changing the status of the order item to Returned
					$this->updateOrderItemStatus($orderId, $productId, "RT", $customerNote, $orderItemId);

					// Changing the status of the order to Partially Returned
					$this->updateOrderStatus($orderId, "PRT");
				}

				break;

			case "RC":

				if ($isProduct)
				{
					// Changing the status of the order item to Reclamation
					$this->updateOrderItemStatus($orderId, $productId, "RC", $customerNote, $orderItemId);

					// Changing the status of the order to Partially Reclamation
					$this->updateOrderStatus($orderId, "PRC");
				}

				break;

			case "S":

				if ($isProduct)
				{
					// Changing the status of the order item to Reclamation
					$this->updateOrderItemStatus($orderId, $productId, "S", $customerNote, $orderItemId);

					// Changing the status of the order to Partially Reclamation
					$this->updateOrderStatus($orderId, "PS");
				}

				break;

			case "C":

				// SensDownload Products
				if ($paymentStatus == "Paid")
				{
					$this->SendDownload($orderId);
				}

				break;
		}

		if ($app->input->getCmd('order_sendordermail') == 'true')
		{
			$this->changeOrderStatusMail($orderId, $newStatus, $customerNote);
		}

		$this->createBookInvoice($orderId, $newStatus);

		$msg       = JText::_('COM_REDSHOP_ORDER_STATUS_SUCCESSFULLY_SAVED_FOR_ORDER_ID') . " " . $orderId;

		$isArchive = ($app->input->getInt('isarchive')) ? '&isarchive=1' : '';

		if ($return == 'order')
		{
			$app->redirect('index.php?option=com_redshop&view=' . $return . '' . $isArchive . '', $msg);
		}
		else
		{
			$tmpl = $app->input->getCmd('tmpl');

			if ('' != $tmpl)
			{
				$app->redirect('index.php?option=com_redshop&view=' . $return . '&cid[]=' . $orderId . '&tmpl=' . $tmpl . '' . $isArchive . '', $msg);
			}
			else
			{
				$app->redirect('index.php?option=com_redshop&view=' . $return . '&cid[]=' . $orderId . '' . $isArchive . '', $msg);
			}
		}
	}
}