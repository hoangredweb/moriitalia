<?php
/**
 * @package     RedITEM
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM subscriblers Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Subscriblers
 * @since       2.1.9
 *
 */
class RedItemModelSubscriblers extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_subscriblers';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'subscriblers_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Settings array
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'u.id',
				'name', 'u.name',
				'username', 'u.username',
				'email', 'u.email',
				'subscrible',
				'notify'
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

		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'u.*, ' . $db->qn('ms.state', 'subscrible') . ', ' . $db->qn('ms.type', 'notify')
			)
		);

		$query->from($db->qn('#__users', 'u'))
			->leftJoin($db->qn('#__reditem_mail_settings', 'ms') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('ms.user_id'));

		// Filter by search user's name, username, email
		$filterSearch = $this->getState('filter.search', '');

		if (!empty($filterSearch))
		{
			$where = array();
			$where[] = $db->qn('u.name') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('u.username') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');
			$where[] = $db->qn('u.email') . ' LIKE ' . $db->quote('%' . $db->escape($filterSearch, true) . '%');

			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter by subscrible status
		$filterSubscrible = $this->getState('filter.subscrible');

		if (is_numeric($filterSubscrible))
		{
			$query->where($db->qn('ms.state') . ' = ' . $db->quote($filterSubscrible));
		}

		// Filter by subscrible status
		$filterNotify = $this->getState('filter.notify');

		if (is_numeric($filterNotify))
		{
			$query->where($db->qn('ms.type') . ' = ' . $db->quote($filterNotify));
		}

		// Get the ordering modifiers
		$orderCol  = $this->state->get('list.ordering', 'u.id');
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
		$id	.= ':' . $this->getState('filter.subscrible');
		$id	.= ':' . $this->getState('filter.notify');

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

		// Subscrible filter
		$subscrible = $this->getUserStateFromRequest($this->context . '.filter_subscrible', 'filter_subscrible');
		$this->setState('filter.subscrible', $subscrible);

		// Subscrible filter
		$notify = $this->getUserStateFromRequest($this->context . '.filter_notify', 'filter_notify');
		$this->setState('filter.notify', $notify);

		// List limit
		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		// List limitstart
		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('u.id', 'ASC');
	}
}
