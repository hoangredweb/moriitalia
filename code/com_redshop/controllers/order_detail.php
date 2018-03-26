<?php

class RedshopControllerOrder_detail extends RedshopControllerOrder_detailDefault
{
	/**
	 * Notify payment function
	 *
	 * @return  void
	 */
	public function notify_payment()
	{
		$app         = JFactory::getApplication();
		$db          = JFactory::getDbo();
		$user        = JFactory::getUser();
		$request     = JRequest::get('request');
		$Itemid      = JRequest::getInt('Itemid');
		$objOrder    = order_functions::getInstance();

		JPluginHelper::importPlugin('redshop_payment');
		JPluginHelper::importPlugin('redshop_user');
		JPluginHelper::importPlugin('redshop_coupon');
		$dispatcher = JDispatcher::getInstance();

		$results = $dispatcher->trigger(
			'onNotifyPayment' . $request['payment_plugin'],
			array(
				$request['payment_plugin'],
				$request
			)
		);

		$msg = $results[0]->msg;

		if (array_key_exists("order_id_temp", $results[0]))
		{
			$order_id = $results[0]->order_id_temp;
		}
		else
		{
			$order_id = $results[0]->order_id;
		}

		// Change Order Status based on resutls
		$objOrder->changeorderstatus($results[0]);

		$model     = $this->getModel('order_detail');
		$resetcart = $model->resetcart();
		$orderDetail = $objOrder->getOrderDetails($order_id);

		if ($results[0]->order_payment_status_code == 'Paid')
		{
			if ((float) $orderDetail->order_total >= (float) Redshop::getConfig()->get('USER_AMOUNT_TO_POINT'))
			{
				$dispatcher->trigger(
					'onAfterOrderSuccess',
					array(
						$user->id
					)
				);
			}

			$dispatcher->trigger('updateCoupon', array($user->email));
		}

		/*
		 * Plugin will trigger onAfterNotifyPayment
		 */
		$dispatcher->trigger(
			'onAfterNotifyPayment' . $request['payment_plugin'],
			array(
				$request['payment_plugin'],
				$order_id
			)
		);

		if ($request['payment_plugin'] == "rs_payment_payer")
		{
			die("TRUE");
		}

		if ($request['payment_plugin'] != "rs_payment_worldpay")
		{
			// New checkout flow
			$redirect_url = JRoute::_(JURI::base() . "index.php?option=com_redshop&view=order_detail&layout=receipt&Itemid=$Itemid&oid=" . $order_id);
			$this->setRedirect($redirect_url, $msg);
		}
	}
}