<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Type helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Helper
 * @since       2.1
 *
 */
class ReditemHelperType
{
	/**
	 * Assoc array of table names by type id.
	 *
	 * @var array
	 */
	private static $tables = array();

	/**
	 * Assoc array of fields by type id.
	 *
	 * @var array
	 */
	private static $fields = array();

	/**
	 * Get table name from type id.
	 *
	 * @param   int      $typeId    Type id.
	 * @param   boolean  $fullName  Return full name (ex: '#__reditem_types_TABLE_NAME') or just TABLE_NAME
	 *
	 * @return  string  Table name if type exists, false otherwise.
	 */
	public static function getTableName($typeId, $fullName = true)
	{
		if (empty(self::$tables[$typeId]))
		{
			$db        = RFactory::getDbo();
			$query     = $db->getQuery(true);
			$query->select($db->qn('table_name'))
				->from($db->qn('#__reditem_types'))
				->where($db->qn('id') . ' = ' . (int) $typeId);

			if ($name = $db->setQuery($query, 0, 1)->loadResult())
			{
				self::$tables[$typeId] = '#__reditem_types_' . $name;
			}
			else
			{
				return false;
			}
		}

		if ($fullName)
		{
			return self::$tables[$typeId];
		}
		else
		{
			return str_replace('#__reditem_types_', '', self::$tables[$typeId]);
		}
	}

	/**
	 * Function for getting all custom fields per item type.
	 *
	 * @param   int     $type     Item type id.
	 * @param   string  $assocBy  Get assoc list by column name.
	 *
	 * @return  array  Array of custom fields objects.
	 */
	public static function getCustomFieldList($type, $assocBy = '')
	{
		if (empty(self::$fields[$type]) || !empty($assocBy))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('*')
				->from($db->qn('#__reditem_fields'))
				->where($db->qn('type_id') . ' = ' . (int) $type);
			$db->setQuery($query);

			if (!empty($assocBy))
			{
				return $db->loadObjectList($assocBy);
			}
			else
			{
				self::$fields[$type] = $db->loadObjectList();
			}
		}

		return self::$fields[$type];
	}

	/**
	 * Function for getting type title.
	 *
	 * @param   int  $type  Item type id.
	 *
	 * @return  object  Type object.
	 */
	public static function getType($type)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__reditem_types'))
			->where($db->qn('id') . ' = ' . (int) $type);
		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
