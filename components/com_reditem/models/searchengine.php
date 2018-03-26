<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Search engine model
 *
 * @package     RedITEM.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class ReditemModelSearchEngine extends RModelList
{
	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'items_limit';

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
			$filterFields = array(
			);

			$config['filter_fields'] = $filterFields;
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
		$user  = ReditemHelperSystem::getUser();
		$query = $db->getQuery(true);

		// Check permission of user
		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			return false;
		}

		// Select data
		$query->select(
			$this->getState(
				'list.select',
				'se.*,' . $db->qn('t.title', 'type_name')
			)
		);
		$query->from($db->qn('#__reditem_search_engine', 'se'));
		$query->leftJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('se.type_id'));

		// Filter by user ID
		$userId = $this->getState('filter.userId', 0);

		if (!empty($userId))
		{
			$query->where($db->qn('user_id') . ' = ' . (int) $userId);
		}
		else
		{
			$query->where($db->qn('user_id') . ' = ' . (int) $user->id);
		}

		// Get the ordering modifiers
		$orderCol  = $this->state->get('list.ordering', 'se.id');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'se.id', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$filterUserId = $this->getUserStateFromRequest($this->context . '.filter_userId', 'filter_userId');
		$this->setState('filter.userId', $filterUserId);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method for store search engine for user
	 *
	 * @param   array  $data  Array of data
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public function saveFilter($data)
	{
		$user = ReditemHelperSystem::getUser();

		if (empty($data) || !$user->authorise('core.searchengine', 'com_reditem'))
		{
			return false;
		}

		$saveData = array();
		$db       = JFactory::getDbo();

		// Get {filter_category} tag if available
		if (isset($data['filter_category']) && !empty($data['filter_category']))
		{
			$saveData['filter_category'] = $data['filter_category'];
		}

		// Get {filter_title} tag if available
		if (isset($data['filter_title']) && !empty($data['filter_title']))
		{
			$saveData['filter_title'] = $data['filter_title'];
		}

		// Get {filter_customfield} tag if available
		if (isset($data['filter_customfield']) && !empty($data['filter_customfield']))
		{
			$saveData['filter_customfield'] = $data['filter_customfield'];

			// Remove empty filter
			foreach ($saveData['filter_customfield'] as $filter => $value)
			{
				if (empty($value))
				{
					unset($saveData['filter_customfield'][$filter]);
				}
			}
		}

		// Get {filter_ranges} tag if available
		if (isset($data['filter_ranges']) && !empty($data['filter_ranges']))
		{
			$saveData['filter_ranges'] = $data['filter_ranges'];
		}

		foreach ($saveData as $group => $filters)
		{
			if (empty($filters))
			{
				unset($saveData[$group]);
			}
		}

		// Make sure filter data not empty
		if (empty($saveData))
		{
			return false;
		}

		// Get url page
		if (isset($data['current_url']) && !empty($data['current_url']))
		{
			$saveData['url'] = $data['current_url'];
		}

		$searchData = new JRegistry($saveData);
		$searchData = $searchData->__toString();
		$typeId     = (int) $data['typeId'];

		$columns = array('user_id', 'type_id', 'send_mail', 'search_data');
		$values = array($user->id, $typeId, 0, $db->quote($searchData));

		$query = $db->getQuery(true)
			->insert($db->qn('#__reditem_search_engine'))
			->columns($db->qn($columns))
			->values(implode(',', $values));
		$db->setQuery($query);

		if (!$db->execute())
		{
			return false;
		}

		return $db->insertid();
	}

	/**
	 * Method for remove an search engine of user
	 *
	 * @param   int  $filterId  ID of stored id
	 * @param   int  $userId    ID of owner. Default is get from current user.
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function removeFilter($filterId, $userId = null)
	{
		$filterId = (int) $filterId;
		$userId = (int) $userId;

		if (empty($filterId))
		{
			return false;
		}

		$user = null;

		if (empty($userId))
		{
			$userId = ReditemHelperSystem::getUser();
		}
		else
		{
			$user = ReditemHelperSystem::getUser($userId);
		}

		// Check user permission
		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->qn('#__reditem_search_engine'))
			->where($db->qn('id') . ' = ' . (int) $filterId)
			->where($db->qn('user_id') . ' = ' . (int) $user->id);
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method for change status of Send Mail feature for search engine
	 *
	 * @param   int  $filterId  ID of stored id
	 * @param   int  $status    Status for search engine. Default is 0 (not send mail)
	 * @param   int  $userId    ID of owner. Default is get from current user.
	 *
	 * @return  boolean         True on success. False otherwise.
	 */
	public function changeSendMail($filterId, $status = 0, $userId = null)
	{
		$filterId = (int) $filterId;
		$status = (int) $status;
		$userId = (int) $userId;

		if (empty($filterId))
		{
			return false;
		}

		$user = null;

		if (empty($userId))
		{
			$userId = ReditemHelperSystem::getUser();
		}
		else
		{
			$user = ReditemHelperSystem::getUser($userId);
		}

		// Check user permission
		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->qn('#__reditem_search_engine'))
			->set($db->qn('send_mail') . ' = ' . $status)
			->where($db->qn('id') . ' = ' . (int) $filterId)
			->where($db->qn('user_id') . ' = ' . (int) $user->id);
		$db->setQuery($query);

		return $db->execute();
	}
}
