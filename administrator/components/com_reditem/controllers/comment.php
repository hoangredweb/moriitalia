<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The comment edit controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Comment
 * @since       2.1
 */
class ReditemControllerComment extends RControllerForm
{
	/**
	 * Task for add Category
	 *
	 * @return void
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.commentId', '');

		return parent::add();
	}

	/**
	 * For edit a comment
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$commentModel = RModel::getAdminInstance('Comment');

		$comment = $commentModel->getItem();

		$app->setUserState('com_reditem.global.commentId', $comment->id);

		return parent::edit($key, $urlVar);
	}
}
