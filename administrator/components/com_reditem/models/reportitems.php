<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM items report Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.ReportItems
 * @since       2.1.3
 *
 */
class ReditemModelReportItems extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_reportitems';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'reportitems_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'i.id',
				'title', 'i.title',
				'owner_name',
				'reportedCount',
				'firstDateReported',
				'lastDateReported'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Prepared query for select reported items count
		$reportedCountQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__reditem_item_reports', 'itemReports'))
			->leftJoin($db->qn('#__reditem_items', 'items') . ' ON ' . $db->qn('items.id') . ' = ' . $db->qn('itemReports.item_id'))
			->where($db->qn('items.id') . ' = ' . $db->qn('i.id'));

		$filterReporter = (int) $this->getState('filter.reporter', 0);

		if ($filterReporter)
		{
			$reportedCountQuery->where($db->qn('itemReports.user_id') . ' = ' . $filterReporter);
		}

		// Prepared query for select first date reported
		$firstDateReportedQuery = $db->getQuery(true)
			->select($db->qn('itemReports.created'))
			->from($db->qn('#__reditem_item_reports', 'itemReports'))
			->leftJoin($db->qn('#__reditem_items', 'items') . ' ON ' . $db->qn('items.id') . ' = ' . $db->qn('itemReports.item_id'))
			->where($db->qn('items.id') . ' = ' . $db->qn('i.id'))
			->order($db->qn('itemReports.created') . ' ASC LIMIT 1');

		// Prepared query for select first date reported
		$lastDateReportedQuery = $db->getQuery(true)
			->select($db->qn('itemReports.created'))
			->from($db->qn('#__reditem_item_reports', 'itemReports'))
			->leftJoin($db->qn('#__reditem_items', 'items') . ' ON ' . $db->qn('items.id') . ' = ' . $db->qn('itemReports.item_id'))
			->where($db->qn('items.id') . ' = ' . $db->qn('i.id'))
			->order($db->qn('itemReports.created') . ' DESC LIMIT 1');

		// Select list
		$select = array();
		$select[] = $this->getState('list.select', 'i.*');
		$select[] = '(' . $reportedCountQuery . ') AS ' . $db->qn('reportedCount');
		$select[] = '(' . $firstDateReportedQuery . ') AS ' . $db->qn('firstDateReported');
		$select[] = '(' . $lastDateReportedQuery . ') AS ' . $db->qn('lastDateReported');
		$select[] = $db->qn('owner.name', 'owner_name');

		$query->select(implode(',', $select))
			->from($db->qn('#__reditem_items', 'i'))
			->leftJoin($db->qn('#__users', 'owner') . ' ON ' . $db->qn('i.created_user_id') . ' = ' . $db->qn('owner.id'));

		// Build filter for query
		$this->buildFilter($query, $db);

		// Make sure only show reported items
		$query->having($db->qn('reportedCount') . ' > 0');

		// Get the ordering modifiers
		$orderCol  = $this->state->get('list.ordering', 'i.id');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
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
	 * @return	string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.reportsCount');
		$id	.= ':' . $this->getState('filter.owner');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Filter search
		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		// Filter reports count
		$filterReportsCount = $this->getUserStateFromRequest($this->context . '.filter_reportsCount', 'filter_reportsCount');
		$this->setState('filter.reportsCount', $filterReportsCount);

		// From date filter
		$dateFrom = $this->getUserStateFromRequest($this->context . '.filter_dateFrom', 'filter_dateFrom');
		$this->setState('filter.filter_dateFrom', $dateFrom);

		// To date filter
		$dateTo = $this->getUserStateFromRequest($this->context . '.filter_dateTo', 'filter_dateTo');
		$this->setState('filter.filter_dateTo', $dateTo);

		// Filter by owner
		$filterOwner = $this->getUserStateFromRequest($this->context . '.filter_owner', 'filter_owner');
		$this->setState('filter.owner', $filterOwner);

		// Filter by reporter
		$filterReporter = $this->getUserStateFromRequest($this->context . '.filter_reporter', 'filter_reporter');
		$this->setState('filter.reporter', $filterReporter);

		// List limit
		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		// List limitstart
		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('i.id', 'ASC');
	}

	/**
	 * Method for build condition for query base on filter
	 *
	 * @param   JDatabaseQuery  &$query  Query object
	 * @param   Object          $db      Database object
	 *
	 * @return  boolean                  True on success. False otherwise.
	 */
	public function buildFilter(&$query, $db = null)
	{
		if (empty($query))
		{
			return false;
		}

		if ($db == null)
		{
			$db = RFactory::getDbo();
		}

		// Filter by search
		$filterSearch = $this->getState('filter.search');

		if ($filterSearch)
		{
			$where = array();
			$where[] = $db->qn('i.title') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('owner.username') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('owner.email') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter by number of reports
		$filterReportsCount = (int) $this->getState('filter.reportsCount');

		if ($filterReportsCount)
		{
			$query->having($db->qn('reportedCount') . ' = ' . $filterReportsCount);
		}

		// Filter by owner ID
		$filterOwnerID = (int) $this->getState('filter.owner');

		if ($filterOwnerID)
		{
			$query->where($db->qn('owner.id') . ' = ' . $filterOwnerID);
		}

		// Filter by dateFrom
		$filterDateFrom = $this->getState('filter.dateFrom', '');

		if (!empty($filterDateFrom))
		{
			$tmpDate = ReditemHelperSystem::getDateWithTimezone($filterDateFrom . ' 00:00:00');

			$reportDateFromQuery = $db->getQuery(true)
				->select($db->qn('item_id'))
				->from($db->qn('#__reditem_item_reports'))
				->where($db->qn('created') . ' >= ' . $db->quote($tmpDate->toSql()));

			$query->where($db->qn('i.id') . ' IN (' . $reportDateFromQuery . ')');
		}

		// Filter by dateTo
		$filterDateTo = $this->getState('filter.dateTo', '');

		if (!empty($filterDateTo))
		{
			$tmpDate = ReditemHelperSystem::getDateWithTimezone($filterDateTo . ' 23:59:59');

			$reportDateToQuery = $db->getQuery(true)
				->select($db->qn('item_id'))
				->from($db->qn('#__reditem_item_reports'))
				->where($db->qn('created') . ' <= ' . $db->quote($tmpDate->toSql()));

			$query->where($db->qn('i.id') . ' IN (' . $reportDateToQuery . ')');
		}

		return true;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		$items = parent::getItems();

		if (!$items || empty($items))
		{
			return $items;
		}

		foreach ($items as &$item)
		{
			$reports = ReditemHelperReport::getReportsItem($item->id);

			if (!empty($reports))
			{
				$db = RFactory::getDbo();
				$item->reports = array();

				foreach ($reports as $report)
				{
					$report->point = 5;

					$query = $db->getQuery(true)
						->select($db->qn('point'))
						->from($db->qn('#__reditem_reporter_point'))
						->where($db->qn('report_id') . ' = ' . (int) $report->id)
						->where($db->qn('type') . ' = ' . $db->quote('item'));
					$db->setQuery($query);
					$result = $db->loadObject();

					if ($result)
					{
						$report->point = $result->point;
					}

					$item->reports[] = $report;
				}
			}
		}

		return $items;
	}

	/**
	 * Method for admin add rating on each of report
	 *
	 * @param   int    $userId    ID of user who made the report
	 * @param   int    $reportId  ID of report
	 * @param   float  $point     Rating point for this report
	 *
	 * @return  boolean          True on success. False other wise.
	 */
	public function addPoint($userId, $reportId, $point)
	{
		$userId   = (int) $userId;
		$reportId = (int) $reportId;
		$point    = (float) $point;

		if (!$userId || !$reportId || !$point)
		{
			return false;
		}

		$db = RFactory::getDbo();

		// Delete old one if exist
		$query = $db->getQuery(true)
			->delete($db->qn('#__reditem_reporter_point'))
			->where($db->qn('user_id') . ' = ' . $userId)
			->where($db->qn('report_id') . ' = ' . $reportId)
			->where($db->qn('type') . ' = ' . $db->quote('item'));
		$db->setQuery($query);
		$db->execute();

		$columns = array('user_id', 'type', 'report_id', 'point');
		$values  = array($userId, $db->quote('item'), $reportId, $point);

		// Insert new record
		$query->clear()
			->insert($db->qn('#__reditem_reporter_point'))
			->columns($db->qn($columns))
			->values(implode(',', $values));
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method for ignore an report of item
	 *
	 * @param   int  $itemId    ID of item
	 * @param   int  $reportId  ID of report
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function ignoreReport($itemId, $reportId)
	{
		$itemId   = (int) $itemId;
		$reportId = (int) $reportId;

		if (!$itemId || !$reportId)
		{
			return false;
		}

		$db = RFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__reditem_item_reports'))
			->set($db->qn('state') . ' = 0')
			->where($db->qn('item_id') . ' = ' . $itemId)
			->where($db->qn('id') . ' = ' . $reportId);
		$db->setQuery($query);

		if (!$db->execute())
		{
			return false;
		}

		ReditemHelperReport::calculateItemBlockDueToReport($itemId);

		return true;
	}

	/**
	 * Method for approve an report of item
	 *
	 * @param   int  $itemId    ID of item
	 * @param   int  $reportId  ID of report
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function approveReport($itemId, $reportId)
	{
		$itemId   = (int) $itemId;
		$reportId = (int) $reportId;

		if (!$itemId || !$reportId)
		{
			return false;
		}

		$db = RFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__reditem_item_reports'))
			->set($db->qn('state') . ' = 1')
			->where($db->qn('item_id') . ' = ' . $itemId)
			->where($db->qn('id') . ' = ' . $reportId);
		$db->setQuery($query);

		if (!$db->execute())
		{
			return false;
		}

		ReditemHelperReport::calculateItemBlockDueToReport($itemId);

		return true;
	}
}
