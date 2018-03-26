<?php
/**
 * @package     RedSHOP
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.plugin.plugin');
JLoader::import('redshop.library');

/**
 * Plgredshop_Productstock_Notifyemail Class
 *
 * @since  1.5
 */
class PlgRedshop_UserUser_Point extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Method is update user point
	 *
	 * @param   array  $user_id  user id
	 *
	 * @return  void
	 */
	public function onAfterOrderSuccess($user_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$point = $this->getCurrentPoint($user_id);
		$this->updateShopperGroup($user_id);

		if ($point != "")
		{
			$fields = array($db->qn('point') . ' = ' . $db->q(((int) $point) + 1));
			$conditions = array($db->qn('user_id') . ' = ' . $db->q((int) $user_id));

			$query->update($db->qn('#__redshop_user_points'))
				->set($fields)
				->where($conditions);
		}
		else
		{
			$columns = array('user_id', 'point');
			$values = array($db->q((int) $user_id), $db->q(1));

			$query->insert($db->qn('#__redshop_user_points'))
				->columns($db->qn($columns))
				->values(implode(',', $values));
		}

		return $db->setQuery($query)->execute();
	}

	/**
	 * Method is get current point of user
	 *
	 * @param   array  $user_id  user id
	 *
	 * @return  void
	 */
	public function getCurrentPoint($user_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->qn('point'))
			->from($db->qn('#__redshop_user_points'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $user_id));

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * Method is get current point of user
	 *
	 * @param   array  $user_id  user id
	 *
	 * @return  void
	 */
	public function updateShopperGroup($user_id)
	{
		$point = $this->getCurrentPoint($user_id);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__redshop_user_point_config'));
		$result = $db->setQuery($query)->loadObjectList();
		$shopperGroup = 1;

		foreach ($result as $key => $value)
		{
			if ($point >= $value->point)
			{
				$shopperGroup = $value->shoppergroup_id;
			}
		}

		$fields     = array($db->qn('shopper_group_id') . ' = ' . $db->q((int) $shopperGroup));
		$conditions = array($db->qn('user_id') . ' = ' . $db->q((int) $user_id));

		$query = $db->getQuery(true)
			->clear()
			->update($db->qn('#__redshop_users_info'))
			->set($fields)
			->where($conditions);

		return $db->setQuery($query)->execute();
	}

	/**
	 * Method is get current point of user
	 *
	 * @param   array  $user_id  user id
	 *
	 * @return  void
	 */
	public function updateUserPoint($user_id, $point)
	{
		$userPoint = $this->getCurrentPoint($user_id);
		$newPoint = $userPoint - $point;
		$db = JFactory::getDbo();
		$fields     = array($db->qn('point') . ' = ' . $db->q((int) $newPoint));
		$conditions = array($db->qn('user_id') . ' = ' . $db->q((int) $user_id));
		$query = $db->getQuery(true)
			->clear()
			->update($db->qn('#__redshop_user_points'))
			->set($fields)
			->where($conditions);

		return $db->setQuery($query)->execute();
	}
}
