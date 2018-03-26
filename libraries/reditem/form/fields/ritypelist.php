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
 * RedITEM type select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RITypeList
 *
 * @since       2.0
 */
class JFormFieldRITypeList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RITypeList';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$db = JFactory::getDbo();
		$options = array();
		$currentOptions = parent::getOptions();

		// Prepare type list
		$query = $db->getQuery(true)
			->select($db->qn(array('t.id', 't.title')))
			->from($db->qn('#__reditem_types', 't'))
			->order($db->qn('t.title'));
		$db->setQuery($query);
		$types = $db->loadObjectList();

		$except = $this->getAttribute('except', false);

		// Create type list
		if ($types)
		{
			if ($except)
			{
				$app = JFactory::getApplication();
				$RITypeId = $app->getUserState('com_reditem.global.field.RITypeId');

				foreach ($types as $type)
				{
					if ($type->id != $RITypeId)
					{
						$options[] = JHTML::_('select.option', $type->id, $type->title);
					}
				}
			}
			else
			{
				foreach ($types as $type)
				{
					$options[] = JHTML::_('select.option', $type->id, $type->title);
				}
			}
		}

		if (!empty($currentOptions))
		{
			$options = array_merge($currentOptions, $options);
		}

		return $options;
	}
}
