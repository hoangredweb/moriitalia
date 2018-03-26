<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * RedSLIDER section select list
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Field.RLSectionList
 *
 * @since       2.0
 */
class JFormFieldRLSectionList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RLSectionList';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		JPluginHelper::importPlugin('redslider_sections');
		$dispatcher = RFactory::getDispatcher();

		// Get list of sections' name
		$items = $dispatcher->trigger('getSectionName', array());

		$options = array();

		if (count($items) > 0)
		{
			foreach ($items as $item)
			{
				$options[] = JHTML::_('select.option', $item->id, $item->name);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return JHTML::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
	}
}
