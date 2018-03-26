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
 * RedITEM reports Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Reports
 * @since       2.1.3
 *
 */
class ReditemModelReportUsers extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_reportusers';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'reportusers_limit';

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
				'id', 'user.id',
				'name', 'user.name',
				'block', 'user.block',
				'reportedItemsCount',
				'reportedCommentsCount'
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
		$db = RFactory::getDbo();

		// Prepared query for select reported items count
		$reportedItemsCountQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__reditem_item_reports', 'itemReports'))
			->leftJoin($db->qn('#__reditem_items', 'items') . ' ON ' . $db->qn('items.id') . ' = ' . $db->qn('itemReports.item_id'))
			->where($db->qn('items.created_user_id') . ' = ' . $db->qn('user.id'));

		// Prepared query for select reported comments count
		$reportedCommentsCountQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__reditem_comment_reports', 'commentReports'))
			->leftJoin($db->qn('#__reditem_comments', 'comments') . ' ON ' . $db->qn('comments.id') . ' = ' . $db->qn('commentReports.comment_id'))
			->where($db->qn('comments.user_id') . ' = ' . $db->qn('user.id'));

		// Prepared query for select last date reported items
		$reportedItemsLastDateQuery = $db->getQuery(true)
			->select($db->qn('lir.created'))
			->from($db->qn('#__reditem_item_reports', 'lir'))
			->leftJoin($db->qn('#__reditem_items', 'li') . ' ON ' . $db->qn('li.id') . ' = ' . $db->qn('lir.item_id'))
			->where($db->qn('li.created_user_id') . ' = ' . $db->qn('user.id'))
			->order($db->qn('lir.created') . ' DESC LIMIT 1');

		$reportedCommentsLastDateQuery = $db->getQuery(true)
			->select($db->qn('lcr.created'))
			->from($db->qn('#__reditem_comment_reports', 'lcr'))
			->leftJoin($db->qn('#__reditem_comments', 'lc') . ' ON ' . $db->qn('lc.id') . ' = ' . $db->qn('lcr.comment_id'))
			->where($db->qn('lc.user_id') . ' = ' . $db->qn('user.id'))
			->order($db->qn('lcr.created') . ' DESC LIMIT 1');

		$query = $db->getQuery(true);

		// Select list
		$select = array();
		$select[] = $this->getState('list.select', 'user.*');
		$select[] = '(' . $reportedItemsCountQuery . ') AS ' . $db->qn('reportedItemsCount');
		$select[] = '(' . $reportedCommentsCountQuery . ') AS ' . $db->qn('reportedCommentsCount');
		$select[] = '(' . $reportedItemsLastDateQuery . ') AS ' . $db->qn('reportedItemsLastDate');
		$select[] = '(' . $reportedCommentsLastDateQuery . ') AS ' . $db->qn('reportedCommentsLastDate');

		$query->select(implode(',', $select))
			->from($db->qn('#__users', 'user'));

		// Filter by search user's name, username, email
		$filterSearch = $this->getState('filter.search', '');

		if (!empty($filterSearch))
		{
			$where = array();
			$where[] = $db->qn('user.name') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('user.username') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('user.email') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');

			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter by search user status
		$filterUserStatus = $this->getState('filter.userStatus');

		if (is_numeric($filterUserStatus))
		{
			$query->where($db->qn('user.block') . ' = ' . $db->quote($filterUserStatus));
		}
		else
		{
			$query->where($db->qn('user.block') . ' IN (0,1)');
		}

		// Only show users has items or comments which is reported
		$query->having('((' . $db->qn('reportedItemsCount') . ' > 0) OR (' . $db->qn('reportedCommentsCount') . ' > 0))');

		// Get the ordering modifiers
		$orderCol  = $this->state->get('list.ordering', 'user.id');
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

		// Search filter
		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		// List limit
		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		// List limitstart
		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('user.id', 'ASC');
	}

	/**
	 * Method for set block status of user
	 *
	 * @param   array  $userIds      List of user Ids
	 * @param   int    $blockStatus  Status value (0 => actived, 1 => blocked)
	 *
	 * @return  boolean              True on success. False otherwise.
	 */
	public function setBlock($userIds, $blockStatus)
	{
		if (empty($userIds))
		{
			return false;
		}

		$app = RFactory::getApplication();
		$blockStatus = (int) $blockStatus;

		foreach ($userIds as $userId)
		{
			$user = ReditemHelperSystem::getUser($userId);

			if (!$user->id)
			{
				continue;
			}

			$user->block = $blockStatus;

			if (!$user->save())
			{
				if ($blockStatus == 1)
				{
					$app->enqueueMessage(JText::sprintf('COM_REDITEM_REPORT_USERS_ERROR_BLOCK_USER', $user->name), 'error');
				}
				else
				{
					$app->enqueueMessage(JText::sprintf('COM_REDITEM_REPORT_USERS_ERROR_UNBLOCK_USER', $user->name), 'error');
				}
			}
			else
			{
				if ($blockStatus == 1)
				{
					$app->enqueueMessage(JText::sprintf('COM_REDITEM_REPORT_USERS_BLOCK_USER_SUCCESS', $user->name));
				}
				else
				{
					$app->enqueueMessage(JText::sprintf('COM_REDITEM_REPORT_USERS_UNBLOCK_USER_SUCCESS', $user->name));
				}
			}
		}

		return true;
	}
}
