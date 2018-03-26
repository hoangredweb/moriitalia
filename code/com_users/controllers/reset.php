<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('UsersController', JPATH_COMPONENT . '/controller.php');

/**
 * Reset controller class for Users.
 *
 * @since  1.6
 */
class UsersControllerReset extends UsersControllerResetDefault
{
	/**
	 * Method to request a password reset.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function request()
	{
		// Check the request token.
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel('Reset', 'UsersModel');
		$data  = $this->input->post->get('jform', array(), 'array');
		$language = JFactory::getLanguage();
		$lang = $language->getTag();

		// Submit the password reset request.
		$return = $model->processResetRequest($data);

		// Check for a hard error.
		if ($return instanceof Exception)
		{
			// Get the error message to display.
			if ($app->get('error_reporting'))
			{
				$message = $return->getMessage();
			}
			else
			{
				$message = JText::_('COM_USERS_RESET_REQUEST_ERROR');
			}

			// Get the route to the next page.
			$itemid = UsersHelperRoute::getResetRoute();
			$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
			$route  = 'index.php?option=com_users&view=reset' . $itemid . '&lang=' . $lang;

			// Go back to the request form.
			$this->setRedirect(JRoute::_($route, false), $message, 'error');

			return false;
		}
		elseif ($return === false)
		{
			// The request failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getResetRoute();
			$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
			$route  = 'index.php?option=com_users&view=reset' . $itemid . '&lang=' . $lang;

			// Go back to the request form.
			$message = JText::sprintf('COM_USERS_RESET_REQUEST_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($route, false), $message, 'notice');

			return false;
		}
		else
		{
			// The request succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getResetRoute();
			$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
			$route  = 'index.php?option=com_users&view=reset&layout=confirm' . $itemid . '&lang=' . $lang;

			// Proceed to step two.
			$this->setRedirect(JRoute::_($route, false));

			return true;
		}
	}

	/**
	 * Method to complete the password reset process.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function complete()
	{
		// Check for request forgeries
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel('Reset', 'UsersModel');
		$data  = $this->input->post->get('jform', array(), 'array');

		// Complete the password reset request.
		$return = $model->processResetComplete($data);

		// Check for a hard error.
		if ($return instanceof Exception)
		{
			// Get the error message to display.
			if ($app->get('error_reporting'))
			{
				$message = $return->getMessage();
			}
			else
			{
				$message = JText::_('COM_USERS_RESET_COMPLETE_ERROR');
			}

			// Get the route to the next page.
			$itemid = UsersHelperRoute::getResetRoute();
			$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
			$route  = 'index.php?option=com_users&view=reset&layout=complete' . $itemid;

			// Go back to the complete form.
			$this->setRedirect(JRoute::_($route, false), $message, 'error');

			return false;
		}
		elseif ($return === false)
		{
			// Complete failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getResetRoute();
			$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
			$route  = 'index.php?option=com_users&view=reset&layout=complete' . $itemid;

			// Go back to the complete form.
			$message = JText::sprintf('COM_USERS_RESET_COMPLETE_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($route, false), $message, 'notice');

			return false;
		}
		else
		{
			// Complete succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getLoginRoute();
			$itemid = $itemid !== null ? '&Itemid='  : '';
			$route  = 'index.php?option=com_redshop&view=login&Itemid=1009';

			// Proceed to the login form.
			$message = JText::_('COM_USERS_RESET_COMPLETE_SUCCESS');
			$this->setRedirect(JRoute::_($route, false), $message);

			return true;
		}
	}
}
