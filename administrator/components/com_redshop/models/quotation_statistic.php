<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class RedshopModelQuotation_statistic extends RedshopModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'q.quotation_cdate', 'quotation_cdate',
				'quotation_id', 'quotation_number',
				'quotation_status', 'quotation_total',
				'count'
			);
		}

		parent::__construct($config);
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
		$id .= ':' . $this->getState('filter_status');

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
	protected function populateState($ordering = 'q.quotation_cdate', $direction = 'desc')
	{
		$filter_status = $this->getUserStateFromRequest($this->context . 'filter_status', 'filter_status', 0);
		$filter_sale = $this->getUserStateFromRequest($this->context . 'filter_sale', 'filter_sale', 0);
		$filteroption = $this->getUserStateFromRequest($this->context . 'filter_sale', 'filteroption', 0);
		$filter = $this->getUserStateFromRequest($this->context . 'filter', 'filter', '');

		$this->setState('filter', $filter);
		$this->setState('filter_status', $filter_status);
		$this->setState('filter_sale', $filter_sale);
		$this->setState('filteroption', $filteroption);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 */
	public function getListQuery()
	{
		$formate = $this->getDateFormate();
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('q.*')
			->select('SUM(quotation_total) AS total')
			->select('COUNT(*) AS count')
			->select('FROM_UNIXTIME(q.quotation_cdate,"' . $formate . '") AS viewdate')
			->from($db->qn('#__redshop_quotation', 'q'))
			->leftJoin($db->qn('#__redshop_users_info', 'uf') . ' ON q.user_id = uf.user_id')
			->where('(uf.address_type = ' . $db->q('BT') . ' OR q.user_id = 0)')
			->group('q.user_id');

		$filter = $this->getState('filter');
		$filter_status = $this->getState('filter_status');
		$filter_sale = $this->getState('filter_sale');
		$filteroption = $this->getState('filteroption');

		if ($filter)
		{
			$query->where('(uf.firstname LIKE ' . $db->q('%' . $filter . '%') . ' OR uf.lastname LIKE ' . $db->q('%' . $filter . '%') . ')');
		}

		if ($filter_status != 0)
		{
			$query->where($db->qn('q.quotation_status') . ' = ' . $db->q($filter_status));
		}

		if ($filter_sale != 0)
		{
			$query->where($db->qn('q.sale_id') . ' = ' . $db->q((int) $filter_sale));
		}

		$filterOrder = $this->getState('list.ordering', 'q.quotation_cdate');
		$filterOrderDir = $this->getState('list.direction', 'desc');

		$query->order($db->qn($db->escape($filterOrder)) . ' ' . $db->escape($filterOrderDir));

		return $query;
	}

	public function getQuotations()
	{
		$today = $this->getStartDate();
		$mindate = $this->getMinDate();
		$db = JFactory::getDbo();
		$query = $this->getListQuery();
		$filteroption = $this->getState('filteroption');
		$quotations = $this->_getList($query);
		$result = array();

		if ($filteroption && $mindate != "" && $mindate != 0)
		{
			while ($mindate < strtotime($today))
			{
				$list = $this->getNextInterval($today);
				$query->where($db->qn('q.quotation_cdate') . ' > ' . $db->q(strtotime($list->preday)))
					->where($db->qn('q.quotation_cdate') . ' <= ' . $db->q(strtotime($today)));
				$rs = $db->setQuery($query)->loadObjectList();

				for ($i = 0, $in = count($rs); $i < $in; $i++)
				{
					if ($filteroption == 2)
					{
						$rs[$i]->viewdate = date("d M, Y", strtotime($list->preday) + 1);
					}

					$result[] = $rs[$i];
				}

				$today = $list->preday;
			}

			if (!empty($result))
			{
				$quotations = $result;
			}
		}

		return $quotations;
	}

	public function getStatisticAmount()
	{
		$filter_sale = $this->getState('filter_sale');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('SUM(quotation_total) AS total')
			->select('SUM(quotation_subtotal) AS subtotal')
			->select('COUNT(*) AS count')
			->from($db->qn('#__redshop_quotation'));

		if ($filter_sale != 0)
		{
			$query->where('sale_id = ' . $db->q((int) $filter_sale));
		}

		return $db->setQuery($query)->loadObject();
	}

	public function getStatisticSale()
	{
		$filter_sale = $this->getState('filter_sale');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('sale_id')
			->from($db->qn('#__redshop_quotation'))
			->group($db->qn('sale_id'));

		if ($filter_sale != 0)
		{
			$query->where('sale_id = ' . $db->q((int) $filter_sale));
		}

		return count($db->setQuery($query)->loadObjectList());
	}

	public function getNextInterval($today)
	{
		$filteroption = $this->getState('filteroption');
		$list = array();

		switch ($filteroption)
		{
			case 1:
				$query = 'SELECT SUBDATE("' . $today . '", INTERVAL 1 DAY) AS preday';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				break;
			case 2:
				$query = 'SELECT SUBDATE("' . $today . '", INTERVAL 1 WEEK) AS preday';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				break;
			case 3:
				$query = 'SELECT LAST_DAY(SUBDATE("' . $today . '", INTERVAL 1 MONTH)) AS preday';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				$list->preday = $list->preday . " 23:59:59";
				break;
			case 4:
				$query = 'SELECT SUBDATE("' . $today . '", INTERVAL 1 YEAR) AS preday';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				break;
		}

		return $list;
	}

	public function getStartDate()
	{
		$return = "";
		$filteroption = $this->getState('filteroption');

		switch ($filteroption)
		{
			case 1:
				$query = 'SELECT CURDATE() AS date';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				$return = $list->date . " 23:59:59";
				break;
			case 2:
				$query = 'SELECT ADDDATE(CURDATE(), INTERVAL 6-weekday(CURDATE()) DAY) AS date';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				$return = $list->date . " 23:59:59";
				break;
			case 3:
				$query = 'SELECT LAST_DAY(CURDATE()) as date';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				$return = $list->date . " 23:59:59";
				break;
			case 4:
				$query = 'SELECT LAST_DAY("' . date("Y-12-d") . '") as date';
				$this->_db->setQuery($query);
				$list = $this->_db->loadObject();
				$return = $list->date . " 23:59:59";
				break;
		}

		return $return;
	}

	public function getDateFormate()
	{
		$return = "";
		$filteroption = $this->getState('filteroption');

		switch ($filteroption)
		{
			case 1:
				$return = "%d %b, %Y";
				break;
			case 2:
				$return = "%d %b, %Y";
				break;
			case 3:
				$return = "%b, %Y";
				break;
			case 4:
				$return = "%Y";
				break;
			default:
				$return = "%Y";
				break;
		}

		return $return;
	}

	public function getMinDate()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('quotation_cdate'))
			->from($db->qn('#__redshop_quotation'))
			->order($db->qn('quotation_cdate') . ' ASC');

		return $db->setQuery($query)->loadResult();
	}
}
