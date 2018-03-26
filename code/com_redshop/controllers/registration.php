<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * registration Controller.
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 * @since       1.0
 */
class RedshopControllerRegistration extends RedshopControllerRegistrationDefault
{
	/**
	 * newregistration function
	 *
	 * @access public
	 * @return void
	 */
	public function newregistration()
	{
		$app        = JFactory::getApplication();
		$post       = JRequest::get('post');
		$Itemid     = JRequest::getInt('Itemid', 0);
		$dispatcher = JDispatcher::getInstance();

		$prodhelperobj = productHelper::getInstance();
		$redshopMail   = redshopMail::getInstance();

		$model   = $this->getModel('registration');
		$success = $model->store($post);

		if ($success)
		{
			$message = JText::sprintf('COM_REDSHOP_ALERT_REGISTRATION_SUCCESSFULLY', $post['username']);
			JPluginHelper::importPlugin('redshop_alert');
			$dispatcher->trigger('storeAlert', array($message));

			if ($post['mywishlist'] == 1)
			{
				$wishreturn = JRoute::_('index.php?loginwishlist=1&option=com_redshop&view=wishlist&Itemid=' . $Itemid, false);
				$this->setRedirect($wishreturn);
			}
			else
			{
				$msg = WELCOME_MSG;

				if (SHOP_NAME != "")
				{
					$msg = str_replace("{shopname}", SHOP_NAME, $msg);
				}

				// Redirection settings
				$link = JRoute::_('index.php?option=com_redshop&view=account&logout=907&Itemid=133');

				// Redirection settings End
				$this->setRedirect($link, $msg);
			}
		}
		else
		{
			parent::display();
		}
	}
}
