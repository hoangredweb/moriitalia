<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

class RedshopModelUser_point_config extends RedshopModel
{
	public $_id = null;

	public $_data = null;

	public $_table_prefix = null;

	public function __construct()
	{
		parent::__construct();

		$this->_table_prefix = '#__redshop_';

		$array = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int) $array[0]);

	}

	public function setId($id)
	{
		$this->_id = $id;
		$this->_data = null;
	}

	public function &getData()
	{
		if ($this->_loadData())
		{
		}
		else
		{
			$this->_initData();
		}

		return $this->_data;
	}

	public function _loadData()
	{
		if (empty($this->_data))
		{
			$query = ' SELECT *'
				. ' FROM ' . $this->_table_prefix . 'user_point_config';
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObjectList();

			return (boolean) $this->_data;
		}

		return true;
	}

	public function _initData()
	{
		if (empty($this->_data))
		{
			$detail = new stdClass;
			$detail->shoppergroup_id = 0;
			$detail->point = 0;

			$this->_data = $detail;

			return (boolean) $this->_data;
		}

		return true;
	}

	public function store($data)
	{
		$input = JFactory::getApplication()->input;
		$groups = $input->post->get_Array('point', array());

		foreach ($groups as $group => $point)
		{
			$data = array();
			$data['shoppergroup_id'] = $group;
			$data['point'] = $point;
			$check = $this->checkExist($group);

			if (!empty($check))
			{
				$this->updateShoppergroupPoint($data);
			}
			else
			{
				$this->insertShoppergroupPoint($data);
			}
		}

		return true;
	}

	public function checkExist($shoppergroup_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__redshop_user_point_config'))
			->where($db->qn('shoppergroup_id') . ' = ' . $db->q((int) $shoppergroup_id));

		return $db->setQuery($query)->loadResult();
	}

	public function insertShoppergroupPoint($data)
	{
		$db = JFactory::getDBO();
		$columns = array('shoppergroup_id', 'point');
		$values = array($db->q((int) $data['shoppergroup_id']), $db->q((int) $data['point']));

		$query = $db->getQuery(true)
			->insert($db->qn('#__redshop_user_point_config'))
			->columns($db->qn($columns))
			->values(implode(',', $values));

		return $db->setQuery($query)->execute();
	}

	public function updateShoppergroupPoint($data)
	{
		$db = JFactory::getDBO();

		$fields = array($db->qn('point') . ' = ' . $db->q(((int) $data['point'])));
		$conditions = array($db->qn('shoppergroup_id') . ' = ' . $db->q((int) $data['shoppergroup_id']));

		$query = $db->getQuery(true)
			->update($db->qn('#__redshop_user_point_config'))
			->set($fields)
			->where($conditions);

		return $db->setQuery($query)->execute();
	}
}
