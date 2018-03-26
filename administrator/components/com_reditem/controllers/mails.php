<?php
/**
 * @package     RedITEM
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The mails controller
 *
 * @package     RedITEM
 * @subpackage  Controller.Mails
 * @since       2.1.5
 */
class RedItemControllerMails extends RControllerAdmin
{
	/**
	 * Method to set mail to become default on it's section
	 *
	 * @return  void
	 */
	public function setDefault()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$mailModel = RModel::getAdminInstance('Mail', array('ignore_request' => true));

		$cids   = $input->get_Array('cid', array());
		$return = $input->getBase64('return', null);
		$cid = 0;

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$mailModel->setDefault($cid, 1))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_MAILS_SET_DEFAULT_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_MAILS_SET_DEFAULT_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect('index.php?option=com_reditem&view=mails');
		}

		$this->redirect();
	}

	/**
	 * Method to set mail to become default on it's section
	 *
	 * @return  void
	 */
	public function setUnDefault()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$mailModel = RModel::getAdminInstance('Mail', array('ignore_request' => true));

		$cids   = $input->get_Array('cid', array());
		$return = $input->getBase64('return', null);
		$cid = 0;

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$mailModel->setDefault($cid, 0))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_MAILS_SET_DEFAULT_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_MAILS_SET_DEFAULT_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect('index.php?option=com_reditem&view=mails');
		}

		$this->redirect();
	}
}
