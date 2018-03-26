<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Log helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Log
 * @since       2.1
 *
 */
class ReditemHelperLog
{
	/**
	 * Method for store log data
	 *
	 * @param   string  $logType  Type of log
	 * @param   object  $logData  Array of log data
	 * @param   int     $userId   ID of user whom made log. If null, get current logged in user.
	 *
	 * @return  boolean           True on success. False other wise.
	 */
	public static function storeLog($logType, $logData, $userId = null)
	{
		// Make sure log type and log data not empty
		if (empty($logType) || empty($logData))
		{
			return false;
		}

		$userId = (int) $userId;
		$user   = ReditemHelperSystem::getUser();

		// Check if userId is available
		if ($userId)
		{
			// Get user follow userId
			$user = ReditemHelperSystem::getUser($userId);
		}

		// Check if user is Guest
		if ($user->guest)
		{
			return false;
		}

		$db      = RFactory::getDbo();
		$logData = new JRegistry($logData);

		$columns = array('user_id', 'type', 'target_id', 'data', 'created');
		$values  = array(
			(int) $user->id,
			$db->quote($logType),
			(int) $logData->get('id', 0),
			$db->quote($logData),
			$db->quote(ReditemHelperSystem::getDateWithTimezone()->toSql())
		);

		$query = $db->getQuery(true)
			->insert($db->qn('#__reditem_log_useractivity'))
			->columns($db->qn($columns))
			->values(implode(',', $values));
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method for get log data of an user
	 *
	 * @param   int  $userId  ID of user. If not specific, get from current user
	 *
	 * @return  mixed   Array of data log if success. False otherwise.
	 */
	public static function getLogData($userId = null)
	{
		if (!isset($userId))
		{
			$userId = ReditemHelperSystem::getUser()->id;
		}

		$table = self::getLogTable($userId);

		if (empty($table))
		{
			return false;
		}

		$db = RFactory::getDbo();
		$query = $db->getQuery(true)
			->select('log.*')
			->from($db->qn($table, 'log'));
		$db->setQuery($query);
		$logs = $db->loadObjectList();

		return $logs;
	}
}
