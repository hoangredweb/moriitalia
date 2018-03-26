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

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */

class JFormFieldRPCategory extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'rpcategory';

	public function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get("id", 0, "INT");
		$catid = $input->get("catid", 0, "INT");
		$modelAssociations = JModelLegacy::getInstance("Associations", "RedproductfinderModel");

		$categories = $modelAssociations->getCategories();

		$layout = new JLayoutFile('rpcategory');
		$selected = array();

		// Get association ID
		if ($id != 0)
		{
			$catSelected = $modelAssociations->getAssociationCategory($id);

			if (count($catSelected) > 0)
			{
				foreach ($catSelected as $k => $cat)
				{
					$selected[] = $cat["category_id"];
				}
			}
		}

		$html = $layout->render(
			array(
				"selected" => $selected,
				"catid" => $catid,
				"categories" => $categories
			)
		);

		return $html;
	}
}
