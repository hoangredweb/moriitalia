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
 * RedITEM author list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RIAuthor
 *
 * @since       2.1.3
 */
class JFormFieldRIAuthor extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RIAuthor';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected static $options = array();

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	protected function getOptions()
	{
		// Accepted modifiers
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();

			$options = array();

			$db = JFactory::getDbo();

			// Construct the query
			$query = $db->getQuery(true)
				->select('u.id AS value, u.name AS text')
				->from($db->qn('#__users', 'u'))
				->join('INNER', $db->qn('#__reditem_items', 'c') . ' ON c.created_user_id = u.id')
				->group('u.id, u.name')
				->order('u.name')
				->where($db->qn('u.block') . ' = 0');

			// Setup the query
			$db->setQuery($query);

			// Return the result
			if ($options = $db->loadObjectList())
			{
				static::$options[$hash] = array_merge(static::$options[$hash], $options);
			}
		}

		return static::$options[$hash];
	}
}
