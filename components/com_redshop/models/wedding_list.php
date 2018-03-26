<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;



/**
 * Class wishlistModelwishlist
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelWedding_list extends RedshopModel
{
	public $_id = null;

	public $_name = null;

	// Product data
	public $_userid = null;

	public $_table_prefix = null;

	public $_comment = null;

	public $_cdate = null;

	public function __construct()
	{
		parent::__construct();
		$this->_table_prefix = '#__redshop_';
	}

	public function getWeddingListProduct()
	{
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();

		if ($user->id)
		{
			$query = $db->getQuery(true)
				->select('w.*')
				->select('p.*')
				->from($db->qn('#__redshop_product', 'p'))
				->leftjoin($db->qn('#__redshop_wedding_list', 'w') . ' ON ' . $db->qn('p.product_id') . ' = ' . $db->qn('w.product_id'))
				->where($db->qn('w.user_id') . ' = ' . $db->q((int) $user->id));

			return $db->setQuery($query)->loadObjectList();
		}
	}

	public function store($data)
	{
		$check = $this->check_user_wedding_authority($data['user_id'], $data['product_id']);

		if ($check == 0)
		{
			$db         = JFactory::getDbo();
			$query = $db->getQuery(true);
			$columns = array('user_id', 'product_id', 'cdate');
			$values = array($db->q((int) $data['user_id']), $db->q((int) $data['product_id']), $db->q((int) $data['cdate']));
			$query
				->insert($db->quoteName('#__redshop_wedding_list'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			return $db->setQuery($query)->execute();
		}
	}

	public function check_user_wedding_authority($userId, $productId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('wedding_list_id'))
			->from($db->qn('#__redshop_wedding_list'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $userId))
			->where($db->qn('product_id') . ' = ' . $db->q((int) $productId));

		$rs = $db->setQuery($query)->loadResult();

		if ($rs)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function delete($userId, $productId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->qn('#__redshop_wedding_list'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $userId))
			->where($db->qn('product_id') . ' = ' . $db->q((int) $productId));

		if ($db->setQuery($query)->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
