
<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
echo 'here';

class RedshopControllerOrder extends RedshopControllerOrderDefault
{
	public function export_data()
	{
		/**
		 * new order export for paid customer support
		 */
		$extrafile = JPATH_SITE . '/administrator/components/com_redshop/extras/order_export.php';

		if (file_exists($extrafile))
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/extras/order_export.php';

			$orderExport = new orderExport;
			$orderExport->createOrderExport();
			exit;
		}

		$producthelper = new producthelper;
		$order_function = new order_functions;
		$model = $this->getModel('order');

		$product_count = array();
		$db = JFactory::getDbo();

		$cid = JRequest::getVar('cid', array(0), 'method', 'array');
		$data = $model->export_data($cid);
		$order_id = implode(',', $cid);
		$where = "";

		if ($order_id != 0)
		{
			$where .= " where order_id IN (" . $order_id . ") ";
		}

		$sql = "SELECT order_id,count(order_item_id) as noproduct FROM `#__redshop_order_item`  " . $where . " GROUP BY order_id";

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: text/x-xls");
		header("Content-type: text/xls");
		header("Content-type: application/xls");
		header('Content-Disposition: attachment; filename=Order.xls');

		$db->setQuery($sql);
		$no_products = $db->loadObjectList();

		for ($i = 0, $in = count($data); $i < $in; $i++)
		{
			$product_count [] = $no_products [$i]->noproduct;
		}

		$no_products = max($product_count);

		echo "Ngày CT, Số CT, Tỷ giá, Mã giao dịch, Mã khách, Người mua, Diễn giải, Mã thanh toán, Mã hàng,";
		echo "Tên mặt hàng, ĐVT, Mã kho, SL đặt, SL xuất, Giá bán, Giá bán sau thuế, Tiền, Mã thuế, Thuế suất, Thuế,";
		echo "Ngày giao, Vụ việc\n";

		for ($i = 0, $in = count($data); $i < $in; $i++)
		{
			$no_items = $order_function->getOrderItemDetail($data[$i]->order_id);

			for ($it = 0; $it < count($no_items); $it++)
			{
				echo date('d-m-Y H:i', $data[$i]->cdate) . " ,";
				echo $data[$i]->order_number . ",";
				echo '' . ",";
				echo $data[$i]->order_id . ",";
				echo $data[$i]->user_id . ",";
				echo $data[$i]->firstname . " " . $data[$i]->lastname . ",";
				echo str_replace(array("\r\n", "\n\r", "\n", "\r"), ' ', str_replace(',', '-', $data[$i]->customer_note)) . ",";
				echo $data[$i]->order_id . ",";
				echo $data[$i]->order_id . ",";
				echo str_replace(',', ' - ', $no_items[$it]->order_item_name) . ",";
				echo REDCURRENCY_SYMBOL . ",";
				echo $no_items[$it]->stockroom_id . ",";
				echo $no_items[$it]->product_quantity . ",";
				echo $no_items[$it]->product_quantity . ",";
				echo $no_items[$it]->product_item_price . ' ' . REDCURRENCY_SYMBOL . ",";
				echo $no_items[$it]->product_final_price . ' ' . REDCURRENCY_SYMBOL . ",";
				echo $data[$i]->order_total . ' ' . REDCURRENCY_SYMBOL . ",";
				echo $data[$i]->order_tax_details . ",";
				echo $data[$i]->order_tax . ",";
				echo $data[$i]->order_tax . ",";
				echo date('d-m-Y H:i', $data[$i]->mdate) . ",";
				echo '' . "," . "\n";
			}
		}

		exit ();
	}
}