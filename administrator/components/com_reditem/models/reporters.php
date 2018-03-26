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
 * RedITEM reporters Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Reporters
 * @since       2.1.3
 *
 */
class ReditemModelReporters extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_reporters';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'reporters_limit';

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
				'reportedItems',
				'reportedComments',
				'point'
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
		$reportedItemsQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__reditem_item_reports', 'itemReports'))
			->where($db->qn('itemReports.user_id') . ' = ' . $db->qn('user.id'));

		// Prepared query for select reported comments count
		$reportedCommentsQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__reditem_comment_reports', 'commentReports'))
			->where($db->qn('commentReports.user_id') . ' = ' . $db->qn('user.id'));

		// Prepared query for select point of reporter
		$reportersPointQuery = $db->getQuery(true)
			->select('SUM(' . $db->qn('rp.point') . ') / COUNT(' . $db->qn('rp.point') . ')')
			->from($db->qn('#__reditem_reporter_point', 'rp'))
			->where($db->qn('rp.user_id') . ' = ' . $db->qn('user.id'));

		$query = $db->getQuery(true);

		// Select list
		$select = $this->getState(
			'list.select',
			'user.*,'
			. '(' . $reportedItemsQuery . ') AS ' . $db->qn('reportedItems') . ','
			. '(' . $reportedCommentsQuery . ') AS ' . $db->qn('reportedComments') . ','
			. '(' . $reportersPointQuery . ') AS ' . $db->qn('point')
		);

		$query->select($select)
			->from($db->qn('#__users', 'user'));

		// Only show reporters
		$query->having('((' . $db->qn('reportedItems') . ' > 0) OR (' . $db->qn('reportedComments') . ' > 0))');

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

		// Filter by search user point
		$filterPoint = (float) $this->getState('filter.point');

		if ($filterPoint)
		{
			$query->having($db->qn('point') . ' = ' . $filterPoint);
		}

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

		// Point filter
		$filterPoint = $this->getUserStateFromRequest($this->context . '.filter_point', 'filter_point');
		$this->setState('filter.point', $filterPoint);

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
