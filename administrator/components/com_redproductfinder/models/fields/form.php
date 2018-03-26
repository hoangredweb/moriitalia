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

class JFormFieldForm extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'form';

	public function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get("id", 0, "INT");

		$modelAssociations = JModelLegacy::getInstance("Associations", "RedproductfinderModel");

		$types = $modelAssociations->getTypeTagList();

		$layout = new JLayoutFile('form');
		$checked = array();

		// Get association ID
		if ($id != 0)
		{
			$tagChecked = $modelAssociations->getAssociationTags($id);

			if (count($tagChecked) > 0)
			{
				foreach ($tagChecked as $k => $value)
				{
					$checked[] = $value["tag_type"];
				}
			}
		}

		$html = $layout->render(
			array(
				"types" => $types,
				"checked" => $checked
			)
		);

		return $html;
	}
}
