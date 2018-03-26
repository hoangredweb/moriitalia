<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
JLoader::import('redshop.library');
JLoader::load('RedshopHelperProduct');
require_once JPATH_SITE . "/administrator/components/com_redshop/helpers/redshop.cfg.php";
/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */

class JFormFieldProduct extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'product';

	public function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get("id", 0, "INT");
		$catid = $input->get("catid", 0, "INT");
		$modelAssociations = JModelLegacy::getInstance("Associations", "RedproductfinderModel");
		$producthelper = new producthelper;
		$products = $modelAssociations->getProducts($id);
		$product_id = $modelAssociations->getProductByAssociation($id);

		$layout = new JLayoutFile('product');

		$html = $layout->render(
			array(
				"products" => $products,
				"producthelper" => $producthelper,
				"product_id" => $product_id
			)
		);

		return $html;
	}
}
