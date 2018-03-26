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
 * Cart Controller.
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 * @since       1.0
 */
class RedshopControllerCart extends RedshopControllerCartDefault
{
	public function userPoint()
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$cart = $session->get('cart');
		$input = $app->input;
		$itemId = $input->post->get('Itemid');
		$point = $input->post->get('user_point', 0);
		$amount = Redshop::getConfig()->get('USER_POINT_TO_AMOUNT');
		$link = JRoute::_('index.php?option=com_redshop&view=cart&Itemid=' . $Itemid, false);
		$pointAmount = $point * $amount;

		$userPoint = $this->getUserPoint();

		if ($userPoint < $point)
		{
			$msg = JText::_('COM_REDSHOP_USER_POINT_IS_NOT_ENOUGH');

			return $this->setRedirect($link, $msg, 'warning');
		}


		$cart['point_discount'] = $pointAmount;
		$cart['user_point'] = $point;
		$session->set('cart', $cart);
		$this->_carthelper->cartFinalCalculation();
		$this->_carthelper->carttodb();
		$msg = JText::sprintf('COM_REDSHOP_USER_POINT_SUCCESSFUL', $point);
		
		$this->setRedirect($link, $msg);
	}

	public function getUserPoint()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('point'))
			->from($db->qn('#__redshop_user_points'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $user->id));

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * Method to add coupon code in cart for discount
	 *
	 * @return void
	 */
	public function coupon()
	{
		$session   = JFactory::getSession();
		$post      = JRequest::get('post');
		$Itemid    = JRequest::getInt('Itemid');
		$redhelper = redhelper::getInstance();
		$Itemid    = $redhelper->getCartItemid();
		$model     = $this->getModel('cart');
		$language = JFactory::getLanguage();
		$lang = $language->getTag();

		// Call coupon method of model to apply coupon
		$valid = $model->coupon();
		$cart  = $session->get('cart');
		$this->modifyCalculation($cart);
		$this->_carthelper->cartFinalCalculation(false);

		// Store cart entry in db
		$this->_carthelper->carttodb();

		// If coupon code is valid than apply to cart else raise error
		if ($valid)
		{
			$link = JRoute::_('index.php?option=com_redshop&view=cart&Itemid=' . $Itemid .'&lang=' . $lang, false);

			if (Redshop::getConfig()->get('APPLY_VOUCHER_COUPON_ALREADY_DISCOUNT') != 1)
			{
				$this->setRedirect($link, JText::_('COM_REDSHOP_DISCOUNT_CODE_IS_VALID_NOT_APPLY_PRODUCTS_ON_SALE'), 'warning');
			}
			else
			{
				$this->setRedirect($link, JText::_('COM_REDSHOP_DISCOUNT_CODE_IS_VALID'));
			}
		}
		else
		{
			$link = JRoute::_('index.php?option=com_redshop&view=cart&Itemid=' . $Itemid .'&lang=' . $lang, false);
			$this->setRedirect($link, JText::_('COM_REDSHOP_COUPON_CODE_IS_NOT_VALID'), 'error');
		}
	}
}