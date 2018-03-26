<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('redshop.library');
JLoader::load('RedshopHelperUser');
JLoader::import('product', JPATH_SITE . '/libraries/redshop/helper');

/**
 * Findproducts controller.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class RedproductfinderControllerFindproducts extends JControllerForm
{
	/**
	 * This method are core process to get product from ajax
	 *
	 * @return void
	 */
	function find()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$param = JComponentHelper::getParams('com_redproductfinder');
		$input = $app->input;

		$model = JModelLegacy::getInstance("FindProducts", "RedproductfinderModel");

		$layout = new JLayoutFile('result');

		$post = $input->post->get('redform', array(), 'filter');
		$view = $input->post->get("view", "", 'filter');

		$model->setState("redform.data", $post);
		$model->setState("redform.view", $view);

		$list = $model->getItem();

		// Get all product from here
		foreach ( $list as $k => $value )
		{
			$products[] = $value;
		}

		$pagination = $model->getPagination();
		$orderBy = $model->getState('order_by');
		$total = $model->getTotal();

		// Get layout HTML
		if (isset($products))
		{
			$html = $layout->render(
			array(
					"products" => $products,
					"model" => $model,
					"post"	   => $post,
					"template_id" => $post["template_id"],
					"getPagination" => $pagination,
					"orderby" => $orderBy,
					'total' => $total,
				)
			);

			echo $html;
		}
		else
		{
			echo JText::_('COM_REDPRODUCTFINDER_NOT_FOUND');
		}

		$app->close();
	}

	/**
	 * This method are clear form data
	 *
	 * @return void
	 */
	function clear()
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$session->clear('form_data');
		$session->clear('product_id_list');
		$session->set('form_data', "");
		$session->set('product_id_list', "");

		$app->close();
	}
}
