<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Comment table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       2.1
 */
class ReditemTableComment extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_comments';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'state';

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$input      = RFactory::getApplication()->input;
		$dispatcher = RFactory::getDispatcher();
		$isNew      = false;

		// Run plugin group
		JPluginHelper::importPlugin('reditem');

		if (!$this->id)
		{
			$isNew = true;
		}

		$isEdit = true;

		// Get item object before storing
		$commentBefore = RTable::getAdminInstance('Comment', array('ignore_request' => true), 'com_reditem');
		$commentBefore->load($this->id);

		// Run event
		$dispatcher->trigger('onBeforeCommentSave', array($this, $input));

		if ($isNew)
		{
			$this->created = ReditemHelperSystem::getDateWithTimezone()->toSql();
		}

		if ($this->parent_id)
		{
			// This is reply to comment.
			$commentModel  = RModel::getAdminInstance('Comment', array('ignore_request' => true), 'com_reditem');
			$parentComment = $commentModel->getItem($this->parent_id);
			$this->reply_user_id = $parentComment->user_id;

			// If parent comment is a private, set this comment to private mode.
			if ($parentComment->private)
			{
				$this->private = 1;
			}
		}
		else
		{
			// This is comment for item, not reply to any comment.
			if ($this->private)
			{
				$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
				$item = $itemModel->getItem($this->item_id);
			}
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		$dispatcher->trigger('onAfterCommentSave', array($this, $isNew, $commentBefore));

		if ($isNew && $this->reply_user_id)
		{
			$dispatcher->trigger('onAfterCommentSaveReply', array($this));
		}
		elseif (!$isNew && $this->trash == 1)
		{
			$dispatcher->trigger('onAfterCommentDeletedByOwner', array($this));
		}

		return true;
	}

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed    $pk        An optional primary key value to delete.  If not set the instance property value is used.
	 * @param   boolean  $children  An optional boolean variable for delete it's children category or not
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null, $children = true)
	{
		$db = RFactory::getDBO();
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');

		// Run event 'onBeforeCommentDelete'
		$dispatcher->trigger('onBeforeCommentDelete', array($this));

		// Remove reports of comment
		$reports = ReditemHelperReport::getReportsComment($this->id);

		if ($reports)
		{
			$reportTable = JTable::getInstance('ReportComment', 'ReditemTable', array('ignore_request' => true));

			foreach ($reports as $report)
			{
				$reportTable->load($report->id);
				$reportTable->delete();
			}
		}

		if (!parent::delete($pk, $children))
		{
			return false;
		}

		// Run event 'onAfterCommentDelete'
		$dispatcher->trigger('onAfterCommentDelete', array($this));

		return true;
	}
}
