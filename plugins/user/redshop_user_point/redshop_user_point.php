<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Joomla User plugin
 *
 * @since  1.5
 */
class PlgUserRedshop_user_point extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogin($user, $options = array())
	{
		$instance = $this->_getUser($user, $options);
		$userPoint = $this->getUserPoint($instance->id);
		$shopperGroupId = $this->getShoppergroupPoint($userPoint);

		if (!empty($shopperGroupId))
		{
			$this->updateShoppergroup($shopperGroupId, $instance->id);
		}

		return true;
	}

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet he will be created
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (remember, autoregister, group).
	 *
	 * @return  object  A JUser object
	 *
	 * @since   1.5
	 */
	protected function _getUser($user, $options = array())
	{
		$instance = JUser::getInstance();
		$id = (int) JUserHelper::getUserId($user['username']);


		if ($id)
		{
			$instance->load($id);

			return $instance;
		}

		// TODO : move this out of the plugin
		$config = JComponentHelper::getParams('com_users');

		// Hard coded default to match the default value from com_users.
		$defaultUserGroup = $config->get('new_usertype', 2);

		$instance->set('id', 0);
		$instance->set('name', $user['fullname']);
		$instance->set('username', $user['username']);
		$instance->set('password_clear', $user['password_clear']);

		// Result should contain an email (check).
		$instance->set('email', $user['email']);
		$instance->set('groups', array($defaultUserGroup));

		// If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);

		if ($autoregister)
		{
			if (!$instance->save())
			{
				JLog::add('Error in autoregistration for user ' . $user['username'] . '.', JLog::WARNING, 'error');
			}
		}
		else
		{
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}

		return $instance;
	}

	/**
	 * This method get user point
	 *
	 * @param   int  $user_id  user_id
	 *
	 * @return  int
	 */
	public function getUserPoint($user_id)
	{
		$db = $this->db;
		$query = $db->getQuery(true)
			->select($db->qn('point'))
			->from($db->qn('#__redshop_user_points'))
			->where($db->qn('user_id') . ' = ' . $db->q((int) $user_id));

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * This method get user point
	 *
	 * @param   int  $point  user point
	 *
	 * @return  int
	 */
	public function getShoppergroupPoint($point)
	{
		$db = $this->db;
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__redshop_user_point_config'));

		$data = $db->setQuery($query)->loadObjectList();
		$arr = array();
		$result = "";

		foreach ($data as $item)
		{
			$arr[$item->shoppergroup_id] = $item->point;
		}

		foreach ($arr as $shopperGroupId => $pointRequire)
		{
			if ($point >= $pointRequire)
			{
				$result = $shopperGroupId;
			}
		}

		return $result;
	}

	/**
	 * This method update user shopper group
	 *
	 * @param   int  $shopper_group  shopper_group id
	 * @param   int  $user_id        user_id
	 *
	 * @return  void
	 */
	public function updateShoppergroup($shopper_group, $user_id)
	{
		$db = $this->db;
		$query = $db->getQuery(true);

		$fields = array($db->qn('shopper_group_id') . ' = ' . $db->q((int) $shopper_group));
		$conditions = array($db->qn('user_id') . ' = ' . $db->q((int) $user_id));

		$query->update($db->qn('#__redshop_users_info'))
			->set($fields)
			->where($conditions);

		return $db->setQuery($query)->execute();
	}
}
