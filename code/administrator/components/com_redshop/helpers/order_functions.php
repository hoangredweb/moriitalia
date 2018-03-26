<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder instead
 */

defined('_JEXEC') or die;

/**
 * Order helper for backend
 *
 * @since       __DEPLOY_VERSION__
 *
 * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder instead
 */
class order_functions extends order_functionsDefault
{
	// /**
	//  * Get billing address
	//  *
	//  * @param   integer  $user_id  User ID
	//  *
	//  * @return  array
	//  *
	//  * @deprecated  __DEPLOY_VERSION__  Use RedshopHelperOrder::getBillingAddress() instead
	//  */
	// public function getBillingAddress($user_id = 0)
	// {
	// 	if ($userId == 0)
	// 	{
	// 		$user = JFactory::getUser();
	// 		$userId = $user->id;
	// 	}

	// 	if (!$userId)
	// 	{
	// 		return false;
	// 	}

	// 	if (!array_key_exists($userId, $this->billingAddresses))
	// 	{
	// 		$db = JFactory::getDbo();

	// 		$query = $db->getQuery(true)
	// 			->select('ui.*')
	// 			->select('CONCAT(' . $db->qn('ui.firstname') . '," ",' . $db->qn('ui.lastname') . ') AS text')
	// 			->select('up.point')
	// 			->from($db->qn('#__redshop_users_info', 'ui'))
	// 			->leftjoin($db->qn('#__redshop_user_points', 'up') . ' ON ' . $db->qn('ui.user_id') . ' = ' . $db->qn('up.user_id'))
	// 			->where($db->qn('ui.address_type') . ' = ' . $db->quote('BT'))
	// 			->where($db->qn('ui.user_id') . ' = ' . (int) $userId);

	// 		$this->billingAddresses[$userId] = $db->setQuery($query)->loadObject();
	// 	}

	// 	return $this->billingAddresses[$userId];
	// }
}