<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


/**
 * Class LoginModelLogin
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelLogin extends RedshopModelLoginDefault
{
	public function setlogin($username, $password)
	{
		$app = JFactory::getApplication();

		$credentials             = array();
		$credentials['username'] = $username;
		$credentials['password'] = $password;

		// Perform the login action
		$error = $app->login($credentials);

		if (isset($error->message))
		{
			$msg = "<a href='" . JRoute::_('index.php?option=com_users&view=reset') . "'>" . JText::_('COM_REDSHOP_FORGOT_PWD_LINK') . "</a>";
			$app->enqueuemessage($msg);
		}

		if ($error === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}