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

class JFormFieldFilter extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'filter';

	public function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get("id", 0, "INT");

		$modelAssociations = JModelLegacy::getInstance("Associations", "RedproductfinderModel");
		$modelFilter = JModelLegacy::getInstance("Filters", "RedproductfinderModel");

		$types = $modelAssociations->getTypeTagList();
		$getData = $modelFilter->getFilter($id);
		$tag_id = "";

		if (count($getData) > 0)
		{
			$tag_id = $getData["tag_id"];
		}

		$layout = new JLayoutFile('filter');

		$html = $layout->render(
			array(
				"types" => $types,
				"tag_id" => $tag_id
			)
		);

		return $html;
	}
}
