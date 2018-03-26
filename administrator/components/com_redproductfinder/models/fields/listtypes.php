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

class JFormFieldListTypes extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'ListTypes';

	public function getInput()
	{
		// Get id tag and begin query
		$input = JFactory::getApplication()->input;

		$id = $input->get("id", "0", "INT");

		// Get list Type
		$modelType = JModelLegacy::getInstance("Types", "RedproductfinderModel");
		$modelTags = JModelLegacy::getInstance("Tags", "RedproductfinderModel");

		$listType = $modelType->getTypesList();

		$options = array();
		$checked = array();
		// Get data from tag id
		if ($id !== "0")
		{
			$TypeChecked = $modelTags->getTagTypes($id);

			foreach ($TypeChecked as $k => $r)
			{
				$checked[] = $r["type_id"];
			}
		}

		foreach ($listType as $l)
		{
			$options[] = array(
				'id' => $l["id"],
				'title' => $l["type_name"]
			);
		}

		if ( count($listType) != 0 )
		{
			$html = JHTML::_('select.genericlist', $options, 'jform[type_id][]', 'class="inputbox" multiple="multiple" size="8"', 'id', 'title', $checked);
		}
		else
		{
			$html = "No Type created";
		}

		return $html;
	}
}
