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
 * RedITEM comments Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Comments
 * @since       2.1
 *
 */
class ReditemModelComments extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_comments';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'comments_limit';

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
				'id', 'cm.id',
				'item_id', 'cm.item_id',
				'user_id', 'cm.user_id',
				'state', 'cm.state',
				'comment', 'cm.comment',
				'created', 'cm.created',
				'i.title', 'item_title',
				'u.name', 'user_name',
				'private', 'cm.private'
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
		$db = JFactory::getDbo();
		$user = ReditemHelperSystem::getUser();
		$app = JFactory::getApplication();

		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'cm.*, i.title as item_title, u.name as user_name, i.created_user_id as owner'
			)
		);

		$query->from($db->qn('#__reditem_comments', 'cm'));

		$query->innerJoin($db->qn('#__reditem_items', 'i') . ' ON ' . $db->qn('cm.item_id') . ' = ' . $db->qn('i.id'));
		$query->innerJoin($db->qn('#__users', 'u') . ' ON ' . $db->qn('cm.user_id') . ' = ' . $db->qn('u.id'));

		// Filter by item Id
		$itemId = (int) $this->getState('filter.item_id');

		if ($itemId)
		{
			$query->where($db->qn('cm.item_id') . ' = ' . $itemId);
		}

		// Filter by parent_id (Reply to)
		$parentId = $this->getState('filter.parent_id');

		if (is_numeric($parentId))
		{
			$query->where($db->qn('cm.parent_id') . ' = ' . $parentId);
		}

		// Filter by published state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			if ($state === 2)
			{
				// Deleted by user
				$query->where($db->qn('cm.trash') . ' = 1');
				$query->where('(' . $db->qn('cm.state') . ' IN (0, 1))');
			}
			elseif ($state == 3)
			{
				// Deleted due to reports
				$query->where($db->qn('cm.trash') . ' = 2');
				$query->where('(' . $db->qn('cm.state') . ' IN (0, 1))');
			}
			else
			{
				$query->where($db->qn('cm.state') . ' = ' . (int) $state);
			}
		}
		elseif (empty($state))
		{
			$query->where('(' . $db->qn('cm.state') . ' IN (0, 1))');
		}

		// Check private permission of users
		if (!$app->isAdmin())
		{
			if ($user->guest)
			{
				$query->where($db->qn('cm.private') . ' = 0');
			}
			else
			{
				$where = array();

				$where[] = $db->qn('cm.private') . ' = 0';
				$where[] = '(' . $db->qn('cm.private') . ' = 1) AND (
					(' . $db->qn('cm.user_id') . ' = ' . $db->quote($user->id) . ') OR (' . $db->qn('cm.reply_user_id') . ' = ' . $user->id . '))';

				$ownerViewAllPrivate = $this->getState('filter.ownerViewAllPrivate');

				if ($ownerViewAllPrivate == 1)
				{
					$where[] = '(' . $db->qn('cm.private') . ' = 1) AND (' . $db->qn('i.created_user_id') . ' = ' . $db->quote($user->id) . ')';
				}

				$query->where('((' . implode(') OR (', $where) . '))');
			}
		}

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 'cm.id');
		$orderDirn	= $this->state->get('list.direction', 'asc');
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
		$id .= (int) $this->getState('filter.item_id');
		$id .= (int) $this->getState('filter.state');
		$id .= $this->getState('filter.parent_id');

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

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('cm.id', 'ASC');
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

		if (!empty($items))
		{
			foreach ($items as $key => $item)
			{
				// Get comment time elapsed
				$item->posted = $this->getTimeElapsed($item->created);

				// Get information of user whom is created this comment
				if (JUser::getTable()->load($item->user_id))
				{
					$item->user = new JUser($item->user_id);
				}
				else
				{
					unset($items[$key]);
				}

				// If this is reply comment, get information of user whom is replied to
				if (($item->private) && ($item->reply_user_id))
				{
					if (JUser::getTable()->load($item->reply_user_id))
					{
						$item->replyToUser = new JUser($item->reply_user_id);
					}
					else
					{
						unset($items[$key]);
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Method for create group of comments
	 *
	 * @param   array  $comments  Array of comments
	 *
	 * @return  array  Array of results
	 */
	public function groupComment($comments = array())
	{
		if (empty($comments) || !is_array($comments))
		{
			return array();
		}

		foreach ($comments as &$comment)
		{
			$comment->replyTo = self::getReply($comment->id);
		}

		return $comments;
	}

	/**
	 * Method for get all reply comment of this comment
	 *
	 * @param   integer  $commentId  Comment ID
	 *
	 * @return  array  List of reply to comments
	 */
	public function getReply($commentId = 0)
	{
		$commentId = (int) $commentId;

		if (!$commentId)
		{
			return array();
		}

		$comments = array();

		$commentsModel = RModel::getAdminInstance('Comments', array('ignore_request' => true), 'com_reditem');
		$commentsModel->setState('filter.parent_id', $commentId);
		$commentsModel->setState('filter.state', 1);

		$ownerViewAllPrivate = $this->getState('filter.ownerViewAllPrivate');
		$commentsModel->setState('filter.ownerViewAllPrivate', $ownerViewAllPrivate);

		$comments = $commentsModel->getItems();

		if (!$comments)
		{
			return array();
		}

		foreach ($comments as &$comment)
		{
			$comment->replyTo = $commentsModel->getReply($comment->id);
		}

		return $comments;
	}

	/**
	 * Method to convert timestamp to time elapsed string
	 *
	 * @param   string  $datetime  string date
	 *
	 * @return  string
	 */
	private function getTimeElapsed($datetime)
	{
		$now      = ReditemHelperSystem::getDateWithTimezone();
		$datetime = ReditemHelperSystem::getDateWithTimezone($datetime);

		$delta = (int) ($now->toUnix() - $datetime->toUnix());
		$format = JText::_('DATE_FORMAT_LC2');

		switch ( $delta )
		{
			case 0:
			case ( $delta < 60 ):
				$string = JText::_('COM_REDITEM_COMMENT_JUST_NOW');
				break;

			case ( $delta < (60 * 2) ):
				$string = JText::_('COM_REDITEM_COMMENT_ONE_MINUTE_AGO');
				break;

			case ( $delta < ( 60 * 60) ):
				$string = JText::sprintf('COM_REDITEM_COMMENT_MINUTES_AGO', floor($delta / 60));
				break;

			case ( $delta < ( 2 * 60 * 60 ) ):
				$string = JText::_('COM_REDITEM_COMMENT_ONE_HOUR_AGO');
				break;

			case ( $delta < ( 24 * 60 * 60 ) ):
				$string = JText::sprintf('COM_REDITEM_COMMENT_HOURS_AGO', floor($delta / 3600));
				break;

			default:
				$string = $datetime->format($format, true);
				break;
		}

		return $string;
	}
}
