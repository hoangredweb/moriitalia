<?php
/**
 * @package     RedITEM.Library
 * @subpackage  Entity
 *
 * @copyright   Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Item Entity
 *
 * @package     RedITEM.Library
 * @subpackage  Entity
 * @since       1.0
 */
final class ReditemEntityItem
{
	/**
	 * The reditem item id
	 *
	 * @var  integer
	 */
	private $id;

	/**
	 * The reditem item type_id
	 *
	 * @var  integer
	 */
	private $type_id;

	/**
	 * The reditem item.
	 *
	 * @var  object
	 */
	private $item;

	/**
	 * An array of instances.
	 *
	 * @var  ReditemEntityItem[]
	 */
	private static $instances = array();

	/**
	 * Constructor.
	 *
	 * @param   integer  $id  Item Id.
	 */
	private function __construct($id)
	{
		$this->item = $this->load($id);
	}

	/**
	 * Get an instance or create it from a redshopb user id.
	 *
	 * @param   integer  $id  Item Id.
	 *
	 * @return  ReditemEntityItem
	 */
	public static function getInstance($id)
	{
		if (!isset(self::$instances[$id]))
		{
			self::$instances[$id] = new static($id);
		}

		return self::$instances[$id];
	}

	/**
	 * Reload the entity.
	 *
	 * @param   integer  $id  Item Id.
	 *
	 * @return  mixed
	 */
	private function load($id)
	{
		$table = RTable::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');

		if ($table->load($id))
		{
			$this->id = $table->id;
			$this->type_id = $table->type_id;

			return $table;
		}

		return false;
	}

	/**
	 * Method to get Item Id
	 *
	 * @return  int
	 */
	public function getItemId()
	{
		return $this->id;
	}

	/**
	 * Method to get Item Type Id
	 *
	 * @return  int
	 */
	public function getTypeId()
	{
		return $this->type_id;
	}
}
