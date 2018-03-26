<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class RedshopModelOrder_detail extends RedshopModelOrder_detailDefault
{
	public function update_shippingrates($data)
	{
		$shippinghelper = shipping::getInstance();

		// Get Order Info
		$orderdata = $this->getTable('order_detail');
		$orderdata->load($this->_id);

		if ($data['shipping_rate_id'] != "")
		{
			// Get Shipping rate info Info
			$neworder_shipping = $shippinghelper->decryptShipping(str_replace(" ", "+", $data['shipping_rate_id']));
			//$neworder_shipping = explode("|", $decry);

			if ($data['shipping_rate_id'] != $orderdata->ship_method_id || $neworder_shipping[0] == 'plgredshop_shippingdefault_shipping_gls')
			{
				if (count($neworder_shipping) > 4)
				{
					// Shipping_rate_value
					$orderdata->order_total = $orderdata->order_total - $orderdata->order_shipping + $neworder_shipping[3];
					$orderdata->order_shipping = $neworder_shipping[3];
					$orderdata->ship_method_id = $data['shipping_rate_id'];
					$orderdata->order_shipping_tax = (isset($neworder_shipping[6]) && $neworder_shipping[6]) ? $neworder_shipping[6] : 0;
					$orderdata->mdate = time();
					$orderdata->shop_id = $data['shop_id'] . "###" . $data['gls_mobile'];

					if (!$orderdata->store())
					{
						return false;
					}

					// Economic Integration start for invoice generate
					if (Redshop::getConfig()->get('ECONOMIC_INTEGRATION') == 1)
					{
						economic::getInstance()->renewInvoiceInEconomic($orderdata);
					}
				}
			}
		}
		return true;
	}
}
