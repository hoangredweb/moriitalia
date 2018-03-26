<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . "/administrator/components/com_redshop/helpers/redshop.cfg.php";
JLoader::import('redshop.library');
JLoader::load('RedshopHelperUser');
JLoader::import('product', JPATH_SITE . '/libraries/redshop/helper');

/**
 * Findproducts View.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  View
 * @since       2.0
 */
class RedproductfinderViewFindProducts extends RViewSite
{
	/**
	 * Display the template list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 */
	function display($tpl = null)
	{
		$app        = JFactory::getApplication();
		$input      = JFactory::getApplication()->input;
		$user       = JFactory::getUser();
		$dispatcher	= RFactory::getDispatcher();
		$session 	= JFactory::getSession();

		JHtml::_('redshopjquery.framework');

		// Add redBox
		JHtml::script('com_redshop/redbox.js', false, true);
		JHtml::script('com_redshop/attribute.js', false, true);
		JHtml::script('com_redshop/common.js', false, true);

		$this->item  		= $this->get('Item');
		$this->state 		= $this->get('State');
		$this->Itemid 		= $input->getInt('Itemid', null);
		$this->option 		= $input->getString('option', 'com_redshop');
		$this->dispatcher	= $dispatcher;
		$model = JModelLegacy::getInstance("FindProducts", "RedproductfinderModel");
		$this->model = $model;

		$data = $input->post->get("redform", array(), "filter");
		$json = $input->post->get('jsondata', "", "filter");
		$formData = $session->get('form_data');
		$categories = $input->getInt('category');

		if ($data)
		{
			$pk = $data;
		}
		elseif (!empty($json))
		{
			$pk = json_decode($json, true);
		}
		else
		{
			$pk = json_decode($formData, true);
		}

		if (!empty($categories))
		{
			$pk['category'] = $categories;
			unset($pk['filterprice']);
		}
		elseif ($categories === 0)
		{
			$pk['category'] = 0;
			unset($pk['filterprice']);
		}

		$products = array();
		$this->data = $pk;

		// Get all product from here
		foreach ( $this->item as $k => $item )
		{
			$products[] = RedshopHelperProduct::getProductById($item);
		}

		if (empty($products))
		{
			if (isset($pk['keyword']))
			{
				$check = $model->checkKeyword($pk['keyword']);

				if ($check == 0)
				{
					$model->insertKeyword($pk['keyword']);
				}
				else
				{
					$model->updateTimes($pk['keyword']);
				}
			}
		}

		$count = $model->getTotal();
		$session->set("count_product", $count);

		$this->json = json_encode($pk);
		$session->set('form_data', $this->json);

		$this->products = $products;

		parent::display($tpl);
	}
}
