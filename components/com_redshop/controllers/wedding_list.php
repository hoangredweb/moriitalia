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
 * wishlist Controller.
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 * @since       1.0
 */
class RedshopControllerWedding_list extends RedshopController
{
	/**
	 * createsave wishlist function
	 *
	 * @access public
	 * @return void
	 */
	public function createsave()
	{
		$app                = JFactory::getApplication();
		$input              = $app->input;
		$user               = JFactory::getUser();
		$model              = $this->getModel("wedding_list");
		$post['product_id'] = $input->getInt('product_id', 0);
		$post['user_id']    = $user->id;
		$post['cdate']      = time();
		$menu               = JFactory::getApplication()->getMenu();
		$menuItem           = $menu->getItems('link', 'index.php?option=com_redshop&view=wedding_list', true);
		$link               = JRoute::_("index.php?option=com_redshop&view=wedding_list&Itemid=" . $menuItem->id);

		if ($model->store($post))
		{
			$msg = JText::_('COM_REDSHOP_PRODUCT_SAVED_IN_WEDDINGLIST_SUCCESSFULLY');
		}
		else
		{
			$msg = JText::_('COM_REDSHOP_PRODUCT_NOT_SAVED_IN_WEDDINGLIST');
		}

		$app->redirect($link, $msg);
	}

	/**
	 * delete wishlist function
	 *
	 * @access public
	 * @return void
	 */
	public function delete()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$user      = JFactory::getUser();
		$model     = $this->getModel("wedding_list");
		$productId = $input->getInt('product_id');
		$menu      = JFactory::getApplication()->getMenu();
		$menuItem  = $menu->getItems('link', 'index.php?option=com_redshop&view=wedding_list', true);
		$link      = JRoute::_("index.php?option=com_redshop&view=wedding_list&Itemid=" . $menuItem->id);

		if ($model->check_user_wedding_authority($user->id, $productId))
		{
			if ($model->delete($user->id, $productId))
			{
				$msg = JText::_('COM_REDSHOP_WISHLIST_DELETED_SUCCESSFULLY');
			}
			else
			{
				$msg = JText::_('COM_REDSHOP_ERROR_IN_DELETING_WISHLIST');
			}
		}
		else
		{
			$msg  = JText::_('COM_REDSHOP_YOU_ARE_NOT_AUTHORIZE_TO_DELETE');
		}

		$app->redirect($link, $msg);
	}
}
