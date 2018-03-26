<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Comment Controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class ReditemControllerComment extends JControllerLegacy
{
	/**
	 * Method for add a comment into redITEM items
	 *
	 * @return  boolean  True if success. False otherwise.
	 */
	public function add()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$input = $app->input;
		$user  = ReditemHelperSystem::getUser();
		$model = $this->getModel('Comment');

		// Get the return url
		$returnUrl = JRoute::_('index.php?option=com_reditem');
		$return    = $input->getBase64('return_url', null);

		if ($return)
		{
			$returnUrl = base64_decode($return);
		}

		$itemId = $input->getInt('item_id', 0);

		// Check if itemId is missing
		if (!$itemId)
		{
			$app->redirect($returnUrl, JText::_('COM_REDITEM_COMMENT_ERROR_MISSING_ITEM_ID'), 'error');
		}

		// Get the parent_id (Reply to)
		$parentId = $input->getInt('parent_id', 0);

		// Get the comment text
		$comment = $input->getString('comment', '');

		if (empty($comment))
		{
			$app->redirect($returnUrl, JText::_('COM_REDITEM_COMMENT_ERROR_ADD_COMMENT_FAIL'), 'error');
		}

		// Get the private value
		$private = $input->getInt('private', 0);

		// Get the id of comment, if edit
		$commentId = $input->getInt('id', 0);

		$data['commentId'] = $commentId;
		$data['userId']    = $user->id;
		$data['itemId']    = $itemId;
		$data['parentId']  = $parentId;
		$data['private']   = $private;
		$data['comment']   = $comment;

		if (!$model->saveComment($data))
		{
			$app->redirect($returnUrl);
		}

		$app->redirect($returnUrl, JText::_('COM_REDITEM_COMMENT_ADD_COMMENT_SUCCESS'));
	}

	/**
	 * Method for user delete his's/her's comment
	 *
	 * @return  void
	 */
	public function ajaxDeleteComment()
	{
		$commentId = JFactory::getApplication()->input->getInt('id', 0);
		$model     = $this->getModel('Comment');
		$result    = $model->removeComment($commentId);

		echo $result;

		JFactory::getApplication()->close();
	}

	/**
	 * Method for report a comment
	 *
	 * @return  void
	 */
	public function ajaxReport()
	{
		$app       = JFactory::getApplication();
		$user      = ReditemHelperSystem::getUser();
		$result    = array('status' => 0);
		$commentId = $app->input->getInt('id', 0);
		$reason    = $app->input->getHtml('reason', '');

		if (!$commentId)
		{
			echo json_encode($result);
			$app->close();
		}

		// Does not allow guest rating
		if ($user->guest)
		{
			echo json_encode($result);
			$app->close();
		}

		// If user choose "other", get input from user
		if ($reason === 'other')
		{
			$reason = $app->input->getHtml('reason_other', '');
		}

		$model = $this->getModel('Comment');

		if ($model->reportComment($commentId, $reason))
		{
			$result['status'] = 1;
			$result['reason'] = $reason;
		}

		echo json_encode($result);
		$app->close();
	}

	/**
	 * Method for remove an report of comment for this user
	 *
	 * @return  void
	 */
	public function ajaxRemoveReport()
	{
		$app    = JFactory::getApplication();
		$user   = ReditemHelperSystem::getUser();
		$result = array('status' => 0);
		$commentId = $app->input->getInt('id', 0);

		if (!$commentId)
		{
			echo json_encode($result);
			$app->close();
		}

		// Does not allow guest rating
		if ($user->guest)
		{
			echo json_encode($result);
			$app->close();
		}

		$model = $this->getModel('Comment');

		if ($model->unReportComment($commentId))
		{
			$result['status'] = 1;
		}

		echo json_encode($result);
		$app->close();
	}
}
