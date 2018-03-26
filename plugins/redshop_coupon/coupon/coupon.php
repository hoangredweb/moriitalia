<?php
/**
 * @package     RedShop
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.plugin.plugin');

/**
 * Plugins redSHOP Alert
 *
 * @since  1.0
 */
class PlgRedshop_CouponCoupon extends JPlugin
{
	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * store alert function
	 *
	 * @param   string   $data   alert message
	 * @param   int      $key    key
	 * @param   string   $email  user email
	 *
	 * @return boolean
	 */
	public function replaceCoupon($data, $key, $emails)
	{
		$mail        = RedshopHelperMail::getMailTemplate(0, 'introduce_friend');
		$couponCode  = $this->generateRandomString(10);
		$status      = 0;
		$exist       = "";
		$email       = $emails[0];
		$friendEmail = $emails[1];

		if ($key == 0)
		{
			$status = 0;
			$userId = $this->getUserInfoByEmail($email);
		}
		elseif ($key == 1)
		{
			$status = 1;
			$userId = 0;
		}

		$exist = $this->checkExist($friendEmail);

		if ($exist == "")
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_coupons'))
				->columns($db->qn(array('coupon_code', 'percent_or_total', 'coupon_value', 'start_date', 'end_date', 'coupon_type', 'userid', 'coupon_left', 'published')))
				->values($db->q($couponCode) . ',' . $db->q(1) . ',' . $db->q('15.00') . ',' . $db->q(time()) . ',' . $db->q(time() + 31536000) . ',' . $db->q(0) . ',' . $db->q((int) $userId) . ',' . $db->q(1) . ',' . $db->q((int) $status));

			$db->setQuery($query)->execute();

			if ($key == 1)
			{
				$userId                 = $this->getUserInfoByEmail($email);
				$couponId               = $this->getCouponId($userId);
				$result                 = array();
				$result['coupon_id']    = $couponId;
				$result['user_id']      = !empty($userId) ? $userId : 0;
				$result['friend_email'] = $friendEmail;
				$this->insertIntroduceFriend($result);
				$userInfo = $this->getUserInfoById($userId);
				$fullname = $userInfo->firstname . ' ' . $userInfo->lastname;
				$data = $mail[0]->mail_body;
			}

			if (strpos($data, '{name}') !== false)
			{
				$data = str_replace("{name}", $fullname, $data);
			}

			if (strpos($data, '{coupon_code}') !== false)
			{
				$data = str_replace("{coupon_code}", $couponCode, $data);
			}
		}
		else
		{
			if (strpos($data, '{name}') !== false)
			{
				$data = str_replace("{name}", "", $data);
			}

			if (strpos($data, '{coupon_code}') !== false)
			{
				$data = str_replace("{coupon_code}", '', $data);
			}

			return JFactory::getApplication()->redirect(JRoute::_($this->params->get('failed_url')));
		}

		return $data;
	}

	public function insertIntroduceFriend($data)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->insert($db->qn('#__redshop_introduce_friend'))
			->columns($db->qn(array('coupon_id', 'user_id', 'friend_email')))
			->values($db->q($data['coupon_id']) . ',' . $db->q($data['user_id']) . ',' . $db->q($data['friend_email']));

		$db->setQuery($query)->execute();

		return $data;
	}

	public function getUserInfoByEmail($email)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__users'))
			->where($db->qn('email') . ' = ' . $db->q($email));

		return $db->setQuery($query)->loadResult();
	}

	public function getUserInfoById($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__redshop_users_info'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $id));

		return $db->setQuery($query)->loadObject();
	}

	public function checkExist($email)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->qn('coupon_id'))
			->from($db->qn('#__redshop_introduce_friend'))
			->where($db->qn('friend_email') . ' = ' . $db->q($email));

		return $db->setQuery($query)->loadResult();
	}

	public function updateCoupon($email)
	{
		$id = $this->checkExist($email);

		if (!empty($id))
		{
			$db = JFactory::getDBO();
			$mail     = RedshopHelperMail::getMailTemplate(0, 'notify_coupon');

			$query = $db->getQuery(true)
				->select($db->qn('user_id'))
				->from($db->qn('#__redshop_introduce_friend'))
				->where($db->qn('coupon_id') . ' = ' . $db->q((int) $id));
			$userId = $db->setQuery($query)->loadResult();

			$query = $db->getQuery(true)
				->clear()
				->select($db->qn('email'))
				->from($db->qn('#__users'))
				->where($db->qn('id') . ' = ' . $db->q((int) $userId));
			$userEmail = $db->setQuery($query)->loadResult();

			$query = $db->getQuery(true)
				->clear()
				->update($db->qn('#__redshop_coupons'))
				->set(array($db->qn('published') . ' = ' . $db->q(1)))
				->where(array($db->qn('coupon_id') . ' = ' . $db->q((int) $id)));

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true)
				->clear()
				->select($db->qn('coupon_code'))
				->from($db->qn('#__redshop_coupons'))
				->where($db->qn('coupon_id') . ' = ' . $db->q((int) $id));
			$code = $db->setQuery($query)->loadResult();

			if (strpos($mail[0]->mail_body, '{coupon_code}') !== false)
			{
				$mail[0]->mail_body = str_replace("{coupon_code}", $code, $mail[0]->mail_body);
			}

			if (!RedshopHelperMail::sendEmail(null, null, $userEmail, $mail[0]->mail_subject, $mail[0]->mail_body, 1))
			{
				return JError::raiseWarning(21, JText::_('COM_REDSHOP_ERROR_SENDING_CONFIRMATION_MAIL'));
			}
		}
	}

	public function getCouponId($userId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->qn('coupon_id'))
			->from($db->qn('#__redshop_coupons'))
			->where($db->qn('userid') . ' = ' . $db->q((int) $userId))
			->order($db->qn('coupon_id') . ' DESC');

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * Generate random string
	 *
	 * @param   int  $length  lenght
	 *
	 * @access public
	 * @return string
	 */
	public static function generateRandomString($length = 8)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $length; $i++)
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}
}
