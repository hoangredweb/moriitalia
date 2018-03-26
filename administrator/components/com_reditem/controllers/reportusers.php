<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The Reports controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Reports
 * @since       2.1.3
 */
class ReditemControllerReportUsers extends RControllerAdmin
{
	/**
	 * Method to block users
	 *
	 * @return  void
	 */
	public function block()
	{
		$app = RFactory::getApplication();

		$userIds = $app->input->get_Array('cid', array());
		$return = $this->input->getBase64('return', null);
		$cid = 0;

		$reportUsersModel = RModel::getAdminInstance('ReportUsers', array('ignore_request' => true), 'com_reditem');
		$reportUsersModel->setBlock($userIds, 1);

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=reportusers');
		}

		$this->redirect();
	}

	/**
	 * Method to block users
	 *
	 * @return  void
	 */
	public function unBlock()
	{
		$app = RFactory::getApplication();

		$userIds = $app->input->get_Array('cid', array());
		$return = $this->input->getBase64('return', null);
		$cid = 0;

		$reportUsersModel = RModel::getAdminInstance('ReportUsers', array('ignore_request' => true), 'com_reditem');
		$reportUsersModel->setBlock($userIds, 0);

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=reportusers');
		}

		$this->redirect();
	}
}
