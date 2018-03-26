<?php
/**
 * @package     RedITEM.Front
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * RedITEM Comment Model
 *
 * @package     RedITEM.Front
 * @subpackage  Models.Comment
 * @since       2.1
 *
 */
class ReditemModelComment extends RModelAdmin
{
	/**
	 * Method for save comment into database
	 *
	 * @param   array  $data  Array of comment data
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public function saveComment($data)
	{
		if (empty($data))
		{
			$app->enqueueMessage(JText::_('COM_REDITEM_COMMENT_ERROR_ADD_COMMENT_FAIL'), 'error');

			return false;
		}

		$app = JFactory::getApplication();
		$commentTable = RTable::getAdminInstance('Comment', array('ignore_request' => true), 'com_reditem');

		// Check if this is edit comment or create new comment
		if (!$data['commentId'])
		{
			$commentTable->id        = null;
			$commentTable->user_id   = $data['userId'];
			$commentTable->item_id   = $data['itemId'];
			$commentTable->parent_id = $data['parentId'];
			$commentTable->state     = 1;
		}
		else
		{
			$commentTable->id = $data['commentId'];

			// Check if comment already exist
			if (!$commentTable->load())
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_COMMENT_ERROR_COULD_NOT_FIND_THIS_COMMENT'), 'error');

				return false;
			}

			// Check if current user is owner of this comment
			if ($commentTable->user_id != $data['userId'])
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_COMMENT_ERROR_YOU_DO_NOT_HAVE_PERMISSION_EDIT_THIS_COMMENT'), 'error');

				return false;
			}
		}

		$commentTable->private = $data['private'];
		$commentTable->comment = $data['comment'];

		if (!$commentTable->store())
		{
			$app->enqueueMessage(JText::_('COM_REDITEM_COMMENT_ERROR_ADD_COMMENT_FAIL'), 'error');

			return false;
		}

		return $commentTable->id;
	}

	/**
	 * Method for report a comment
	 *
	 * @param   int     $commentId  ID of comment
	 * @param   reason  $reason     Reason why user report this item
	 *
	 * @return  boolean  True on success
	 */
	public function reportComment($commentId, $reason)
	{
		$db        = JFactory::getDbo();
		$user      = ReditemHelperSystem::getUser();
		$commentId = (int) $commentId;

		if (!$commentId || $user->guest)
		{
			return false;
		}

		JPluginHelper::importPlugin('reditem');
		$dispatcher = RFactory::getDispatcher();

		$dispatcher->trigger('onBeforeCommentReport', array($commentId, $user->id, $reason));

		$query = $db->getQuery()->clear()
			->delete($db->qn('#__reditem_comment_reports'))
			->where($db->qn('comment_id') . ' = ' . $db->quote($commentId))
			->where($db->qn('user_id') . ' = ' . $db->quote($user->id));
		$db->setQuery($query);
		$db->execute();

		$values = array(
			$db->quote($commentId),
			$db->quote($reason),
			$db->quote($user->id),
			$db->quote(ReditemHelperSystem::getDateWithTimezone()->toSql())
		);

		// Insert new report
		$query->clear()
			->insert($db->qn('#__reditem_comment_reports'))
			->columns($db->qn(array('comment_id', 'reason', 'user_id', 'created')))
			->values(implode(',', $values));
		$db->setQuery($query);

		if ($db->execute())
		{
			$reportId = $db->insertid();
			$dispatcher->trigger('onAfterCommentReported', array($commentId, $reportId, $user->id, $reason));

			return $reportId;
		}

		return false;
	}

	/**
	 * Method for remove report of an comment for this user
	 *
	 * @param   int  $commentId  ID of comment
	 *
	 * @return  boolean  True on success
	 */
	public function unReportComment($commentId)
	{
		$db        = JFactory::getDbo();
		$user      = ReditemHelperSystem::getUser();
		$commentId = (int) $commentId;

		if (!$commentId || $user->guest)
		{
			return false;
		}

		JPluginHelper::importPlugin('reditem');
		$dispatcher = RFactory::getDispatcher();

		$dispatcher->trigger('onBeforeCommentUnReport', array($commentId, $user->id));

		$query = $db->getQuery()->clear()
			->delete($db->qn('#__reditem_comment_reports'))
			->where($db->qn('comment_id') . ' = ' . $db->quote($commentId))
			->where($db->qn('user_id') . ' = ' . $db->quote($user->id));
		$db->setQuery($query);

		if ($db->execute())
		{
			$dispatcher->trigger('onAfterCommentUnReported', array($commentId, $user->id));

			return true;
		}

		return false;
	}

	/**
	 * Method for remove a comment
	 *
	 * @param   int  $commentId  ID of comment
	 *
	 * @return  array  Array of result with status.
	 */
	public function removeComment($commentId)
	{
		$return = array();

		if (!$commentId)
		{
			// If comment ID is missing
			$return['status'] = 0;
			$return['msg'] = JText::_('COM_REDITEM_COMMENT_AJAX_DELETE_COMMENT_ERROR_MISSING_COMMENT_ID');
			$return = new JRegistry($return);

			return $return->toString();
		}

		$commentTable = RTable::getAdminInstance('Comment', array('ignore_request' => true), 'com_reditem');

		// Set comment ID for table
		$commentTable->id = $commentId;

		if (!$commentTable->load())
		{
			// If comment is not found
			$return['status'] = 0;
			$return['msg'] = JText::_('COM_REDITEM_COMMENT_AJAX_DELETE_COMMENT_ERROR_COMMENT_NOT_FOUND');
			$return = new JRegistry($return);

			return $return->toString();
		}

		// Change trash to "1": Delete by user
		$commentTable->trash = 1;

		if (!$commentTable->store())
		{
			// If comments cann't store
			$return['status'] = 0;
			$return['msg'] = JText::_('COM_REDITEM_COMMENT_AJAX_DELETE_COMMENT_ERROR_COULD_NOT_SAVE_COMMENT');
			$return = new JRegistry($return);

			return $return->toString();
		}

		$return['status'] = 1;
		$return['msg'] = JText::_('COM_REDITEM_COMMENT_DELETE_BY_USER');
		$return = new JRegistry($return);

		return $return->toString();
	}
}
