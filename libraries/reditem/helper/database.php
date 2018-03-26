<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * redITEM database helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers.Database
 * @since       2.5.0
 *
 */
abstract class ReditemHelperDatabase
{
	/**
	 * Function to convert a string or array into a safe array to use for db queries
	 *
	 * @param   mixed   $values        Array or string to use as values
	 * @param   string  $filter        Filter to apply to the values
	 * @param   array   $removeValues  Items to remove/filter from the source array
	 *
	 * @return  array
	 */
	public static function filter($values, $filter = 'integer', $removeValues = array(''))
	{
		// Avoid null values
		if (null === $values)
		{
			return array();
		}

		// Convert comma separated values to arrays
		if (!is_array($values))
		{
			$values = (array) explode(',', $values);
		}

		// If all is selected remove filter
		if (in_array('*', $values))
		{
			return array();
		}

		// Remove undesired source values
		$values       = array_diff($values, $removeValues);
		$filterer     = new ReditemDatabaseFilterArray($values, $filter);
		$filteredData = $filterer->filter();

		// Remove again undesired values from result
		return array_diff($filteredData, $removeValues);
	}

	/**
	 * Fast use proxy to filter integers
	 *
	 * @param   mixed  $data          Data to filter
	 * @param   array  $removeValues  Values that we want removed from the output
	 *
	 * @return  array
	 */
	public static function filterInteger($data, $removeValues = array(''))
	{
		return static::filter($data, 'integer', $removeValues);
	}

	/**
	 * Fast use proxy to filter strings
	 *
	 * @param   mixed  $data          Data to filter
	 * @param   array  $removeValues  Values that we want removed from the output
	 *
	 * @return  array
	 */
	public static function filterString($data, $removeValues = array(''))
	{
		return static::filter($data, 'string', $removeValues);
	}

	/**
	 * Fast use proxy to filter booleans
	 *
	 * @param   mixed  $data          Data to filter
	 * @param   array  $removeValues  Values that we want removed from the output
	 *
	 * @return  array
	 */
	public static function filterBool($data, $removeValues = array(''))
	{
		return static::filter($data, 'bool', $removeValues);
	}
}
