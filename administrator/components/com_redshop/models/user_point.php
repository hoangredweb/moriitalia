<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopModelUser_point extends RedshopModel
{
	public $_id = null;

	public function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('user_id', 0, '', 'array');

		$this->setId((int) $array[0]);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.5
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter');
		$id .= ':' . $this->getState('spgrp_filter');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = 'users_info_id', $direction = '')
	{
		$filter = $this->getUserStateFromRequest($this->context . '.filter', 'filter', '');
		$spgrp_filter = $this->getUserStateFromRequest($this->context . '.spgrp_filter', 'spgrp_filter', 0);

		$this->setState('filter', $filter);
		$this->setState('spgrp_filter', $spgrp_filter);

		parent::populateState($ordering, $direction);
	}

	public function setId($id)
	{
		$this->_id = $id;
	}

	public function _buildQuery()
	{
		$filter = $this->getState('filter');
		$spgrp_filter = $this->getState('spgrp_filter');

		$where = '';

		if ($filter)
		{
			$filter = str_replace(' ', '', $filter);
			$where .= " AND (u.username LIKE '%" . $filter . "%' ";
			$where .= " OR (REPLACE(CONCAT(uf.firstname, uf.lastname), ' ', '') like '%" . $filter . "%'))";
		}

		if ($spgrp_filter)
		{
			$where .= " AND sp.shopper_group_id = '" . $spgrp_filter . "' ";
		}

		$orderby = $this->_buildContentOrderBy();

		if ($this->_id != 0)
		{
			$query = ' SELECT * FROM  #__redshop_user_points AS up '
				. 'LEFT JOIN #__redshop_users AS u ON u.id=up.user_id '
				. 'LEFT JOIN #__redshop_users_info AS uf ON u.id=uf.user_id '
				. 'LEFT JOIN #__redshop_shopper_group AS sp ON uf.shopper_group_id=sp.shopper_group_id '
				. 'WHERE uf.address_type="ST" '
				. 'AND uf.user_id="' . $this->_id . '" '
				. $where
				. $orderby;
		}
		else
		{
			$query = ' SELECT * '
				. 'FROM #__redshop_user_points AS up '
				. 'LEFT JOIN #__users AS u ON u.id = up.user_id '
				. 'LEFT JOIN #__redshop_users_info AS uf ON u.id=uf.user_id '
				. 'LEFT JOIN #__redshop_shopper_group AS sp ON sp.shopper_group_id = uf.shopper_group_id '
				. 'WHERE uf.address_type="BT" '
				. $where
				. $orderby;
		}

		return $query;
	}

	/**
	 * Customer Total sales
	 *
	 * @param   integer  $uid  User Information id
	 *
	 * @deprecated  1.6     Use RedshopHelperUser::totalSales($uid) instead.
	 * @return      float   Total Sales of customer
	 */
	public function customertotalsales($uid)
	{
		return RedshopHelperUser::totalSales($uid);
	}
}
