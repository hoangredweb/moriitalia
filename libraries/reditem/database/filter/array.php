<?php
/**
 * @package     RedITEM.Library
 * @subpackage  Database.Filter
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;

/**
 * Array database filterer
 *
 * @since  2.5.0
 */
class ReditemDatabaseFilterArray extends ReditemDatabaseFilterBase implements ReditemDatabaseFilterInterface
{
	/**
	 * Default filter
	 *
	 * @var  string
	 */
	protected $type = 'integer';

	/**
	 * Filtered data
	 *
	 * @var  array
	 */
	protected $filteredData = array();

	/**
	 * Sanitise an array of integers
	 *
	 * @return  array
	 */
	public function filterInteger()
	{
		if (empty($this->data) || !is_array($this->data))
		{
			return $this->filteredData;
		}

		$this->filteredData = ArrayHelper::toInteger($this->data);

		return $this->filteredData;
	}

	/**
	 * Sanitise an string of strings
	 *
	 * @return  array
	 */
	public function filterString()
	{
		if (empty($this->data) || !is_array($this->data))
		{
			return $this->filteredData;
		}

		$db                 = JFactory::getDbo();
		$this->filteredData = array_map(array($db, 'quote'), $this->data);

		return $this->filteredData;
	}

	/**
	 * Sanitise an array of booleans
	 *
	 * @return  array
	 */
	public function filterBool()
	{
		if (!is_array($this->data) || 0 === count($this->data))
		{
			return $this->filteredData;
		}

		$this->filteredData = array_map(
			function ($value)
			{
				return (int) (bool) $value;
			},
			$this->data
		);

		return $this->filteredData;
	}
}
