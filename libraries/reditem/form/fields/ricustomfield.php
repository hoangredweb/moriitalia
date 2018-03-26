<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * RedITEM customfield select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RITCustomfield
 *
 * @since       2.0
 */
class JFormFieldRICustomfield extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 */
	protected $type = 'RICustomfield';

	/**
	 * Method to get the field options.
	 *
	 * @return   array  Options to populate the select field
	 */
	public function getOptions()
	{
		$db      = JFactory::getDbo();
		$options = parent::getOptions();
		$fields  = array (
			$db->q('checkbox'),
			$db->q('select'),
			$db->q('radio')
		);

		// Prepare type list
		$query = $db->getQuery(true)
			->select(
				array (
					$db->qn('f.id', 'id'),
					$db->qn('f.type', 'fieldType'),
					$db->qn('f.name', 'name'),
					$db->qn('t.title', 'itemType')
				)
			)
			->from($db->qn('#__reditem_fields', 'f'))
			->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('f.type_id'))
			->where($db->qn('f.type') . ' IN (' . implode(',', $fields) . ')');
		$db->setQuery($query);
		$customfields = $db->loadObjectList();

		// Create type list
		if (!empty($customfields))
		{
			foreach ($customfields as $customfield)
			{
				$title     = $customfield->name . ' [' . $customfield->fieldType . '] - ' . $customfield->itemType;
				$options[] = JHTML::_('select.option', $customfield->id, $title);
			}
		}

		return $options;
	}
}
