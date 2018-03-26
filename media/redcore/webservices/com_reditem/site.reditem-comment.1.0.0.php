<?php
/**
 * @package     Webservices
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.2
 */
class RApiHalHelperSiteReditemcomment
{
	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.4
	 */
	public function getItem($pk = null)
	{
		// Load redITEM Library
		JLoader::import('reditem.library');

		$commentModel = RModel::getFrontInstance('Comment', array('ignore_request' => true), 'com_reditem');
		$comment      = $commentModel->getItem($pk);

		if (!$comment)
		{
			return false;
		}

		$this->doStuff($comment);

		return $comment;
	}

	/**
	 * Method to get all comments of an item
	 *
	 * @param   int  $itemId  Item Id
	 *
	 * @return  mixed
	 */
	public function getComments($itemId)
	{
		$tmpComments = ReditemHelperComments::getComments($itemId);
		$comments    = array();

		if (empty($tmpComments))
		{
			return array();
		}

		foreach ($tmpComments as $comment)
		{
			if ($comment->trash || !$comment->state)
			{
				continue;
			}

			$this->doStuff($comment);

			// Remove unneeded fields
			unset($comment->item_id);
			unset($comment->trash);
			unset($comment->state);
			unset($comment->reply_user_id);
			unset($comment->user_id);
			unset($comment->user_name);
			unset($comment->user);

			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Method for delete comment by owner
	 *
	 * @param   int  $id  ID of comment
	 *
	 * @return  boolean   True on success. False otherwise.
	 */
	public function delete($id)
	{
		$id = (int) $id;

		if (!$id)
		{
			return false;
		}

		$comment = RTable::getAdminInstance('Comment', array('ignore_request' => true), 'com_reditem');
		$comment->id = $id;

		if (!$comment->load())
		{
			return false;
		}

		$user = ReditemHelperSystem::getUser();

		if ($comment->user_id != $user->id)
		{
			return false;
		}

		$comment->trash = 1;

		if (!$comment->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Method for get Groups name of user
	 *
	 * @param   object  $user  JUser object
	 *
	 * @return  boolean        True on success. False other wise.
	 */
	public function getUserGroup($user)
	{
		if (empty($user) || empty($user->groups))
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		foreach ($user->groups as $key => $value)
		{
			if (is_object($value))
			{
				continue;
			}

			$query->select($db->qn('id'))
				->select($db->qn('title'))
				->from($db->qn('#__usergroups'))
				->where($db->qn('id') . ' = ' . $value);
			$db->setQuery($query);
			$result = $db->loadObject();
			$user->groups[$key] = $result;
		}

		return true;
	}

	/**
	 * Method for do some stuff for comments
	 *
	 * @param   object  $comment  Comment data object
	 *
	 * @return  boolean           True on success. False otherwise
	 */
	public function doStuff($comment)
	{
		if (empty($comment) || !is_object($comment))
		{
			return false;
		}

		$comment->owner       = new stdClass;
		$comment->replyToUser = new stdClass;
		$comment->reports     = array();

		// Owner stuff
		$user = ReditemHelperSystem::getUser($comment->user_id);
		$this->getUserGroup($user);
		$comment->owner->id       = $user->id;
		$comment->owner->name     = $user->name;
		$comment->owner->username = $user->username;
		$comment->owner->email    = $user->email;
		$comment->owner->groups   = $user->groups;

		// Replier user stuff
		if (!empty($comment->reply_user_id))
		{
			$user = ReditemHelperSystem::getUser($comment->reply_user_id);
			$this->getUserGroup($user);
			$comment->replyToUser->id       = $user->id;
			$comment->replyToUser->name     = $user->name;
			$comment->replyToUser->username = $user->username;
			$comment->replyToUser->email    = $user->email;
			$comment->replyToUser->groups   = $user->groups;
		}

		// Get reports of this comment
		$reports = ReditemHelperReport::getReportCommentData($comment->id);

		if (!empty($reports))
		{
			$comment->reports = $reports;
		}

		return true;
	}
}
