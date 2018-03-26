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
 * RedITEM user list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.reditemuser
 *
 * @since       2.1.3
 */
class JFormFieldRedItemUser extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RedItemUser';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$db      = JFactory::getDbo();
		$options = parent::getOptions();

		$query = $db->getQuery(true)
			->select(
				array(
					$db->qn('u.id'),
					$db->qn('u.name'),
					$db->qn('u.username'),
				)
			)
			->from($db->qn('#__users', 'u'))
			->where($db->qn('u.block') . ' = 0');
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if (!empty($users))
		{
			foreach ($users as $user)
			{
				$text = $user->name . ' - ' . $user->username;
				$value = $user->id;
				$options[] = JHtml::_('select.option', $value, $text);
			}
		}

		return $options;
	}
}
